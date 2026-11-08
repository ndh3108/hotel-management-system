<?php

namespace App\Core;

use App\Domain\Services\LoggerService;

class Log
{
    private static ?LoggerService $logger = null;

    private static function getLogger(): LoggerService
    {
        if (self::$logger === null) {
            self::$logger = new LoggerService();
        }
        return self::$logger;
    }

    public static function auth(string $action, ?int $actorId, string $status, array $context = [], string $severity = 'INFO'): void
    {
        self::getLogger()->log([
            'actor_id' => $actorId,
            'role' => $context['role'] ?? 'Guest',
            'action' => $action,
            'resource_type' => 'Auth',
            'after' => $context,
            'status' => $status,
            'severity' => $severity
        ]);
    }

    public static function payment(string $action, ?int $actorId, ?int $resourceId, string $status, array $context = [], string $severity = 'INFO'): void
    {
        self::getLogger()->log([
            'actor_id' => $actorId,
            'role' => $context['role'] ?? 'System',
            'action' => $action,
            'resource_type' => 'Payment',
            'resource_id' => $resourceId,
            'after' => $context,
            'status' => $status,
            'severity' => $severity
        ]);
    }

    public static function security(string $action, ?int $actorId, string $status, array $context = [], string $severity = 'WARNING'): void
    {
        self::getLogger()->log([
            'actor_id' => $actorId,
            'role' => $context['role'] ?? 'Unknown',
            'action' => $action,
            'resource_type' => 'Security',
            'after' => $context,
            'status' => $status,
            'severity' => $severity
        ]);
    }

    public static function event(string $action, ?int $actorId, ?string $resourceType, ?int $resourceId, string $status, array $before = [], array $after = [], string $severity = 'INFO'): void
    {
        self::getLogger()->log([
            'actor_id' => $actorId,
            'role' => $after['role'] ?? $before['role'] ?? 'User',
            'action' => $action,
            'resource_type' => $resourceType ?? 'Event',
            'resource_id' => $resourceId,
            'before' => empty($before) ? null : $before,
            'after' => empty($after) ? null : $after,
            'status' => $status,
            'severity' => $severity
        ]);
    }
}

