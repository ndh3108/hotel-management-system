<?php

namespace App\Domain\Services\RBAC;

use App\Models\Permission;
use App\Domain\Services\RBACService;
use App\Domain\Services\AuditLogService;
use PDO;

/**
 * Permission management (soft delete + enable/disable).
 */
class RBACPermissionService
{
    private Permission $permissionModel;
    private RBACService $rbac;
    private PDO $pdo;
    private AuditLogService $auditLog;

    public function __construct()
    {
        $this->permissionModel = new Permission();
        $this->rbac = new RBACService();
        $this->pdo = $this->permissionModel->getConnection();
        $this->auditLog = new AuditLogService();
    }

    public function listPermissions(): array
    {
        try {
            $stmt = $this->pdo->query("
                SELECT
                    p.*,
                    COUNT(DISTINCT CASE
                        WHEN r.deleted_at IS NULL
                         AND (r.is_active = 1 OR r.is_active IS NULL)
                        THEN rp.role_id
                    END) AS active_role_count
                FROM permissions
                p
                LEFT JOIN role_permissions rp ON rp.permission_id = p.id
                LEFT JOIN roles r ON r.id = rp.role_id
                WHERE p.deleted_at IS NULL
                GROUP BY p.id
                ORDER BY p.module ASC, p.name ASC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (\Throwable $e) {
            return $this->permissionModel->getAll();
        }
    }

    public function findPermission(int $id): ?array
    {
        return $this->permissionModel->findById($id);
    }

    public function createPermission(array $data): int|false
    {
        $name = trim((string) ($data['name'] ?? ''));
        if ($name === '') {
            return false;
        }

        $code = $this->normalizeCode((string) ($data['code'] ?? ''));
        if ($code === '') {
            $code = $this->normalizeCode($name);
        }

        $module = trim((string) ($data['module'] ?? ($data['block_module'] ?? '')));
        if ($module === '') {
            $module = $data['block_module'] ?? null;
        }

        $payload = [
            'code' => $code,
            'name' => $name,
            'module' => $module,
            'block_module' => $module,
            'description' => $data['description'] ?? null,
            'is_active' => 1,
            'deleted_at' => null,
        ];

        $id = $this->permissionModel->create($payload);
        if ($id) {
            $this->rbac->bumpGlobalPermissionVersion();
        }
        return $id;
    }

    public function updatePermission(int $permissionId, array $data): bool
    {
        $name = trim((string) ($data['name'] ?? ''));
        if ($name === '') {
            return false;
        }

        $code = $this->normalizeCode((string) ($data['code'] ?? ''));
        if ($code === '') {
            $code = $this->normalizeCode($name);
        }

        $module = trim((string) ($data['module'] ?? ($data['block_module'] ?? '')));
        if ($module === '') {
            $module = $data['block_module'] ?? null;
        }

        $payload = [
            'code' => $code,
            'name' => $name,
            'module' => $module,
            'block_module' => $module,
            'description' => $data['description'] ?? null,
            'is_active' => isset($data['is_active']) ? (int) $data['is_active'] : 1,
        ];

        $ok = $this->permissionModel->update($permissionId, $payload);
        if ($ok) {
            $this->rbac->bumpGlobalPermissionVersion();
        }
        return (bool) $ok;
    }

    public function disablePermission(int $permissionId): bool
    {
        $ok = $this->permissionModel->update($permissionId, [
            'is_active' => 0,
        ]);
        if ($ok) {
            $this->rbac->bumpGlobalPermissionVersion();
        }
        return (bool) $ok;
    }

    public function softDeletePermission(int $permissionId): bool
    {
        $used = 0;
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) 
                FROM role_permissions rp
                JOIN roles r ON r.id = rp.role_id
                WHERE rp.permission_id = :permission_id
                  AND r.deleted_at IS NULL
                  AND (r.is_active = 1 OR r.is_active IS NULL)
            ");
            $stmt->execute(['permission_id' => $permissionId]);
            $used = (int) $stmt->fetchColumn();
        } catch (\Throwable $e) {
            $used = 0;
        }

        // Soft delete permission.
        $ok = $this->permissionModel->update($permissionId, [
            'deleted_at' => date('Y-m-d H:i:s'),
            'is_active' => 0,
        ]);

        if ($ok) {
            $this->rbac->bumpGlobalPermissionVersion();
            if ($used > 0) {
                $this->auditLog->logAction(
                    'SOFT_DELETE_PERMISSION',
                    'permissions',
                    $permissionId,
                    ['used_role_count' => $used],
                    ['deleted_at' => date('Y-m-d H:i:s'), 'is_active' => 0]
                );
            }
        }

        return (bool) $ok;
    }

    private function normalizeCode(string $value): string
    {
        $value = strtoupper(trim($value));
        return preg_replace('/[^A-Z0-9_]+/', '_', $value) ?? '';
    }
}

