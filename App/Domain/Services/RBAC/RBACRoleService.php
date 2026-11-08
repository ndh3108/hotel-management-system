<?php

namespace App\Domain\Services\RBAC;

use App\Models\Role;
use App\Models\Permission;
use App\Domain\Services\RBACService;
use App\Domain\Services\AuditLogService;
use PDO;

/**
 * Role management (soft delete + permission matrix).
 */
class RBACRoleService
{
    private Role $roleModel;
    private Permission $permissionModel;
    private RBACService $rbac;
    private PDO $pdo;
    private AuditLogService $auditLog;

    public function __construct()
    {
        $this->roleModel = new Role();
        $this->permissionModel = new Permission();
        $this->rbac = new RBACService();
        $this->pdo = $this->roleModel->getConnection();
        $this->auditLog = new AuditLogService();
    }

    public function listRoles(): array
    {
        try {
            $stmt = $this->pdo->query("
                SELECT
                    r.*,
                    COUNT(DISTINCT CASE
                        WHEN ur.deleted_at IS NULL
                         AND (ur.is_active = 1 OR ur.is_active IS NULL)
                         AND (COALESCE(ur.expired_at, ur.expires_at) IS NULL OR COALESCE(ur.expired_at, ur.expires_at) > NOW())
                        THEN ur.user_id
                    END) AS active_user_count,
                    COUNT(DISTINCT rp.permission_id) AS permission_count
                FROM roles
                r
                LEFT JOIN user_roles ur ON ur.role_id = r.id
                LEFT JOIN role_permissions rp ON rp.role_id = r.id
                WHERE r.deleted_at IS NULL
                GROUP BY r.id
                ORDER BY r.sort_order ASC, r.id ASC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (\Throwable $e) {
            return $this->roleModel->getAll();
        }
    }

    public function findRole(int $id): ?array
    {
        return $this->roleModel->findById($id);
    }

    public function createRole(array $data): int|false
    {
        $name = trim((string) ($data['name'] ?? ''));
        if ($name === '') {
            return false;
        }

        $code = $this->normalizeCode((string) ($data['code'] ?? ''));
        if ($code === '') {
            $code = $this->normalizeCode($name);
        }

        $payload = [
            'name' => $name,
            'code' => $code,
            'description' => $data['description'] ?? null,
            'is_system' => 0,
            'is_active' => 1,
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'permission_version' => 1,
            'deleted_at' => null,
        ];

        $id = $this->roleModel->create($payload);
        if ($id) {
            $this->rbac->bumpGlobalPermissionVersion();
        }
        return $id;
    }

    public function updateRole(int $roleId, array $data): bool
    {
        $existingRole = $this->findRole($roleId);
        if (!$existingRole) {
            return false;
        }

        $payload = [
            'name' => trim((string) ($data['name'] ?? '')),
            'code' => $this->normalizeCode((string) ($data['code'] ?? '')),
            'description' => $data['description'] ?? null,
            'is_active' => isset($data['is_active']) ? (int) $data['is_active'] : 1,
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ];

        // Nếu code trống thì fallback về name
        if ($payload['code'] === '') {
            $payload['code'] = $this->normalizeCode($payload['name']);
        }

        if ($payload['name'] === '') {
            return false;
        }

        if ((int) ($existingRole['is_system'] ?? 0) === 1) {
            $payload['is_active'] = 1;
        }

        $ok = $this->roleModel->update($roleId, $payload);
        if ($ok) {
            $this->rbac->bumpGlobalPermissionVersion();
        }
        return (bool) $ok;
    }

    public function softDeleteRole(int $roleId): bool
    {
        $role = $this->findRole($roleId);
        if (!$role) {
            return false;
        }

        $usedCount = 0;
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) 
                FROM user_roles
                WHERE role_id = :role_id
                  AND deleted_at IS NULL
                  AND (is_active = 1 OR is_active IS NULL)
                  AND (expired_at IS NULL OR expired_at > NOW())
            ");
            $stmt->execute(['role_id' => $roleId]);
            $usedCount = (int) $stmt->fetchColumn();
        } catch (\Throwable $e) {
            // Backward compatibility.
            try {
                $stmt = $this->pdo->prepare("
                    SELECT COUNT(*) 
                    FROM user_roles
                    WHERE role_id = :role_id
                      AND (expires_at IS NULL OR expires_at > NOW())
                ");
                $stmt->execute(['role_id' => $roleId]);
                $usedCount = (int) $stmt->fetchColumn();
            } catch (\Throwable $e2) {
                $usedCount = 0;
            }
        }

        $roleKey = strtolower(trim((string) ($role['code'] ?? $role['name'] ?? '')));

        // Protect admin/super_admin.
        if ((int) ($role['is_system'] ?? 0) === 1 && in_array($roleKey, ['admin', 'super_admin', 'super-admin'], true)) {
            return false;
        }

        // Always soft delete.
        $ok = $this->roleModel->update($roleId, [
            'deleted_at' => date('Y-m-d H:i:s'),
            'is_active' => 0,
        ]);

        if ($ok) {
            $this->rbac->bumpGlobalPermissionVersion();
            if ($usedCount > 0) {
                $this->auditLog->logAction(
                    'SOFT_DELETE_ROLE',
                    'roles',
                    $roleId,
                    ['used_user_count' => $usedCount],
                    ['deleted_at' => date('Y-m-d H:i:s'), 'is_active' => 0]
                );
            }
        }

        return (bool) $ok;
    }

    /**
     * Permissions grouped by module for role edit UI.
     *
     * @return array<string, array<int, array{id:int,code:string,name:string,checked:bool,description:?string}>>
     */
    public function getPermissionsMatrixForRole(int $roleId): array
    {
        $result = [];

        // v2 schema: deleted_at + is_active.
        $sqlV2 = "
            SELECT
                p.id,
                COALESCE(p.code, p.name) AS code,
                p.name,
                COALESCE(p.module, p.block_module, 'Chung') AS module,
                p.description,
                CASE WHEN rp.permission_id IS NULL THEN 0 ELSE 1 END AS assigned
            FROM permissions p
            LEFT JOIN role_permissions rp
                ON rp.permission_id = p.id AND rp.role_id = :role_id
            WHERE p.deleted_at IS NULL
              AND (p.is_active = 1 OR p.is_active IS NULL)
            ORDER BY module ASC, p.name ASC
        ";

        // v1 schema fallback.
        $sqlV1 = "
            SELECT
                p.id,
                COALESCE(p.code, p.name) AS code,
                p.name,
                COALESCE(p.block_module, 'Chung') AS module,
                p.description,
                CASE WHEN rp.permission_id IS NULL THEN 0 ELSE 1 END AS assigned
            FROM permissions p
            LEFT JOIN role_permissions rp
                ON rp.permission_id = p.id AND rp.role_id = :role_id
            ORDER BY module ASC, p.name ASC
        ";

        try {
            $stmt = $this->pdo->prepare($sqlV2);
            $stmt->execute(['role_id' => $roleId]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (\Throwable $e) {
            try {
                $stmt = $this->pdo->prepare($sqlV1);
                $stmt->execute(['role_id' => $roleId]);
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            } catch (\Throwable $e2) {
                // Thấp nhất: chỉ lấy id/name/block_module, không dùng code/module mới.
                $sqlV0 = "
                    SELECT
                        p.id,
                        p.name,
                        COALESCE(p.block_module, 'Chung') AS module,
                        p.description,
                        CASE WHEN rp.permission_id IS NULL THEN 0 ELSE 1 END AS assigned
                    FROM permissions p
                    LEFT JOIN role_permissions rp
                        ON rp.permission_id = p.id AND rp.role_id = :role_id
                    ORDER BY module ASC, p.name ASC
                ";
                $stmt = $this->pdo->prepare($sqlV0);
                $stmt->execute(['role_id' => $roleId]);
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            }
        }

        foreach ($rows as $row) {
            $module = (string) ($row['module'] ?? 'Chung');
            if (!isset($result[$module])) {
                $result[$module] = [];
            }
            $result[$module][] = [
                'id' => (int) $row['id'],
                'code' => (string) ($row['code'] ?? $row['name']),
                'name' => (string) ($row['name'] ?? ''),
                'checked' => ((int) ($row['assigned'] ?? 0)) === 1,
                'description' => $row['description'] ?? null,
            ];
        }

        return $result;
    }

    private function normalizeCode(string $value): string
    {
        $value = strtoupper(trim($value));
        return preg_replace('/[^A-Z0-9_]+/', '_', $value) ?? '';
    }
}

