<?php

namespace App\Services;

use App\Core\Database;
use App\Models\User;
use App\Domain\Services\RBAC\AccessControlService;
use App\Domain\Services\LoggerService;
use PDO;

/**
 * AuthService - Handles user authentication and authorization logic.
 */
class AuthService
{
    private User $userModel;
    private AccessControlService $accessControl;

    public function __construct()
    {
        $this->userModel = new User();
        $this->accessControl = new AccessControlService();
    }

    /**
     * Attempt frontend user login
     */
    public function attemptFrontend(string $email, string $password): ?array
    {
        $email = trim($email);
        if ($email === '' || $password === '') {
            return null;
        }

        $userData = $this->userModel->findByEmail($email);
        if (!$userData) {
            LoggerService::info("Login failed: User not found", ['email' => $email]);
            return null;
        }

        $passwordHash = $userData['password_hash'] ?? $userData['password'] ?? '';
        $isMatch = password_verify($password, $passwordHash);
        
        if ($isMatch) {
            LoggerService::info("User logged in successfully", ['user_id' => $userData['id']]);
        } else {
            LoggerService::info("Login failed: Invalid password", ['email' => $email]);
        }

        return $isMatch ? $userData : null;
    }

    /**
     * Attempt admin user login
     */
    public function attemptAdmin(string $email, string $password): ?array
    {
        $userData = $this->userModel->findByEmail($email);
        if (!$userData) {
            LoggerService::info("Admin login failed: User not found", ['email' => $email]);
            return null;
        }

        $passwordHash = $userData['password_hash'] ?? $userData['password'] ?? '';
        if (!password_verify($password, $passwordHash)) {
            LoggerService::info("Admin login failed: Invalid password", ['email' => $email]);
            return null;
        }

        $userId = (int) ($userData['id'] ?? 0);
        if ($this->canAccessAdmin($userId)) {
            LoggerService::info("Admin login successful", ['user_id' => $userId]);
            return $userData;
        }

        LoggerService::info("Admin login failed: Insufficient permissions", ['user_id' => $userId]);
        return null;
    }

    public function getPermissions(int $userId): array
    {
        $sessionCache = $this->accessControl->primeSessionCache($userId);
        return $sessionCache['permissions'] ?? [];
    }

    public function hasPermission(int $userId, string $permission): bool
    {
        return $this->accessControl->hasPermission($userId, $permission);
    }

    public function hasRole(int $userId, string|array $roles): bool
    {
        return $this->accessControl->hasRole($userId, $roles);
    }

    public function canAccessAdmin(int $userId): bool
    {
        return $this->accessControl->canAccessAdmin($userId);
    }
}


