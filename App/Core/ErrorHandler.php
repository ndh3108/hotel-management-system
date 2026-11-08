<?php

namespace App\Core;

/**
 * Error Handler - Xử lý lỗi và exception
 */
class ErrorHandler
{
    /**
     * Xử lý lỗi HTTP (404, 500, etc.)
     */
    public static function handle(int $code, string $message = ''): void
    {
        $errorView = BASE_PATH . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'Errors' . DIRECTORY_SEPARATOR . $code . '.php';

        if (is_file($errorView)) {
            extract(['message' => $message, 'code' => $code]);
            include $errorView;
            return;
        }

        // Fallback
        self::renderDefault($code, $message);
    }

    /**
     * Xử lý exception
     */
    public static function handleException(\Throwable $e): void
    {
        http_response_code(500);
        self::logError($e);

        $errorView = BASE_PATH . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'Errors' . DIRECTORY_SEPARATOR . '500.php';

        if (is_file($errorView)) {
            $debug = defined('APP_DEBUG') && APP_DEBUG;
            extract([
                'message' => $debug ? $e->getMessage() : 'Đã xảy ra lỗi hệ thống',
                'exception' => $debug ? $e : null,
            ]);
            include $errorView;
            return;
        }

        self::renderDefault(500, $debug ? $e->getMessage() : 'Internal Server Error');
    }

    /**
     * Render lỗi mặc định
     */
    private static function renderDefault(int $code, string $message): void
    {
        $titles = [
            404 => 'Không tìm thấy trang',
            403 => 'Không có quyền truy cập',
            500 => 'Lỗi máy chủ',
        ];

        $title = $titles[$code] ?? "Error {$code}";
        $debug = defined('APP_DEBUG') && APP_DEBUG;

        echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>{$title}</title></head><body>";
        echo "<h1>{$title}</h1>";
        if ($debug && $message) {
            echo "<p>{$message}</p>";
        }
        echo "</body></html>";
    }

    /**
     * Ghi log lỗi
     */
    private static function logError(\Throwable $e): void
    {
        $logFile = BASE_PATH . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'error.log';
        $logDir = dirname($logFile);

        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }

        $message = sprintf(
            "[%s] %s: %s in %s:%d\nStack trace:\n%s\n",
            date('Y-m-d H:i:s'),
            get_class($e),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $e->getTraceAsString()
        );

        @file_put_contents($logFile, $message, FILE_APPEND);
    }
}

