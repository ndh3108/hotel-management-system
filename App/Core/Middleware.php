<?php

namespace App\Core;

/**
 * Base Middleware - Tất cả middleware kế thừa từ đây.
 * Tránh việc controller tự check quyền: logic xác thực tập trung tại middleware.
 */
abstract class Middleware
{
    /**
     * Xử lý request. Gọi $next() để chuyển sang middleware/controller tiếp theo.
     * Nếu không gọi $next() (vd: redirect) thì request dừng tại đây.
     *
     * @param \Closure $next function(): void
     */
    abstract public function handle(\Closure $next): void;

    /**
     * Chạy middleware: gọi handle với closure thực thi logic tiếp theo.
     * Dùng khi gọi thủ công (vd: trong admin.php).
     */
    public static function run(\Closure $next, string $middlewareClass): void
    {
        $middleware = new $middlewareClass();
        $middleware->handle($next);
    }
}

