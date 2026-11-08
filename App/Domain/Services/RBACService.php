<?php

namespace App\Domain\Services;

use App\Models\User;
use App\Models\UserRole;
use App\Models\Role;
use App\Models\Permission;
use PDO;
use App\Domain\Services\AuditLogService;
use App\Core\Auth;

class RBACService
{
    private UserRole $userRoleModel;
    private User $userModel;
    private PDO $pdo;
    private AuditLogService $auditLog;

    public function __construct()
    {
        $this->userRoleModel = new UserRole();
        $this->userModel = new User();
        // dùng PDO trực tiếp để bump version (tối ưu 1 query).
        $this->pdo = $this->userModel->getConnection();
        $this->auditLog = new AuditLogService();
    }

    /**
     * Gọi bằng cronjob hoặc trước mỗi request để dọn dẹp các quyền tạm thời.
     */
    public function cleanupExpiredRoles(): void
    {
        $this->userRoleModel->cleanupExpiredRoles();
    }

    /**
     * Phân quyền (N-N)
     */
    public function assignRoleToUser(int $userId, int $roleId, ?string $expiresAt = null): bool
    {
        $old = null;
        try {
            $stmtOld = $this->pdo->prepare("
                SELECT * FROM user_roles
                WHERE user_id = :user_id AND role_id = :role_id
                ORDER BY expires_at DESC
                LIMIT 1
            ");
            $stmtOld->execute(['user_id' => $userId, 'role_id' => $roleId]);
            $old = $stmtOld->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (\Throwable $e) {
            $old = null;
        }

        $ok = $this->userRoleModel->assignRole($userId, $roleId, $expiresAt);
        if ($ok) {
            $this->bumpRolePermissionVersion($roleId);

            $new = [
                'user_id' => $userId,
                'role_id' => $roleId,
                'expires_at' => $expiresAt,
            ];
            $this->auditLog->logAction('ASSIGN_ROLE', 'user_roles', $userId, $old ? $old : null, $new);
        }
        return $ok;
    }
    
    /**
     * Revoke quyền
     */
    public function revokeRoleFromUser(int $userId, int $roleId): bool
    {
        $old = null;
        try {
            $stmtOld = $this->pdo->prepare("
                SELECT * FROM user_roles
                WHERE user_id = :user_id AND role_id = :role_id
                LIMIT 1
            ");
            $stmtOld->execute(['user_id' => $userId, 'role_id' => $roleId]);
            $old = $stmtOld->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (\Throwable $e) {
            $old = null;
        }

        $ok = $this->userRoleModel->removeRole($userId, $roleId);
        if ($ok) {
            $this->bumpRolePermissionVersion($roleId);

            $this->auditLog->logAction('REVOKE_ROLE', 'user_roles', $userId, $old ? $old : null, null);
        }
        return $ok;
    }

    /**
     * Update permissions for a specific role
     */
    public function updateRolePermissions(int $roleId, array $permissionIds): bool
    {
        $oldPermissionIds = [];
        try {
            $stmtOld = $this->pdo->prepare("
                SELECT permission_id
                FROM role_permissions
                WHERE role_id = :role_id
            ");
            $stmtOld->execute(['role_id' => $roleId]);
            $oldPermissionIds = $stmtOld->fetchAll(PDO::FETCH_COLUMN) ?: [];
        } catch (\Throwable $e) {
            $oldPermissionIds = [];
        }

        $roleModel = new Role();
        // Remove old permissions
        $sqlDelete = "DELETE FROM role_permissions WHERE role_id = :role_id";
        $stmtDelete = $roleModel->getConnection()->prepare($sqlDelete);
        $stmtDelete->execute(['role_id' => $roleId]);

        $ok = true;
        if (!empty($permissionIds)) {
            // Insert new permissions
            $sqlInsert = "INSERT INTO role_permissions (role_id, permission_id) VALUES ";
            $placeholders = [];
            $values = [];
            foreach ($permissionIds as $pId) {
                $placeholders[] = "(?, ?)";
                $values[] = $roleId;
                $values[] = $pId;
            }

            $sqlInsert .= implode(", ", $placeholders);
            $stmtInsert = $roleModel->getConnection()->prepare($sqlInsert);
            $ok = $stmtInsert->execute($values);
        }

        if ($ok) {
            $this->bumpRolePermissionVersion($roleId);
            $this->auditLog->logAction(
                'UPDATE_ROLE_PERMISSIONS',
                'role_permissions',
                $roleId,
                ['old_permission_ids' => array_values(array_map('intval', $oldPermissionIds))],
                ['new_permission_ids' => array_values(array_map('intval', $permissionIds))]
            );
        }

        return (bool) $ok;
    }

    /**
     * Bump version toàn hệ RBAC (invalidate cache đúng lúc).
     * Dùng cho CRUD role/permission khi không đi qua updateRolePermissions().
     */
    public function bumpGlobalPermissionVersion(): void
    {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE rbac_permission_versions
                SET permission_version = COALESCE(permission_version, 1) + 1,
                    updated_at = NOW()
                WHERE id = 1
            ");
            $stmt->execute();
        } catch (\Throwable $e) {
            // schema cũ không có bảng global => bỏ qua
        }
    }

    private function bumpRolePermissionVersion(int $roleId): void
    {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE roles
                SET permission_version = COALESCE(permission_version, 1) + 1
                WHERE id = :role_id
            ");
            $stmt->execute(['role_id' => $roleId]);
        } catch (\Throwable $e) {
            // Nếu schema chưa có permission_version thì bỏ qua để không phá luồng cũ.
        }

        // Bump version toàn hệ để cache cho user có thể invalid chính xác.
        $this->bumpGlobalPermissionVersion();
    }
}

