<?php

namespace App\Core;

/**
 * Auth - Xác thực thống nhất: 1 session (user), phân quyền qua role_id.
 * role_id=1 => admin, còn lại => user.
 * Controller dùng AuthMiddleware / AdminMiddleware để bảo vệ route.
 */
class Auth
{
    private const USER_KEY = 'user';

    public static function setUser(array $user): void
    {
        Session::set(self::USER_KEY, $user);
    }

    public static function user(): ?array
    {
        return Session::get(self::USER_KEY);
    }

    /** Đã đăng nhập */
    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function logout(): void
    {
        Session::forget(self::USER_KEY);
        Session::forget('permissions');
        Session::forget('roles');
        Session::forget('permission_version');
        Session::forget('rbac_cache');
    }

    /** Chưa đăng nhập (guest) */
    public static function guest(): bool
    {
        return !self::check();
    }

    /** ID người dùng hiện tại */
    public static function id(): ?int
    {
        $u = self::user();
        return ($u !== null && isset($u['id'])) ? (int) $u['id'] : null;
    }

    /** Role: 'admin' (role_id=1) hoặc 'user' */
    public static function role(): ?string
    {
        if (!self::check()) {
            return null;
        }
        return self::canAccessAdmin() ? 'admin' : 'user';
    }

    public static function hasPermission(string $permissionCode): bool
    {
        $userId = self::id();
        if (!$userId) return false;

        $authService = new \App\Services\AuthService();
        return $authService->hasPermission($userId, $permissionCode);
    }

    public static function hasRole(string|array $role): bool
    {
        $userId = self::id();
        if (!$userId) return false;

        $authService = new \App\Services\AuthService();
        return $authService->hasRole($userId, $role);
    }

    public static function canAccessAdmin(): bool
    {
        $userId = self::id();
        if (!$userId) {
            return false;
        }

        $authService = new \App\Services\AuthService();
        return $authService->canAccessAdmin($userId);
    }
}

