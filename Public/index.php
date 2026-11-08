<?php

session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

/**
 * Entry point Frontend - Bootstrap và Router
 */

require __DIR__ . '/bootstrap.php';

use App\Core\Router;

// Set base path
Router::setBasePath('/doan/public');

// Include route files
require_once BASE_PATH . '/App/Routes/web.php';
require_once BASE_PATH . '/App/Routes/admin.php';
require_once BASE_PATH . '/App/Routes/api.php';

// Pseudo-Cron hook
register_shutdown_function(function () {
    try {
        if (class_exists('\App\Domain\Services\CronService')) {
            $cron = new \App\Domain\Services\CronService();
            $cron->run();
        }
    } catch (\Throwable $e) {
        // Silently fail cron if error
    }
});

// Dispatch request
Router::dispatch();
