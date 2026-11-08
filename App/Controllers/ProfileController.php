<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Session;
use App\Models\Booking;
use App\Services\UserService;

/**
 * ProfileController - Trang cá nhân (yêu cầu đăng nhập).
 * Không tự check quyền; route đã gắn AuthMiddleware.
 */
class ProfileController extends Controller
{
    private UserService $userService;

    public function __construct()
    {
        parent::__construct();
        $this->viewPath = BASE_PATH . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Views';
        $this->userService = new UserService();
    }

    public function index(): void
    {
        $current = Auth::user();
        $user = $this->userService->getUserWithProfile((int) ($current['id'] ?? 0)) ?? ($current ?? []);
        $userId = (int)($user['id'] ?? 0);

        $bookingModel = new Booking();

        $bookingCount = 0;
        $reviewCount = 0;
        if ($userId > 0) {
            $bookingCount = count($bookingModel->getByUser($userId));
        }

        $this->render('Profile/index', [
            'user' => $user,
            'bookingCount' => $bookingCount,
            'reviewCount' => $reviewCount,
            'points' => (int)($user['points'] ?? 0),
            'activeTab' => (string)($_GET['tab'] ?? 'profile'),
            'activities' => $this->userService->getRecentActivities($userId, 8),
            'sessions' => $this->userService->getRecentSessions($userId, 8),
            'flashMessage' => Session::getFlash('profile_success'),
            'flashError' => Session::getFlash('profile_error'),
        ]);
    }

    public function update(): void
    {
        $userId = (int) (Auth::id() ?? 0);
        if ($userId <= 0 || !$this->verifyCsrfToken()) {
            Session::flash('profile_error', 'Yêu cầu không hợp lệ.');
            $this->redirect(\App\Core\url('/profile'));
            return;
        }

        $result = $this->userService->updateProfile($userId, [
            'full_name' => (string) $this->input('full_name'),
            'email' => (string) $this->input('email'),
            'phone' => (string) $this->input('phone'),
            'address_line' => (string) $this->input('address_line', ''),
            'ward' => (string) $this->input('ward', ''),
            'district' => (string) $this->input('district', ''),
            'city' => (string) $this->input('city', ''),
            'country' => (string) $this->input('country', 'Việt Nam'),
            'postal_code' => (string) $this->input('postal_code', ''),
        ]);

        if ($result['success'] ?? false) {
            $freshUser = $this->userService->getUserWithProfile($userId);
            if ($freshUser) {
                Auth::setUser($freshUser);
            }
            Session::flash('profile_success', 'Cập nhật thông tin thành công.');
        } else {
            Session::flash('profile_error', (string) ($result['message'] ?? 'Cập nhật thất bại.'));
        }

        $this->redirect(\App\Core\url('/profile'));
    }

    public function uploadAvatar(): void
    {
        $userId = (int) (Auth::id() ?? 0);
        if ($userId <= 0 || !$this->verifyCsrfToken()) {
            Session::flash('profile_error', 'Yêu cầu không hợp lệ.');
            $this->redirect(\App\Core\url('/profile'));
            return;
        }

        $result = $this->userService->uploadAvatar($userId, $_FILES['avatar'] ?? []);
        if ($result['success'] ?? false) {
            $freshUser = $this->userService->getUserWithProfile($userId);
            if ($freshUser) {
                Auth::setUser($freshUser);
            }
            Session::flash('profile_success', 'Cập nhật avatar thành công.');
        } else {
            Session::flash('profile_error', (string) ($result['message'] ?? 'Cập nhật avatar thất bại.'));
        }

        $this->redirect(\App\Core\url('/profile'));
    }

    public function changePassword(): void
    {
        $userId = (int) (Auth::id() ?? 0);
        if ($userId <= 0 || !$this->verifyCsrfToken()) {
            Session::flash('profile_error', 'Yêu cầu không hợp lệ.');
            $this->redirect(\App\Core\url('/profile?tab=password'));
            return;
        }

        $newPassword = (string) $this->input('new_password');
        $confirm = (string) $this->input('confirm_password');
        if ($newPassword !== $confirm) {
            Session::flash('profile_error', 'Mật khẩu xác nhận không khớp.');
            $this->redirect(\App\Core\url('/profile?tab=password'));
            return;
        }

        $result = $this->userService->changePassword(
            $userId,
            (string) $this->input('current_password'),
            $newPassword
        );

        Session::flash(($result['success'] ?? false) ? 'profile_success' : 'profile_error', (string) (($result['success'] ?? false) ? 'Đổi mật khẩu thành công.' : ($result['message'] ?? 'Đổi mật khẩu thất bại.')));
        $this->redirect(\App\Core\url('/profile?tab=password'));
    }

    public function verifyEmail(): void
    {
        $userId = (int) (Auth::id() ?? 0);
        if ($userId > 0 && $this->verifyCsrfToken() && $this->userService->markEmailVerified($userId)) {
            $freshUser = $this->userService->getUserWithProfile($userId);
            if ($freshUser) {
                Auth::setUser($freshUser);
            }
            Session::flash('profile_success', 'Đã xác minh email.');
        } else {
            Session::flash('profile_error', 'Xác minh email thất bại.');
        }
        $this->redirect(\App\Core\url('/profile?tab=security'));
    }

    public function verifyPhone(): void
    {
        $userId = (int) (Auth::id() ?? 0);
        if ($userId > 0 && $this->verifyCsrfToken() && $this->userService->markPhoneVerified($userId)) {
            $freshUser = $this->userService->getUserWithProfile($userId);
            if ($freshUser) {
                Auth::setUser($freshUser);
            }
            Session::flash('profile_success', 'Đã xác minh số điện thoại.');
        } else {
            Session::flash('profile_error', 'Xác minh số điện thoại thất bại.');
        }
        $this->redirect(\App\Core\url('/profile?tab=security'));
    }

    public function logoutOtherSessions(): void
    {
        $userId = (int) (Auth::id() ?? 0);
        if ($userId <= 0 || !$this->verifyCsrfToken()) {
            Session::flash('profile_error', 'Yêu cầu không hợp lệ.');
            $this->redirect(\App\Core\url('/profile?tab=sessions'));
            return;
        }

        $count = $this->userService->logoutOtherSessions($userId);
        Session::flash('profile_success', $count > 0 ? 'Đã đăng xuất các phiên khác.' : 'Không có phiên nào khác để đăng xuất.');
        $this->redirect(\App\Core\url('/profile?tab=sessions'));
    }
}

