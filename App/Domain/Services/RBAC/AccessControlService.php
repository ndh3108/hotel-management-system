<?php

namespace App\Domain\Services\RBAC;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Session;
use App\Domain\Services\SessionCacheService;
use PDO;

/**
 * Trung tâm phân quyền RBAC (dynamic + deny-by-default + soft delete filter).
 *
 * Ghi chú tương thích:
 * - Dự án hiện tại đang dùng `permissions.name` làm permissionCode.
 * - Service này ưu tiên `permissions.code` nếu DB có cột, còn không thì fallback sang `name`.
 */
class AccessControlService
{
    private PDO $pdo;
    private SessionCacheService $sessionCache;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
        $this->sessionCache = new SessionCacheService();
    }

    /**
     * Version hiện tại của quyền dựa theo các role active của user.
     * Bump version khi admin thay đổi role/permission (RBACService sẽ bump).
     */
    public function getCurrentPermissionVersionForUser(int $userId): int
    {
        if ($userId <= 0) {
            return 0;
        }

        // Ưu tiên version toàn hệ (invalidate chính xác khi admin sửa RBAC).
        try {
            $stmt = $this->pdo->query("SELECT permission_version FROM rbac_permission_versions WHERE id = 1 LIMIT 1");
            $v = $stmt->fetchColumn();
            if ($v !== false && $v !== null) {
                return (int)$v;
            }
        } catch (\Throwable $e) {
            // fallback theo schema cũ
        }

        // Fallback: permission_version theo roles (có thể không tồn tại nếu schema chưa nâng cấp).
        $sql = "
            SELECT MAX(COALESCE(r.permission_version, 1)) AS v
            FROM user_roles ur
            JOIN roles r ON r.id = ur.role_id
            WHERE ur.user_id = :user_id
              AND ur.deleted_at IS NULL
              AND (ur.is_active = 1 OR ur.is_active IS NULL)
              AND (ur.expires_at IS NULL OR ur.expires_at > NOW())
              AND r.deleted_at IS NULL
              AND (r.is_active = 1 OR r.is_active IS NULL)
        ";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['user_id' => $userId]);
            $v = $stmt->fetchColumn();
            return $v !== false && $v !== null ? (int)$v : 1;
        } catch (\Throwable $e) {
            // Fallback khi chưa có cột mới.
            return 1;
        }
    }

    /**
     * Load toàn bộ permissions của user từ DB và cache vào session.
     *
     * @return array{permissions:array<int,string>, role_ids:array<int,int>, permission_version:int, role_codes:array<int,string>}
     */
    public function primeSessionCache(int $userId): array
    {
        $permissionVersion = $this->getCurrentPermissionVersionForUser($userId);

        $roleSqlV2 = "
            SELECT DISTINCT r.id AS role_id, COALESCE(r.code, r.name) AS role_code
            FROM user_roles ur
            JOIN roles r ON r.id = ur.role_id
            WHERE ur.user_id = :user_id
              AND ur.deleted_at IS NULL
              AND (ur.is_active = 1 OR ur.is_active IS NULL)
              AND (ur.expires_at IS NULL OR ur.expires_at > NOW())
              AND r.deleted_at IS NULL
              AND (r.is_active = 1 OR r.is_active IS NULL)
            ORDER BY role_id ASC
        ";

        $permissionSqlV2 = "
            SELECT DISTINCT COALESCE(p.code, p.name) AS perm_code
            FROM user_roles ur
            JOIN role_permissions rp ON rp.role_id = ur.role_id
            JOIN permissions p ON p.id = rp.permission_id
            WHERE ur.user_id = :user_id
              AND ur.deleted_at IS NULL
              AND (ur.is_active = 1 OR ur.is_active IS NULL)
              AND (ur.expires_at IS NULL OR ur.expires_at > NOW())
              AND p.deleted_at IS NULL
              AND (p.is_active = 1 OR p.is_active IS NULL)
            ORDER BY perm_code ASC
        ";

        // Schema cũ: không có deleted_at/is_active/permission_version/code/module.
        // v1: schema cũ không có cột `code`, `deleted_at`, `is_active`, ... => chỉ dựa vào `name`.
        $roleSqlV1 = "
            SELECT DISTINCT r.id AS role_id, r.name AS role_code
            FROM user_roles ur
            JOIN roles r ON r.id = ur.role_id
            WHERE ur.user_id = :user_id
              AND (ur.expires_at IS NULL OR ur.expires_at > NOW())
            ORDER BY role_id ASC
        ";

        $permissionSqlV1 = "
            SELECT DISTINCT p.name AS perm_code
            FROM user_roles ur
            JOIN role_permissions rp ON rp.role_id = ur.role_id
            JOIN permissions p ON p.id = rp.permission_id
            WHERE ur.user_id = :user_id
              AND (ur.expires_at IS NULL OR ur.expires_at > NOW())
            ORDER BY perm_code ASC
        ";

        $roles = [];
        $permissions = [];

        try {
            $stmtRoles = $this->pdo->prepare($roleSqlV2);
            $stmtRoles->execute(['user_id' => $userId]);
            $roles = $stmtRoles->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (\Throwable $e) {
            try {
                $stmtRoles = $this->pdo->prepare($roleSqlV1);
                $stmtRoles->execute(['user_id' => $userId]);
                $roles = $stmtRoles->fetchAll(PDO::FETCH_ASSOC) ?: [];
            } catch (\Throwable $e2) {
                $roles = [];
            }
        }

        try {
            $stmtPerm = $this->pdo->prepare($permissionSqlV2);
            $stmtPerm->execute(['user_id' => $userId]);
            $permissions = $stmtPerm->fetchAll(PDO::FETCH_COLUMN) ?: [];
        } catch (\Throwable $e) {
            try {
                $stmtPerm = $this->pdo->prepare($permissionSqlV1);
                $stmtPerm->execute(['user_id' => $userId]);
                $permissions = $stmtPerm->fetchAll(PDO::FETCH_COLUMN) ?: [];
            } catch (\Throwable $e2) {
                $permissions = [];
            }
        }

        if ($roles === []) {
            $legacyRole = $this->getLegacyRoleFromUsersTable($userId);
            if ($legacyRole !== null) {
                $roles[] = $legacyRole;
            }
        }

        if ($permissions === []) {
            $permissions = $this->getLegacyPermissionsFromUsersTable($userId);
        }

        $roleIds = array_values(array_unique(array_filter(array_map(
            fn($r) => (int) ($r['role_id'] ?? 0),
            $roles
        ))));
        $roleCodes = array_values(array_unique(array_filter(array_map(
            fn($r) => strtolower(trim((string) ($r['role_code'] ?? ''))),
            $roles
        ))));

        // Deny-by-default: không có permission => rỗng.
        $permissions = array_values(array_unique(array_filter(array_map(
            fn($permission) => trim((string) $permission),
            $permissions
        ))));

        $this->sessionCache->setForUser($userId, $permissionVersion, $permissions, $roleIds, $roleCodes);
        $this->refreshAuthenticatedUserSnapshot($userId, $roleIds, $roleCodes, $permissions, $permissionVersion);

        return [
            'permissions' => $permissions,
            'role_ids' => $roleIds,
            'permission_version' => $permissionVersion,
            'role_codes' => $roleCodes,
        ];
    }

    /**
     * Permission list trong session.
     *
     * @return array{permission_version:int, permissions:array<int,string>}
     */
    public function getUserPermissionsFromSession(int $userId): array
    {
        $cache = $this->sessionCache->getForUser($userId);
        if ($cache === null) {
            $this->primeSessionCache($userId);
            $cache = $this->sessionCache->getForUser($userId);
        }
        if ($cache === null) {
            return ['permission_version' => 0, 'permissions' => []];
        }

        $currentVersion = $this->getCurrentPermissionVersionForUser($userId);
        if ((int)($cache['permission_version'] ?? 0) !== (int)$currentVersion) {
            $this->primeSessionCache($userId);
            $cache = $this->sessionCache->getForUser($userId);
        }

        return [
            'permission_version' => (int)($cache['permission_version'] ?? 0),
            'permissions' => (array)($cache['permissions'] ?? []),
        ];
    }

    /**
     * @return array{permission_version:int, permissions:array<int,string>, roles:array<int,string>, role_ids:array<int,int>}
     */
    public function getUserAccessSnapshotFromSession(int $userId): array
    {
        $this->getUserPermissionsFromSession($userId);
        $cache = $this->sessionCache->getForUser($userId);

        if ($cache === null) {
            return [
                'permission_version' => 0,
                'permissions' => [],
                'roles' => [],
                'role_ids' => [],
            ];
        }

        return [
            'permission_version' => (int) ($cache['permission_version'] ?? 0),
            'permissions' => (array) ($cache['permissions'] ?? []),
            'roles' => (array) ($cache['role_codes'] ?? []),
            'role_ids' => (array) ($cache['role_ids'] ?? []),
        ];
    }

    /**
     * Kiểm tra permission bằng session cache + deny-by-default.
     */
    public function hasPermission(int $userId, string $permissionCode): bool
    {
        $permissionCode = trim($permissionCode);
        if ($userId <= 0 || $permissionCode === '') {
            return false;
        }

        $perms = $this->getUserPermissionsFromSession($userId);
        return in_array($permissionCode, $perms['permissions'], true);
    }

    /**
     * Kiểm tra Role (hỗ trợ cả Code và Name).
     */
    public function hasRole(int $userId, string|array $roles): bool
    {
        if ($userId <= 0) return false;
        
        $snapshot = $this->getUserAccessSnapshotFromSession($userId);
        $userRoles = (array)($snapshot['roles'] ?? []);
        
        // Merge in role names for legacy support if codes are NULL
        // Actually getUserAccessSnapshotFromSession should already have normalized codes/names
        
        $targetRoles = is_array($roles) ? $roles : [$roles];
        $targetRoles = array_map(fn($r) => strtolower(trim((string)$r)), $targetRoles);
        $userRoles = array_map(fn($r) => strtolower(trim((string)$r)), $userRoles);

        foreach ($targetRoles as $target) {
            if (in_array($target, $userRoles, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check permission dùng payload JWT (stateless).
     * Nếu permission_version không khớp => deny để tránh cache stale.
     */
    public function hasPermissionFromJwtPayload(array $payload, string $permissionCode): bool
    {
        $permissionCode = trim($permissionCode);
        $userId = isset($payload['userId']) ? (int)$payload['userId'] : 0;

        if ($userId <= 0 || $permissionCode === '') {
            return false;
        }

        $payloadPerms = $payload['permissions'] ?? [];
        if (!is_array($payloadPerms) || empty($payloadPerms)) {
            return false;
        }

        // Version invalidation cho JWT
        if (isset($payload['permission_version'])) {
            $currentVersion = $this->getCurrentPermissionVersionForUser($userId);
            if ((int)$payload['permission_version'] !== (int)$currentVersion) {
                return false;
            }
        } else {
            // Token cũ chưa có version: fallback sang kiểm tra bằng session/cache để đảm bảo deny-by-default.
            return $this->hasPermission($userId, $permissionCode);
        }

        return in_array($permissionCode, $payloadPerms, true);
    }

    /**
     * Quyền truy cập trang quản trị.
     * Giữ tương thích code cũ hiện có trong DB.
     */
    public function canAccessAdmin(int $userId): bool
    {
        // Deny-by-default: chỉ qua khi user có permission rõ ràng.
        $snapshot = $this->getUserAccessSnapshotFromSession($userId);
        $roles = array_map(
            fn($role) => strtolower(trim((string) $role)),
            (array) ($snapshot['roles'] ?? [])
        );

        if (in_array('admin', $roles, true) || in_array('super_admin', $roles, true) || in_array('super-admin', $roles, true)) {
            return true;
        }

        $permissions = (array) ($snapshot['permissions'] ?? []);
        return in_array('VIEW_DASHBOARD', $permissions, true)
            || in_array('Access_Dashboard', $permissions, true);
    }

    private function getLegacyRoleFromUsersTable(int $userId): ?array
    {
        $queries = [
            "
                SELECT r.id AS role_id, COALESCE(r.code, r.name) AS role_code
                FROM users u
                JOIN roles r ON r.id = u.role_id
                WHERE u.id = :user_id
                  AND u.deleted_at IS NULL
                LIMIT 1
            ",
            "
                SELECT r.id AS role_id, r.name AS role_code
                FROM users u
                JOIN roles r ON r.id = u.role_id
                WHERE u.id = :user_id
                LIMIT 1
            ",
        ];

        foreach ($queries as $sql) {
            try {
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(['user_id' => $userId]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
                if ($row !== null) {
                    return $row;
                }
            } catch (\Throwable $e) {
                continue;
            }
        }

        return null;
    }

    /**
     * @return array<int,string>
     */
    private function getLegacyPermissionsFromUsersTable(int $userId): array
    {
        $queries = [
            "
                SELECT DISTINCT COALESCE(p.code, p.name) AS perm_code
                FROM users u
                JOIN role_permissions rp ON rp.role_id = u.role_id
                JOIN permissions p ON p.id = rp.permission_id
                WHERE u.id = :user_id
                  AND u.deleted_at IS NULL
                  AND p.deleted_at IS NULL
                  AND (p.is_active = 1 OR p.is_active IS NULL)
                ORDER BY perm_code ASC
            ",
            "
                SELECT DISTINCT p.name AS perm_code
                FROM users u
                JOIN role_permissions rp ON rp.role_id = u.role_id
                JOIN permissions p ON p.id = rp.permission_id
                WHERE u.id = :user_id
                ORDER BY perm_code ASC
            ",
        ];

        foreach ($queries as $sql) {
            try {
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(['user_id' => $userId]);
                $permissions = $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
                if ($permissions !== []) {
                    return array_values(array_unique(array_filter(array_map(
                        fn($permission) => trim((string) $permission),
                        $permissions
                    ))));
                }
            } catch (\Throwable $e) {
                continue;
            }
        }

        return [];
    }

    /**
     * @param array<int,int> $roleIds
     * @param array<int,string> $roleCodes
     * @param array<int,string> $permissions
     */
    private function refreshAuthenticatedUserSnapshot(
        int $userId,
        array $roleIds,
        array $roleCodes,
        array $permissions,
        int $permissionVersion
    ): void {
        if (Auth::id() !== $userId) {
            return;
        }

        $user = Auth::user() ?? [];
        if ($user === []) {
            return;
        }

        $user['role_id'] = $roleIds[0] ?? ($user['role_id'] ?? null);
        $user['roles'] = $roleCodes;
        $user['permissions'] = $permissions;
        $user['permission_version'] = $permissionVersion;
        $user['role'] = in_array('admin', $roleCodes, true) || in_array('super_admin', $roleCodes, true) || in_array('super-admin', $roleCodes, true)
            ? 'admin'
            : 'user';

        Auth::setUser($user);
        Session::set('user', $user);
    }
}

