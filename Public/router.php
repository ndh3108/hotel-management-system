<?php
/**
 * Router cho PHP built-in server (port 8080)
 * - Phục vụ file tĩnh từ Public/ (assets, test.php)
 * - Chuyển mọi request khác về Public/index.php
 */
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH));
$publicDir = __DIR__ . DIRECTORY_SEPARATOR . 'Public';
$file = $publicDir . ($uri === '/' ? '' : $uri);

// Phục vụ file tĩnh (CSS, JS, images, test.php, v.v.)
if ($uri !== '/' && $uri !== '' && file_exists($file) && is_file($file)) {
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $mimes = [
        'css' => 'text/css; charset=utf-8',
        'js' => 'application/javascript; charset=utf-8',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'ico' => 'image/x-icon',
        'svg' => 'image/svg+xml',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf' => 'font/ttf',
    ];
    if (isset($mimes[$ext])) {
        header('Content-Type: ' . $mimes[$ext]);
    }
    header('Content-Length: ' . filesize($file));
    readfile($file);
    exit;
}

// Chuyển request sang index.php
require $publicDir . DIRECTORY_SEPARATOR . 'index.php';

