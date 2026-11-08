# Middleware & Auth

## Nguyên tắc

- **Controller không tự check quyền.** Toàn bộ kiểm tra đăng nhập / phân quyền thực hiện tại **Middleware**.
- Middleware chạy trước khi request tới controller; nếu không cho phép thì redirect và dừng.

## Các Middleware

| Middleware          | Mục đích                           | Dùng cho                         |
| ------------------- | ---------------------------------- | -------------------------------- |
| **AuthMiddleware**  | Yêu cầu user frontend đã đăng nhập | /profile, /booking, ...          |
| **AdminMiddleware** | Yêu cầu admin đã đăng nhập         | Toàn bộ admin (trừ login/logout) |
| **GuestMiddleware** | Chỉ cho phép khi chưa đăng nhập    | /login, /register                |

## Auth (hai guard)

- **Frontend (user):** `Auth::setUser()`, `Auth::user()`, `Auth::check()`, `Auth::logout()`, `Auth::guest()`
- **Admin:** `Auth::setAdmin()`, `Auth::admin()`, `Auth::adminCheck()`, `Auth::adminLogout()`

Session key: `user` (frontend), `admin_user` (admin). Không dùng chung.

## Cách dùng

### Frontend (Public/index.php)

```php
// Công khai
Router::get('/', HomeController::class, 'index');

// Chỉ guest (đã login thì redirect home)
Router::get('/login', AuthController::class, 'loginForm', [GuestMiddleware::class]);
Router::post('/login', AuthController::class, 'login', [GuestMiddleware::class]);

// Yêu cầu đăng nhập
Router::get('/profile', ProfileController::class, 'index', [AuthMiddleware::class]);
Router::get('/booking', BookingController::class, 'form', [AuthMiddleware::class]);
```

### Admin (Public/admin.php)

- Mọi request với `page !== 'auth'` đều chạy **AdminMiddleware**.
- Trang `page=auth` (login form, login POST, logout) không qua middleware.
- Không cần gắn middleware trong từng controller admin.

### Thêm middleware mới

1. Tạo class kế thừa `App\Core\Middleware`.
2. Implement `handle(\Closure $next): void` — gọi `$next()` để tiếp tục, hoặc redirect và không gọi `$next()`.

```php
class MyMiddleware extends Middleware
{
    public function handle(\Closure $next): void
    {
        if (/* điều kiện */) {
            $next();
            return;
        }
        header('Location: /forbidden');
        exit;
    }
}
```

3. Gắn vào route: `Router::get('/path', Controller::class, 'method', [MyMiddleware::class]);`

## Intended URL (AuthMiddleware)

- Khi user chưa login truy cập route có AuthMiddleware → redirect về `/login` và lưu URL hiện tại vào `Session::flash('_intended')`.
- Sau khi login thành công, có thể redirect về `Session::getFlash('_intended')` thay vì luôn về trang chủ.
