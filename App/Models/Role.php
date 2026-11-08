<?php

namespace App\Models;

use App\Core\Model;

/** Model Role - Bảng roles. CRUD: getAll, findById, create, update, delete. */
class Role extends Model
{
    protected string $table = 'roles';

    protected array $fillable = [
        'name',
        'code',
        'description',
        'is_system',
        'is_active',
        'sort_order',
        'permission_version',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function getActiveRoles(): array
    {
        try {
            $stmt = $this->pdo->query("
                SELECT *
                FROM roles
                WHERE deleted_at IS NULL
                  AND (is_active = 1 OR is_active IS NULL)
                ORDER BY sort_order ASC, id ASC
            ");
            return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        } catch (\Throwable $e) {
            return $this->getAll();
        }
    }
}

