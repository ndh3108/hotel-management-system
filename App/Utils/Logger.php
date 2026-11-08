<?php

namespace App\Utils;

class Logger
{
    public static function logMessage(string $message): void
    {
        $logDir = BASE_PATH . '/logs';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0777, true);
        }
        
        $logFile = $logDir . '/app.log';
        file_put_contents(
            $logFile,
            date("Y-m-d H:i:s") . " - " . $message . PHP_EOL,
            FILE_APPEND
        );
    }
}

