<?php

namespace App\Core;

class CreateServiceBookingMiddleware extends Middleware
{
    public function handle(\Closure $next): void
    {
        if (!Auth::check()) {
            header('Location: ' . \App\Core\url('/login'));
            exit;
        }

        if (!Auth::hasPermission('CREATE_SERVICE_BOOKING')) {
            http_response_code(403);
            echo '403 Forbidden - Missing permission CREATE_SERVICE_BOOKING';
            exit;
        }

        $next();
    }
}

