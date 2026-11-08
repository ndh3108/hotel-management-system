<?php

namespace App\Core;

/**
 * Auth Middleware - Yêu cầu user đã đăng nhập.
 * Nếu chưa đăng nhập → redirect về trang login.
 * Dùng cho các route: profile, booking, ...
 */
class AuthMiddleware extends Middleware
{
    protected string $loginUrl = '/login';

    private static function validateActiveSessionOrLogout(bool $isApi = false): bool
    {
        $userId = Auth::id();
        if (!$userId) {
            return false;
        }

        $accountService = new \App\Services\UserService();
        if (!$accountService->isCurrentSessionActive((int) $userId)) {
            Auth::logout();
            if ($isApi) {
                http_response_code(401);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['success' => false, 'message' => 'Session expired or logged out from another device']);
                exit;
            }

            Session::flash('_error', 'Phiên đăng nhập đã hết hiệu lực.');
            header('Location: ' . \App\Core\url('/login'));
            exit;
        }

        $accountService->touchCurrentSession((int) $userId);
        return true;
    }

    /**
     * Kiểm tra đăng nhập - gọi trong Controller khi cần bảo vệ thủ công.
     * Nếu chưa đăng nhập → redirect về /login và thoát.
     */
    public static function requireAuth(): void
    {
        if (Auth::check()) {
            self::validateActiveSessionOrLogout(false);
            return;
        }
        Session::flash('_intended', $_SERVER['REQUEST_URI'] ?? '/');
        header('Location: ' . \App\Core\url('/login'));
        exit;
    }
    /**
     * Lấy JWT token từ header Authorization
     */
    private static function getBearerToken(): ?string
    {
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { // Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }

    /**
     * Check if current request has a specific permission
     */
    public static function requirePermission(string $permissionCode): void
    {
        $accessControl = new \App\Domain\Services\RBAC\AccessControlService();
        $token = self::getBearerToken();
        $isApi = strpos($_SERVER['REQUEST_URI'] ?? '', '/api/') === 0 || $token;

        if ($token) {
            $payload = \App\Core\JWT::decode($token);
            if ($payload && $accessControl->hasPermissionFromJwtPayload($payload, $permissionCode)) {
                return;
            }
            // Invalid JWT or missing permission (deny-by-default)
            http_response_code(403);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'Access Denied: Missing permission ' . $permissionCode]);
            exit;
        }

        // Fallback or traditional session auth
        if (Auth::check()) {
            self::validateActiveSessionOrLogout($isApi);
            if (Auth::hasPermission($permissionCode)) {
                return; // Has permission
            }

            // No permission via session
            if ($isApi) {
                http_response_code(403);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['success' => false, 'message' => 'Access Denied: Missing permission ' . $permissionCode]);
                exit;
            } else {
                Session::flash('_error', 'Access Denied.');
                header('Location: ' . \App\Core\url('/'));
                exit;
            }
        }

        // Not authenticated
        if ($isApi) {
            http_response_code(401);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        Session::flash('_intended', $_SERVER['REQUEST_URI'] ?? '/');
        header('Location: ' . \App\Core\url('/login'));
        exit;
    }

    public function handle(\Closure $next): void
    {
        $token = self::getBearerToken();
        $isApi = strpos($_SERVER['REQUEST_URI'] ?? '', '/api/') === 0 || $token;

        if ($token) {
            $payload = \App\Core\JWT::decode($token);
            if ($payload) {
                $next();
                return;
            }
            http_response_code(401);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'Unauthorized or invalid token']);
            exit;
        }

        if (Auth::check()) {
            self::validateActiveSessionOrLogout($isApi);
            $next();
            return;
        }

        if ($isApi) {
            http_response_code(401);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        Session::flash('_intended', $this->getCurrentUrl());
        $url = defined('APP_URL') ? rtrim(APP_URL, '/') . $this->loginUrl : $this->loginUrl;
        header('Location: ' . $url);
        exit;
    }

    protected function getCurrentUrl(): string
    {
        return $_SERVER['REQUEST_URI'] ?? '/';
    }
}

