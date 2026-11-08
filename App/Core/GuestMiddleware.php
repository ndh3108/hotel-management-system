<?php

namespace App\Core;

/**
 * Guest Middleware - Chỉ cho phép khi chưa đăng nhập.
 * Đã đăng nhập → redirect theo role: admin → /admin, user → / (hoặc intended).
 */
class GuestMiddleware extends Middleware
{
    public function handle(\Closure $next): void
    {
        if (!Auth::check()) {
            $next();
            return;
        }

        if (Auth::canAccessAdmin()) {
            $target = \App\Core\url('/admin');
        } else {
            $target = \App\Core\url('/');
        }
        header('Location: ' . $target);
        exit;
    }
}

