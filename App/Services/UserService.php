<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\AccountActivityLog;
use App\Models\UserSessionLog;
use App\Models\PasswordResetToken;
use App\Domain\Services\LoggerService;

class UserService
{
    private User $userModel;
    private UserProfile $profileModel;
    private AccountActivityLog $activityModel;
    private UserSessionLog $sessionLogModel;
    private PasswordResetToken $resetTokenModel;

    public function __construct()
    {
        $this->userModel = new User();
        $this->profileModel = new UserProfile();
        $this->activityModel = new AccountActivityLog();
        $this->sessionLogModel = new UserSessionLog();
        $this->resetTokenModel = new PasswordResetToken();
    }

    /**
     * Ensure a user profile exists, sync from user data if needed.
     */
    public function ensureProfile(int $userId): void
    {
        if ($userId <= 0) {
            return;
        }

        $userData = $this->userModel->findById($userId);
        if (!$userData) {
            LoggerService::error("Cannot ensure profile: User not found", ['user_id' => $userId]);
            return;
        }

        $userProfile = $this->profileModel->findByUserId($userId);
        if ($userProfile !== null) {
            $this->profileModel->update((int) $userProfile['id'], [
                'avatar_url' => $userProfile['avatar_url'] ?: ($userData['avatar_url'] ?? null),
                'email_verified_at' => $userProfile['email_verified_at'] ?: ($userData['email_verified_at'] ?? null),
                'phone_verified_at' => $userProfile['phone_verified_at'] ?: ($userData['phone_verified_at'] ?? null),
                'last_login_at' => $userProfile['last_login_at'] ?: ($userData['last_login_at'] ?? null),
                'last_login_ip' => $userProfile['last_login_ip'] ?: ($userData['last_login_ip'] ?? null),
                'profile_completion_percent' => max(
                    (int) ($userProfile['profile_completion_percent'] ?? 0),
                    (int) ($userData['profile_completion_percent'] ?? 0)
                ),
            ]);
            return;
        }

        $this->profileModel->create([
            'user_id' => $userId,
            'avatar_url' => $userData['avatar_url'] ?? null,
            'email_verified_at' => $userData['email_verified_at'] ?? null,
            'phone_verified_at' => $userData['phone_verified_at'] ?? null,
            'last_login_at' => $userData['last_login_at'] ?? null,
            'last_login_ip' => $userData['last_login_ip'] ?? null,
            'profile_completion_percent' => (int) ($userData['profile_completion_percent'] ?? 0),
        ]);
        LoggerService::info("Created new user profile", ['user_id' => $userId]);
    }

    public function getUserWithProfile(int $userId): ?array
    {
        $userData = $this->userModel->findById($userId);
        if (!$userData) {
            return null;
        }

        $this->ensureProfile($userId);
        $userProfile = $this->profileModel->findByUserId($userId) ?? [];
        if ($userProfile !== []) {
            $userProfile['profile_id'] = $userProfile['id'] ?? null;
            unset($userProfile['id']);
        }

        return array_merge($userData, $userProfile);
    }

    public function updateProfile(int $userId, array $updateData): array
    {
        $userData = $this->userModel->findById($userId);
        if (!$userData) {
            return ['success' => false, 'message' => 'Không tìm thấy người dùng'];
        }

        $fullNameNormalized = trim((string) ($updateData['full_name'] ?? ''));
        $emailNormalized = trim((string) ($updateData['email'] ?? ''));
        $phoneNormalized = trim((string) ($updateData['phone'] ?? ''));

        if ($fullNameNormalized === '' || $emailNormalized === '') {
            return ['success' => false, 'message' => 'Họ tên và email là bắt buộc'];
        }

        $userByEmail = $this->userModel->findByEmail($emailNormalized);
        if ($userByEmail && (int) ($userByEmail['id'] ?? 0) !== $userId) {
            return ['success' => false, 'message' => 'Email đã được sử dụng'];
        }

        if ($phoneNormalized !== '') {
            $stmtCheckPhone = $this->userModel->getConnection()->prepare("SELECT id FROM users WHERE phone = :phone AND deleted_at IS NULL LIMIT 1");
            $stmtCheckPhone->execute(['phone' => $phoneNormalized]);
            $userByPhone = $stmtCheckPhone->fetch(\PDO::FETCH_ASSOC) ?: null;
            if ($userByPhone && (int) ($userByPhone['id'] ?? 0) !== $userId) {
                return ['success' => false, 'message' => 'Số điện thoại đã được sử dụng'];
            }
        }

        $isUserUpdateSuccessful = $this->userModel->update($userId, [
            'full_name' => $fullNameNormalized,
            'email' => $emailNormalized,
            'phone' => $phoneNormalized !== '' ? $phoneNormalized : null,
            'avatar_url' => (string) ($updateData['avatar_url'] ?? ($userData['avatar_url'] ?? '')) ?: null,
        ]);

        $this->ensureProfile($userId);
        $currentUserProfile = $this->profileModel->findByUserId($userId);
        
        $calculatedCompletion = $this->calculateProfileCompletion([
            'full_name' => $fullNameNormalized,
            'email' => $emailNormalized,
            'phone' => $phoneNormalized,
            'address_line' => $updateData['address_line'] ?? '',
            'city' => $updateData['city'] ?? '',
            'avatar_url' => $updateData['avatar_url'] ?? ($currentUserProfile['avatar_url'] ?? ''),
        ]);

        $profilePayload = [
            'address_line' => trim((string) ($updateData['address_line'] ?? '')),
            'ward' => trim((string) ($updateData['ward'] ?? '')),
            'district' => trim((string) ($updateData['district'] ?? '')),
            'city' => trim((string) ($updateData['city'] ?? '')),
            'country' => trim((string) ($updateData['country'] ?? 'Việt Nam')),
            'postal_code' => trim((string) ($updateData['postal_code'] ?? '')),
            'admin_note' => (string) ($updateData['admin_note'] ?? ($currentUserProfile['admin_note'] ?? '')),
            'avatar_url' => (string) ($updateData['avatar_url'] ?? ($currentUserProfile['avatar_url'] ?? '')),
            'profile_completion_percent' => $calculatedCompletion,
        ];

        $isProfileUpdateSuccessful = $currentUserProfile
            ? $this->profileModel->update((int) $currentUserProfile['id'], $profilePayload)
            : (bool) $this->profileModel->create(array_merge(['user_id' => $userId], $profilePayload));

        if ($isUserUpdateSuccessful && $isProfileUpdateSuccessful) {
            $this->userModel->update($userId, [
                'avatar_url' => $profilePayload['avatar_url'] !== '' ? $profilePayload['avatar_url'] : null,
                'profile_completion_percent' => $calculatedCompletion,
            ]);
            $this->logActivity($userId, 'PROFILE_UPDATED', 'Cập nhật thông tin hồ sơ');
            LoggerService::info("User profile updated", ['user_id' => $userId]);
            return ['success' => true];
        }

        LoggerService::error("User profile update failed", ['user_id' => $userId]);
        return ['success' => false, 'message' => 'Cập nhật hồ sơ thất bại'];
    }

    public function updateAdminNote(int $userId, string $adminNoteText): bool
    {
        $this->ensureProfile($userId);
        $userProfile = $this->profileModel->findByUserId($userId);
        if (!$userProfile) {
            return false;
        }

        $result = $this->profileModel->update((int) $userProfile['id'], [
            'admin_note' => $adminNoteText,
        ]);
        
        if ($result) {
            LoggerService::info("Admin note updated", ['user_id' => $userId]);
        }
        
        return $result;
    }

    public function uploadAvatar(int $userId, array $fileInfo): array
    {
        if (($fileInfo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Upload avatar thất bại'];
        }

        $allowedMimeTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
        $actualMimeType = mime_content_type($fileInfo['tmp_name']) ?: '';
        if (!isset($allowedMimeTypes[$actualMimeType])) {
            return ['success' => false, 'message' => 'Định dạng ảnh không hợp lệ'];
        }

        $uploadDirectory = BASE_PATH . DIRECTORY_SEPARATOR . 'Public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'avatars';
        if (!is_dir($uploadDirectory)) {
            mkdir($uploadDirectory, 0777, true);
        }

        $targetFilename = 'avatar_' . $userId . '_' . time() . '.' . $allowedMimeTypes[$actualMimeType];
        $fileDestination = $uploadDirectory . DIRECTORY_SEPARATOR . $targetFilename;

        if (!move_uploaded_file($fileInfo['tmp_name'], $fileDestination)) {
            LoggerService::error("Failed to move uploaded file", ['user_id' => $userId, 'dest' => $fileDestination]);
            return ['success' => false, 'message' => 'Không thể lưu avatar'];
        }

        $uploadedAvatarUrl = \App\Core\url('/uploads/avatars/' . $targetFilename);
        $updateResult = $this->updateProfile($userId, ['avatar_url' => $uploadedAvatarUrl] + ($this->getUserWithProfile($userId) ?? []));
        if (!$updateResult['success']) {
            return $updateResult;
        }

        $this->logActivity($userId, 'AVATAR_UPDATED', 'Cập nhật ảnh đại diện');
        LoggerService::info("Avatar uploaded and updated", ['user_id' => $userId, 'url' => $uploadedAvatarUrl]);
        return ['success' => true, 'avatar_url' => $uploadedAvatarUrl];
    }

    public function markEmailVerified(int $userId): bool
    {
        return $this->markVerifiedAt($userId, 'email_verified_at', 'EMAIL_VERIFIED', 'Đã xác minh email');
    }

    public function markPhoneVerified(int $userId): bool
    {
        return $this->markVerifiedAt($userId, 'phone_verified_at', 'PHONE_VERIFIED', 'Đã xác minh số điện thoại');
    }

    public function changePassword(int $userId, string $currentPasswordRaw, string $newPasswordRaw): array
    {
        $userData = $this->userModel->findById($userId);
        if (!$userData) {
            return ['success' => false, 'message' => 'Không tìm thấy người dùng'];
        }

        $storedPasswordHash = (string) ($userData['password_hash'] ?? $userData['password'] ?? '');
        if ($storedPasswordHash === '' || !password_verify($currentPasswordRaw, $storedPasswordHash)) {
            return ['success' => false, 'message' => 'Mật khẩu hiện tại không chính xác'];
        }

        if (strlen($newPasswordRaw) < 6) {
            return ['success' => false, 'message' => 'Mật khẩu mới phải từ 6 ký tự'];
        }

        $recentLogs = $this->activityModel->getRecentByUserId($userId, 5);
        $currentTime = time();
        $recentPasswordChangeAttempts = 0;
        foreach ($recentLogs as $logEntry) {
            if (($logEntry['action'] ?? '') !== 'PASSWORD_CHANGED') {
                continue;
            }
            $createdAtTimestamp = strtotime((string) ($logEntry['created_at'] ?? ''));
            if ($createdAtTimestamp && ($currentTime - $createdAtTimestamp) <= 600) {
                $recentPasswordChangeAttempts++;
            }
        }
        if ($recentPasswordChangeAttempts >= 3) {
            LoggerService::warning("Rapid password change attempts detected", ['user_id' => $userId]);
            return ['success' => false, 'message' => 'Bạn đang đổi mật khẩu quá nhanh, vui lòng thử lại sau'];
        }

        $isUpdateSuccessful = $this->userModel->update($userId, [
            'password_hash' => password_hash($newPasswordRaw, PASSWORD_DEFAULT),
        ]);

        if ($isUpdateSuccessful) {
            $this->logActivity($userId, 'PASSWORD_CHANGED', 'Đổi mật khẩu tài khoản');
            LoggerService::info("Password changed successfully", ['user_id' => $userId]);
            return ['success' => true];
        }

        LoggerService::error("Password change failed", ['user_id' => $userId]);
        return ['success' => false, 'message' => 'Đổi mật khẩu thất bại'];
    }

    public function recordLogin(int $userId): void
    {
        $this->ensureProfile($userId);
        $userProfile = $this->profileModel->findByUserId($userId);
        $clientIp = $_SERVER['REMOTE_ADDR'] ?? null;
        $currentSessionId = session_id();
        $timestampString = date('Y-m-d H:i:s');

        if ($userProfile) {
            $this->profileModel->update((int) $userProfile['id'], [
                'last_login_at' => $timestampString,
                'last_login_ip' => $clientIp,
            ]);
        }

        $this->userModel->updateExistingFields($userId, [
            'last_login_at' => $timestampString,
            'last_login_ip' => $clientIp,
            'failed_login_attempts' => 0,
            'locked_until' => null,
        ]);

        if ($currentSessionId !== '') {
            $activeSession = $this->sessionLogModel->findActiveBySessionId($currentSessionId);
            if ($activeSession) {
                $this->sessionLogModel->update((int) $activeSession['id'], [
                    'last_activity_at' => $timestampString,
                    'is_current' => 1,
                ]);
            } else {
                $this->sessionLogModel->create([
                    'user_id' => $userId,
                    'session_id' => $currentSessionId,
                    'ip_address' => $clientIp,
                    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
                    'last_activity_at' => $timestampString,
                    'is_current' => 1,
                ]);
            }
        }

        LoggerService::info("Login recorded", ['user_id' => $userId, 'ip' => $clientIp]);
        $this->logActivity($userId, 'LOGIN_SUCCESS', 'Đăng nhập thành công');
    }

    public function recordLogout(int $userId): void
    {
        $currentSessionId = session_id();
        if ($currentSessionId !== '') {
            $activeSession = $this->sessionLogModel->findActiveBySessionId($currentSessionId);
            if ($activeSession) {
                $this->sessionLogModel->update((int) $activeSession['id'], [
                    'logged_out_at' => date('Y-m-d H:i:s'),
                    'is_current' => 0,
                    'last_activity_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }
        LoggerService::info("Logout recorded", ['user_id' => $userId]);
        $this->logActivity($userId, 'LOGOUT', 'Đăng xuất khỏi hệ thống');
    }

    public function isCurrentSessionActive(int $userId): bool
    {
        $currentSessionId = session_id();
        if ($userId <= 0 || $currentSessionId === '') {
            return false;
        }

        $latestSession = $this->sessionLogModel->findLatestBySessionId($currentSessionId);
        if (!$latestSession) {
            return true;
        }

        if ((int) ($latestSession['user_id'] ?? 0) !== $userId) {
            return false;
        }

        return empty($latestSession['logged_out_at']);
    }

    public function touchCurrentSession(int $userId): void
    {
        $currentSessionId = session_id();
        if ($userId <= 0 || $currentSessionId === '') {
            return;
        }

        $activeSession = $this->sessionLogModel->findActiveBySessionId($currentSessionId);
        if ($activeSession) {
            $this->sessionLogModel->update((int) $activeSession['id'], [
                'last_activity_at' => date('Y-m-d H:i:s'),
                'is_current' => 1,
            ]);
            return;
        }

        $this->sessionLogModel->create([
            'user_id' => $userId,
            'session_id' => $currentSessionId,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            'last_activity_at' => date('Y-m-d H:i:s'),
            'is_current' => 1,
        ]);
    }

    public function logoutOtherSessions(int $userId): int
    {
        $currentSessionId = session_id();
        if ($userId <= 0 || $currentSessionId === '') {
            return 0;
        }

        $stmtLogoutOthers = $this->sessionLogModel->getConnection()->prepare("
            UPDATE user_session_logs
            SET logged_out_at = NOW(),
                is_current = 0,
                last_activity_at = NOW()
            WHERE user_id = :user_id
              AND session_id <> :session_id
              AND logged_out_at IS NULL
        ");
        $stmtLogoutOthers->execute([
            'user_id' => $userId,
            'session_id' => $currentSessionId,
        ]);

        $affectedSessionCount = $stmtLogoutOthers->rowCount();
        if ($affectedSessionCount > 0) {
            $this->logActivity($userId, 'LOGOUT_OTHER_SESSIONS', 'Đăng xuất các phiên đăng nhập khác');
            LoggerService::info("Other sessions terminated", ['user_id' => $userId, 'count' => $affectedSessionCount]);
        }
        return $affectedSessionCount;
    }

    public function getRecentSessions(int $userId, int $resultLimit = 10): array
    {
        return $this->sessionLogModel->getRecentByUserId($userId, $resultLimit);
    }

    public function getRecentActivities(int $userId, int $resultLimit = 10): array
    {
        return $this->activityModel->getRecentByUserId($userId, $resultLimit);
    }

    public function createPasswordReset(string $targetEmail): ?string
    {
        $userData = $this->userModel->findByEmail(trim($targetEmail));
        if (!$userData) {
            return null;
        }

        $resetTokenString = bin2hex(random_bytes(32));
        $this->resetTokenModel->create([
            'user_id' => (int) $userData['id'],
            'token' => $resetTokenString,
            'expires_at' => date('Y-m-d H:i:s', time() + 3600),
        ]);

        LoggerService::info("Password reset token created", ['user_id' => $userData['id']]);
        $this->logActivity((int) $userData['id'], 'PASSWORD_RESET_REQUESTED', 'Yêu cầu đặt lại mật khẩu');
        return $resetTokenString;
    }

    public function resetPasswordByToken(string $tokenString, string $requestedNewPassword): array
    {
        if (strlen($requestedNewPassword) < 6) {
            return ['success' => false, 'message' => 'Mật khẩu mới phải từ 6 ký tự'];
        }

        $validTokenData = $this->resetTokenModel->findValidToken($tokenString);
        if (!$validTokenData) {
            return ['success' => false, 'message' => 'Token đặt lại mật khẩu không hợp lệ hoặc đã hết hạn'];
        }

        $userIdFromToken = (int) ($validTokenData['user_id'] ?? 0);
        $isUpdateSuccessful = $this->userModel->update($userIdFromToken, [
            'password_hash' => password_hash($requestedNewPassword, PASSWORD_DEFAULT),
        ]);
        if (!$isUpdateSuccessful) {
            return ['success' => false, 'message' => 'Không thể cập nhật mật khẩu'];
        }

        $this->resetTokenModel->update((int) $validTokenData['id'], [
            'used_at' => date('Y-m-d H:i:s'),
        ]);

        LoggerService::info("Password reset completed", ['user_id' => $userIdFromToken]);
        $this->logActivity($userIdFromToken, 'PASSWORD_RESET_COMPLETED', 'Hoàn tất đặt lại mật khẩu');
        return ['success' => true];
    }

    public function logCustomActivity(int $userId, string $actionName, string $activityDescription): void
    {
        if ($userId <= 0) {
            return;
        }
        $this->logActivity($userId, $actionName, $activityDescription);
    }

    private function markVerifiedAt(int $userId, string $fieldName, string $logAction, string $logDescription): bool
    {
        $this->ensureProfile($userId);
        $userProfile = $this->profileModel->findByUserId($userId);
        if (!$userProfile) {
            return false;
        }

        $timestampNow = date('Y-m-d H:i:s');
        $isProfileUpdateSuccessful = $this->profileModel->update((int) $userProfile['id'], [
            $fieldName => $timestampNow,
        ]);
        if ($isProfileUpdateSuccessful) {
            $this->userModel->update($userId, [
                $fieldName => $timestampNow,
            ]);
            LoggerService::info("Field verified", ['user_id' => $userId, 'field' => $fieldName]);
            $this->logActivity($userId, $logAction, $logDescription);
        }
        return $isProfileUpdateSuccessful;
    }

    private function calculateProfileCompletion(array $profileDataSubset): int
    {
        $evaluationFields = [
            trim((string) ($profileDataSubset['full_name'] ?? '')),
            trim((string) ($profileDataSubset['email'] ?? '')),
            trim((string) ($profileDataSubset['phone'] ?? '')),
            trim((string) ($profileDataSubset['address_line'] ?? '')),
            trim((string) ($profileDataSubset['city'] ?? '')),
            trim((string) ($profileDataSubset['avatar_url'] ?? '')),
        ];

        $populatedFieldCount = 0;
        foreach ($evaluationFields as $fieldValue) {
            if ($fieldValue !== '') {
                $populatedFieldCount++;
            }
        }

        return (int) round(($populatedFieldCount / max(1, count($evaluationFields))) * 100);
    }

    private function logActivity(int $userId, string $actionType, string $activitySummary): void
    {
        $this->activityModel->create([
            'user_id' => $userId,
            'action' => $actionType,
            'description' => $activitySummary,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}


