<?php

/**
 * Bootstrap - Load cấu hình, Composer autoload và autoload MVC
 * Gọi từ Public/index.php và Public/admin.php
 */
defined('BASE_PATH') || define('BASE_PATH', dirname(__DIR__));
define('BASE_URL', '/doan/public/');

// Composer autoload (dompdf, v.v.)
$composerAutoload = BASE_PATH . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
if (is_file($composerAutoload)) {
    require $composerAutoload;
}

// APP_URL từ config/app.php (fallback nếu cần)
if (!defined('APP_URL')) {
    define('APP_URL', 'http://localhost/doan/public');
}

// Autoload class theo namespace App\
spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    $baseDir = BASE_PATH . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR;

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';

    if (is_file($file)) {
        require $file;
    }
});

// Load config
require BASE_PATH . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'app.php';

// Load helpers (url, asset, csrf, etc.)
require BASE_PATH . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'Help.php';

// Set error handler
set_exception_handler(function (\Throwable $e) {
    \App\Core\ErrorHandler::handleException($e);
});

// Set error reporting dựa trên APP_DEBUG
error_reporting(defined('APP_DEBUG') && APP_DEBUG ? E_ALL : 0);
ini_set('display_errors', defined('APP_DEBUG') && APP_DEBUG ? '1' : '0');

