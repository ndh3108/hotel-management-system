<?php

namespace App\Domain\Services;

use App\Core\Session;

/**
 * Cache quyền trong session, có version để invalidation đúng lúc.
 */
class SessionCacheService
{
    private const SESSION_KEY = 'rbac_cache';

    /**
     * @return array{user_id:int, permission_version:int, permissions:array<int,string>, role_ids:array<int,int>, role_codes:array<int,string>}|null
     */
    public function getForUser(int $userId): ?array
    {
        $cache = Session::get(self::SESSION_KEY, null);
        if (!is_array($cache)) {
            return null;
        }
        if (($cache['user_id'] ?? null) !== $userId) {
            return null;
        }
        return $cache;
    }

    /**
     * @param array<int,string> $permissions
     * @param array<int,int> $roleIds
     * @param array<int,string> $roleCodes
     */
    public function setForUser(int $userId, int $permissionVersion, array $permissions, array $roleIds, array $roleCodes = []): void
    {
        $permissions = array_values(array_unique(array_filter(array_map(
            fn ($permission) => trim((string) $permission),
            $permissions
        ))));
        $roleIds = array_values(array_unique(array_filter(array_map('intval', $roleIds), fn ($roleId) => $roleId > 0)));
        $roleCodes = array_values(array_unique(array_filter(array_map(
            fn ($roleCode) => strtolower(trim((string) $roleCode)),
            $roleCodes
        ))));

        Session::set(self::SESSION_KEY, [
            'user_id' => $userId,
            'permission_version' => $permissionVersion,
            'permissions' => $permissions,
            'role_ids' => $roleIds,
            'role_codes' => $roleCodes,
        ]);

        Session::set('permissions', $permissions);
        Session::set('roles', $roleCodes);
        Session::set('permission_version', $permissionVersion);
    }

    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
        Session::forget('permissions');
        Session::forget('roles');
        Session::forget('permission_version');
    }
}

