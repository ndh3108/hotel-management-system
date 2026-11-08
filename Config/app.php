<?php

/**
 * Cấu hình ứng dụng - MVC
 */

defined('BASE_PATH') || define('BASE_PATH', dirname(__DIR__));

$config = [
    'name'   => 'Booking Hotel',
    // Port 8080: php -S localhost:8080 router.php | XAMPP: /DA/DoAn-main/Public
    'url'    => (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 8080)
        ? 'http://localhost:8080' : 'http://localhost/doan/Public',
    'debug'  => true,
    'locale' => 'vi',
];

// Định nghĩa constants từ config
defined('APP_NAME') || define('APP_NAME', $config['name']);
defined('APP_URL') || define('APP_URL', $config['url']);
defined('APP_DEBUG') || define('APP_DEBUG', $config['debug']);
defined('APP_LOCALE') || define('APP_LOCALE', $config['locale']);

if (!defined('APP_URL')) {
    define('APP_URL', $config['url']);
}
return $config;

