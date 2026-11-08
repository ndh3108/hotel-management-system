<?php

namespace App\Core;

class ManageServiceBookingMiddleware extends Middleware
{
    public function handle(\Closure $next): void
    {
        if (!Auth::check()) {
            header('Location: ' . \App\Core\url('/login'));
            exit;
        }

        if (!Auth::hasPermission('MANAGE_SERVICE_BOOKING')) {
            http_response_code(403);
            echo '403 Forbidden - Missing permission MANAGE_SERVICE_BOOKING';
            exit;
        }

        $next();
    }
}

