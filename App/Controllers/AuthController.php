<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Session;
use App\Domain\Services\RBAC\AccessControlService;
use App\Services\UserService;
use App\Models\User;
use function App\Core\url;

/**
 * AuthController - Đăng nhập/Đăng ký frontend.
 * Trang login/register dùng GuestMiddleware (đã login thì redirect về home).
 */
class AuthController extends Controller
{
    private User $userModel;
    private UserService $userService;

    public function __construct()
    {
        parent::__construct();
        $this->viewPath = BASE_PATH . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Views';
        $this->userModel = new User();
        $this->userService = new UserService();
    }

    /** Redirect /admin/login → /login (trang đăng nhập thống nhất) */
    public function redirectToLogin(): void
    {
        $this->redirect(url('/login'));
    }

    /** GET: Form đăng nhập */
    public function loginForm(): void
    {
        $this->useLayout = false;
        $this->render('Auth/login_clean', [
            'title' => 'Đăng nhập - Booking Hotel',
            'error' => $_GET['error'] ?? null,
        ]);
    }

    /** POST: Xử lý đăng nhập */
    public function login(): void
    {
        if (!$this->isPost()) {
            $this->redirect(url('/login'));
            return;
        }

        // Support standard POST form and raw JSON POST
        $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
        if (strpos($contentType, 'application/json') !== false) {
            $content = trim(file_get_contents("php://input"));
            $decoded = json_decode($content, true);
            $email = trim((string) ($decoded['email'] ?? ''));
            $password = (string) ($decoded['password'] ?? '');
            $isApiRequest = true;
        } else {
            $email = trim((string) $this->input('email'));
            $password = (string) $this->input('password');
            $isApiRequest = false;
        }

        if ($email === '' || $password === '') {
            if ($isApiRequest) {
                $this->json(['success' => false, 'message' => 'Email and password are required'], 400);
            } else {
                $this->redirect(url('/login?error=empty'));
            }
            return;
        }

        $found = $this->userModel->findByEmail($email);
        $passwordHash = $found['password_hash'] ?? $found['password'] ?? ''; 

        if ($found && !empty($found['locked_until']) && strtotime($found['locked_until']) > time()) {
            $minutes = ceil((strtotime($found['locked_until']) - time()) / 60);
            if ($isApiRequest) {
                $this->json(['success' => false, 'message' => "Account is locked. Try again in $minutes minutes."], 403);
            } else {
                $this->redirect(url('/login?error=locked&email=' . urlencode($email)));
            }
            return;
        }

        if ($found && password_verify($password, $passwordHash)) {
            // Reset fail attempts
            $this->userModel->updateExistingFields((int) $found['id'], [
                'failed_login_attempts' => 0,
                'locked_until' => null
            ]);
            
            Auth::setUser($found);
            $intended = Session::getFlash('_intended');
            
            // Load permissions theo RBACService trung tâm (có cache/version).
            $accessControl = new AccessControlService();
            $cache = $accessControl->primeSessionCache((int)$found['id']);
            $permissions = $cache['permissions'] ?? [];
            $permissionVersion = (int)($cache['permission_version'] ?? 1);
            $roleCodes = $cache['role_codes'] ?? [];

            // Tạo JWT Token
            $payload = [
                'userId' => $found['id'],
                'email' => $found['email'],
                'role_id' => $found['role_id'],
                'permissions' => $permissions,
                'roles' => $roleCodes,
                'permission_version' => $permissionVersion,
                'iat' => time(),
                'exp' => time() + (86400 * 7) // 7 days expiration
            ];
            $jwt = \App\Core\JWT::encode($payload);
            $this->userService->recordLogin((int) $found['id']);
            $freshUser = $this->userService->getUserWithProfile((int) $found['id']);
            if ($freshUser) {
                Auth::setUser($freshUser);
            }

            if ($isApiRequest) {
                $this->json([
                    'success' => true,
                    'message' => 'Login successful',
                    'token' => $jwt,
                    'user' => [
                        'id' => $found['id'],
                        'full_name' => $found['full_name'],
                        'email' => $found['email'],
                        'role_id' => $found['role_id'],
                        'permissions' => $permissions
                    ]
                ]);
                return;
            }

            // Phân quyền redirect sau khi đăng nhập (Truyền thống)
            if (Auth::canAccessAdmin()) {
                $target = ($intended && strpos($intended, 'admin') !== false)
                    ? ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . $intended)
                    : url('/admin');
                $this->redirect($target);
            } else {
                $target = $intended
                    ? ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . $intended)
                    : url('/');
                $this->redirect($target);
            }
            return;
        }

        if ($found) {
            $attempts = ($found['failed_login_attempts'] ?? 0) + 1;
            $updateData = ['failed_login_attempts' => $attempts];
            if ($attempts >= 5) {
                $updateData['locked_until'] = date('Y-m-d H:i:s', time() + 30 * 60);
            }
            $this->userModel->updateExistingFields((int) $found['id'], $updateData);
        }

        if ($isApiRequest) {
            $this->json(['success' => false, 'message' => 'Invalid email or password'], 401);
        } else {
            $this->redirect(url('/login?error=invalid&email=' . urlencode($email)));
        }
    }

    /** GET: Form đăng ký */
    public function registerForm(): void
    {
        $this->useLayout = false;
        $this->render('Auth/register', [
            'title' => 'Đăng ký - Booking Hotel',
            'error' => $_GET['error'] ?? null,
        ]);
    }

    /** POST: Xử lý đăng ký */
    public function register(): void
    {
        if (!$this->isPost()) {
            $this->redirect(url('/register'));
            return;
        }
        $fullName = trim((string) $this->input('full_name'));
        $email = trim((string) $this->input('email'));
        $phone = trim((string) $this->input('phone'));
        $password = (string) $this->input('password');
        $passwordConfirm = (string) $this->input('password_confirm');

        $errors = [];
        if ($fullName === '') {
            $errors[] = 'full_name';
        }
        if ($email === '') {
            $errors[] = 'email';
        } elseif ($this->userModel->findByEmail($email)) {
            $errors[] = 'email_exists';
        }
        if (strlen($password) < 6) {
            $errors[] = 'password_short';
        }
        if ($password !== $passwordConfirm) {
            $errors[] = 'password_mismatch';
        }

        if (!empty($errors)) {
            Session::flash('_old.full_name', $fullName);
            Session::flash('_old.email', $email);
            Session::flash('_old.phone', $phone);
            $query = 'error=' . urlencode(implode(',', $errors));
            $this->redirect(url('/register?' . $query));
            return;
        }

        $userId = $this->userModel->create([
            'role_id' => 3,
            'full_name' => $fullName,
            'email' => $email,
            'phone' => $phone ?: null,
            'password_hash' => password_hash($password, PASSWORD_BCRYPT),
            'status' => 1,
        ]);

        if ($userId) {
            $this->userService->ensureProfile((int) $userId);
            $this->userService->recordLogin((int) $userId);
            $user = $this->userService->getUserWithProfile((int) $userId) ?? $this->userModel->findById($userId);
            if ($user) {
                Auth::setUser($user);
            }
            $this->redirect(url('/'));
            return;
        }

        $this->redirect(url('/register?error=failed'));
    }

    /** POST/GET: Đăng xuất */
    public function logout(): void
    {
        $userId = \App\Core\Auth::id();
        if ($userId) {
            $this->userService->recordLogout((int) $userId);
        }
        Auth::logout();
        $this->redirect(url('/'));
    }

    public function forgotPasswordForm(): void
    {
        $this->useLayout = false;
        $this->render('Auth/forgot-password', [
            'title' => 'Quên mật khẩu',
        ]);
    }

    public function forgotPassword(): void
    {
        $email = trim((string) $this->input('email'));
        if ($email === '') {
            $this->redirect(url('/forgot-password?error=empty'));
            return;
        }

        $token = $this->userService->createPasswordReset($email);
        if ($token !== null && defined('APP_DEBUG') && APP_DEBUG) {
            Session::flash('_password_reset_link', url('/reset-password?token=' . urlencode($token)));
        }

        $this->redirect(url('/forgot-password?success=1'));
    }

    public function resetPasswordForm(): void
    {
        $this->useLayout = false;
        $this->render('Auth/reset-password', [
            'title' => 'Đặt lại mật khẩu',
            'token' => (string) ($_GET['token'] ?? ''),
        ]);
    }

    public function resetPassword(): void
    {
        $token = trim((string) $this->input('token'));
        $password = (string) $this->input('password');
        $confirm = (string) $this->input('password_confirm');

        if ($token === '' || $password === '' || $confirm === '') {
            $this->redirect(url('/reset-password?token=' . urlencode($token) . '&error=empty'));
            return;
        }

        if ($password !== $confirm) {
            $this->redirect(url('/reset-password?token=' . urlencode($token) . '&error=mismatch'));
            return;
        }

        $result = $this->userService->resetPasswordByToken($token, $password);
        if (!($result['success'] ?? false)) {
            $this->redirect(url('/reset-password?token=' . urlencode($token) . '&error=invalid'));
            return;
        }

        $this->redirect(url('/login?success=password_reset'));
    }
}

