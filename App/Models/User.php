<?php

namespace App\Models;

use App\Core\Model;
use PDO;
/** Model User - Bảng users. CRUD: getAll, findById, create, update, delete. */
class User extends Model
{
    protected string $table = 'users';
    private ?array $existingColumns = null;

    protected array $fillable = [
        'role_id',
        'full_name',
        'email',
        'password_hash',
        'password',
        'phone',
        'status',
        'failed_login_attempts',
        'locked_until',
        'email_verified_at',
        'phone_verified_at',
        'last_login_at',
        'last_login_ip',
        'avatar_url',
        'profile_completion_percent',
    ];

    public function findByEmail($email)
    {
        $sql = "SELECT * FROM users WHERE email = :email AND deleted_at IS NULL LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByPhone(string $phone): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE phone = :phone AND deleted_at IS NULL LIMIT 1");
        $stmt->execute(['phone' => $phone]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Get all permission names for a specific user ID
     */
    public function getPermissions(int $userId): array
    {
        // Get permissions from user_roles pivot table
        $sql = "
            SELECT DISTINCT p.name
            FROM permissions p
            JOIN role_permissions rp ON p.id = rp.permission_id
            JOIN user_roles ur ON rp.role_id = ur.role_id
            WHERE ur.user_id = :user_id
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        $permissions = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Also check direct role_id from users table if user_roles is empty
        if (empty($permissions)) {
            $sqlDirect = "
                SELECT DISTINCT p.name
                FROM permissions p
                JOIN role_permissions rp ON p.id = rp.permission_id
                JOIN users u ON rp.role_id = u.role_id
                WHERE u.id = :user_id
            ";
            $stmtDirect = $this->pdo->prepare($sqlDirect);
            $stmtDirect->execute(['user_id' => $userId]);
            $permissions = $stmtDirect->fetchAll(PDO::FETCH_COLUMN);
        }

        return $permissions;
    }

    public function getCursorPageWithRole(int $lastId = 0, int $limit = 15, ?int $roleId = null): array
    {
        $sql = "SELECT u.*, r.name as role_name
                FROM users u
                LEFT JOIN roles r ON u.role_id = r.id
                WHERE u.id > :last_id";

        if ($roleId !== null) {
            $sql .= " AND u.role_id = :role_id";
        }

        $sql .= " ORDER BY u.id ASC LIMIT :limit_rows";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':last_id', $lastId, PDO::PARAM_INT);
        if ($roleId !== null) {
            $stmt->bindValue(':role_id', $roleId, PDO::PARAM_INT);
        }
        $stmt->bindValue(':limit_rows', $limit + 1, PDO::PARAM_INT);
        $stmt->execute();

        return $this->buildCursorPage($stmt->fetchAll(PDO::FETCH_ASSOC) ?: [], $limit);
    }

    public function getPaginatedWithRole(int $cursor = 0, int $limit = 10, ?int $roleId = null): array
    {
        $limit = max(1, $limit);
        $sql = "SELECT u.*, r.name as role_name
                FROM users u
                LEFT JOIN roles r ON u.role_id = r.id
                WHERE u.deleted_at IS NULL";

        if ($cursor > 0) {
            $sql .= " AND u.id > :cursor";
        }

        if ($roleId !== null) {
            $sql .= " AND u.role_id = :role_id";
        }

        $sql .= " ORDER BY u.id ASC LIMIT :limit_rows";

        $stmt = $this->pdo->prepare($sql);
        if ($cursor > 0) {
            $stmt->bindValue(':cursor', $cursor, PDO::PARAM_INT);
        }
        if ($roleId !== null) {
            $stmt->bindValue(':role_id', $roleId, PDO::PARAM_INT);
        }
        $stmt->bindValue(':limit_rows', $limit + 1, PDO::PARAM_INT);
        $stmt->execute();

        return $this->buildCursorPage($stmt->fetchAll(PDO::FETCH_ASSOC) ?: [], $limit);
    }

    public function findById($id): ?array
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE `{$this->primaryKey}` = ? AND `deleted_at` IS NULL LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function hasColumn(string $column): bool
    {
        return in_array($column, $this->getExistingColumns(), true);
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function filterExistingColumns(array $data): array
    {
        $columns = array_flip($this->getExistingColumns());
        return array_intersect_key($data, $columns);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function updateExistingFields(int $id, array $data): bool
    {
        $filtered = $this->filterExistingColumns($data);
        if ($filtered === []) {
            return false;
        }

        return $this->update($id, $filtered);
    }

    /**
     * @return array<int, string>
     */
    private function getExistingColumns(): array
    {
        if ($this->existingColumns !== null) {
            return $this->existingColumns;
        }

        try {
            $stmt = $this->pdo->query("SHOW COLUMNS FROM `{$this->table}`");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            $this->existingColumns = array_values(array_filter(array_map(
                fn (array $row): string => (string) ($row['Field'] ?? ''),
                $rows
            )));
        } catch (\Throwable $e) {
            $this->existingColumns = [];
        }

        return $this->existingColumns;
    }
}

