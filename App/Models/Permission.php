<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Permission extends Model
{
    protected string $table = 'permissions';
    protected array $fillable = [
        'code',
        'name',
        'module',
        'block_module',
        'description',
        'is_active',
        'permission_version',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function getRolePermissions(int $roleId): array
    {
        $sql = "SELECT permission_id FROM role_permissions WHERE role_id = :role_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['role_id' => $roleId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function updateRolePermissions(int $roleId, array $permissionIds): void
    {
        try {
            $this->pdo->beginTransaction();
            // Delete old permissions for this role
            $sqlDelete = "DELETE FROM role_permissions WHERE role_id = :role_id";
            $stmtDel = $this->pdo->prepare($sqlDelete);
            $stmtDel->execute(['role_id' => $roleId]);

            // Insert new permissions
            if (!empty($permissionIds)) {
                $sqlInsert = "INSERT INTO role_permissions (role_id, permission_id) VALUES (:role_id, :permission_id)";
                $stmtIns = $this->pdo->prepare($sqlInsert);
                foreach ($permissionIds as $pid) {
                    $stmtIns->execute(['role_id' => $roleId, 'permission_id' => $pid]);
                }
            }
            $this->pdo->commit();
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}

