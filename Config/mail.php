<?php

/**
 * Mail Configuration
 * Supports SMTP and PHP's built-in mail() function
 */

/**
 * Helper function to get environment variable with fallback
 * Replace with your actual env() function if it exists
 */
if (!function_exists('env')) {
    function env($key, $default = null)
    {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? null;

        if ($value === null) {
            return $default;
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'null':
            case '(null)':
                return null;
            case 'empty':
            case '(empty)':
                return '';
        }

        return $value;
    }
}

return [
    // Email driver: 'smtp' or 'mail'
    'driver' => env('MAIL_DRIVER', 'mail'),

    // SMTP Configuration
    'smtp' => [
        'host' => env('MAIL_HOST', 'smtp.gmail.com'),
        'port' => env('MAIL_PORT', 587),
        'username' => env('MAIL_USERNAME', ''),
        'password' => env('MAIL_PASSWORD', ''),
        'encryption' => env('MAIL_ENCRYPTION', 'tls'), // 'tls' or 'ssl'
        'from_address' => env('MAIL_FROM_ADDRESS', 'noreply@hotelmanagement.com'),
        'from_name' => env('MAIL_FROM_NAME', 'Hotel Management System'),
    ],

    // PHP mail() Configuration
    'mail' => [
        'from_address' => env('MAIL_FROM_ADDRESS', 'noreply@hotelmanagement.com'),
        'from_name' => env('MAIL_FROM_NAME', 'Hotel Management System'),
    ],

    // Retry configuration
    'retry_count' => 3,
    'retry_delay' => 1000, // milliseconds

    // Email queue (for future use with queue system)
    'queue' => [
        'enabled' => false,
        'driver' => 'database', // 'database' or 'file'
    ],
];

