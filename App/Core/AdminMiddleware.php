<?php

namespace App\Core;

/**
 * Admin Middleware - Chỉ cho phép role admin.
 * Guest → redirect /login + lưu intended.
 * User (role !== admin) → 403 Forbidden.
 */
class AdminMiddleware extends Middleware
{
    protected string $loginUrl = '/login';

    public function handle(\Closure $next): void
    {
        if (!Auth::check()) {
            Session::flash('_intended', $_SERVER['REQUEST_URI'] ?? '/admin');
            header('Location: ' . \App\Core\url($this->loginUrl));
            exit;
        }

        if (Auth::canAccessAdmin()) {
            $next();
            return;
        }

        http_response_code(403);
        $basePath = BASE_PATH . '/App/Views';
        $viewFile = $basePath . '/errors/403.php';
        
        if (is_file($viewFile)) {
            include $viewFile;
            exit;
        }

        echo "<div style='text-align:center; padding: 50px; font-family: sans-serif;'>
            <h1 style='color:#e53e3e;'>403 - Access Denied</h1>
            <p>Bạn không có quyền truy cập trang quản trị này do thiếu quyền hạn hệ thống.</p>
            <a href='" . \App\Core\url('/') . "' style='display:inline-block; margin-top:20px; padding: 10px 20px; background:#4f46e5; color:#fff; text-decoration:none; border-radius:5px;'>Về trang chủ</a>
        </div>";
        exit;
    }
}

