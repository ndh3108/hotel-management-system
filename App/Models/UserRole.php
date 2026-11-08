<?php

namespace App\Models;

use App\Core\Model;
use App\Core\Auth;
use PDO;

class UserRole extends Model
{
    protected string $table = 'user_roles';

    // No auto-increment ID
    public function getById($id) { return null; }

    /**
     * Assign a role to a user.
     * @param int $userId
     * @param int $roleId
     * @param string|null $expiresAt Datetime string
     */
    public function assignRole(int $userId, int $roleId, ?string $expiresAt = null): bool
    {
        try {
            $sql = "
                INSERT INTO user_roles (user_id, role_id, expires_at, expired_at, assigned_by, assigned_at, is_active, deleted_at)
                VALUES (:user_id, :role_id, :expires_at, :expired_at, :assigned_by, NOW(), 1, NULL)
                ON DUPLICATE KEY UPDATE
                    expires_at = VALUES(expires_at),
                    expired_at = VALUES(expired_at),
                    assigned_by = VALUES(assigned_by),
                    assigned_at = NOW(),
                    is_active = 1,
                    deleted_at = NULL
            ";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                'user_id' => $userId,
                'role_id' => $roleId,
                'expires_at' => $expiresAt,
                'expired_at' => $expiresAt,
                'assigned_by' => Auth::id(),
            ]);
        } catch (\Throwable $e) {
            // Backward compatibility for older schema without is_active/deleted_at.
            $sql = "
                INSERT INTO user_roles (user_id, role_id, expires_at)
                VALUES (:user_id, :role_id, :expires_at)
                ON DUPLICATE KEY UPDATE expires_at = VALUES(expires_at)
            ";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                'user_id' => $userId,
                'role_id' => $roleId,
                'expires_at' => $expiresAt,
            ]);
        }
    }

    /**
     * Remove a role from a user.
     */
    public function removeRole(int $userId, int $roleId): bool
    {
        // Soft revoke nếu schema có deleted_at/is_active, nếu không thì fallback hard delete.
        try {
            $sql = "
                UPDATE user_roles
                SET deleted_at = NOW(),
                    is_active = 0
                WHERE user_id = :user_id
                  AND role_id = :role_id
            ";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                'user_id' => $userId,
                'role_id' => $roleId,
            ]);
        } catch (\Throwable $e) {
            $sql = "DELETE FROM user_roles WHERE user_id = :user_id AND role_id = :role_id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                'user_id' => $userId,
                'role_id' => $roleId,
            ]);
        }
    }

    /**
     * Get all roles of a user
     */
    public function getUserRoles(int $userId): array
    {
        try {
            $sql = "SELECT r.*, COALESCE(ur.expired_at, ur.expires_at) AS expires_at, ur.is_active, ur.deleted_at
                    FROM roles r
                    JOIN user_roles ur ON r.id = ur.role_id
                    WHERE ur.user_id = :user_id
                    AND ur.deleted_at IS NULL
                    AND (ur.is_active = 1 OR ur.is_active IS NULL)
                    AND (COALESCE(ur.expired_at, ur.expires_at) IS NULL OR COALESCE(ur.expired_at, ur.expires_at) > NOW())";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['user_id' => $userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            // Backward compatibility.
            $sql = "SELECT r.*, ur.expires_at
                    FROM roles r
                    JOIN user_roles ur ON r.id = ur.role_id
                    WHERE ur.user_id = :user_id
                    AND (ur.expires_at IS NULL OR ur.expires_at > NOW())";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['user_id' => $userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    /**
     * Auto cleanup expired roles
     */
    public function cleanupExpiredRoles(): int
    {
        // Soft cleanup nếu có deleted_at/is_active, fallback hard delete.
        try {
            $sql = "
                UPDATE user_roles
                SET deleted_at = NOW(),
                    is_active = 0
                WHERE expires_at IS NOT NULL
                  AND expires_at <= NOW()
            ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->rowCount();
        } catch (\Throwable $e) {
            $sql = "DELETE FROM user_roles WHERE expires_at IS NOT NULL AND expires_at <= NOW()";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->rowCount();
        }
    }

    /**
     * @param array<int, int> $userIds
     * @return array<int, array<int, array<string, mixed>>>
     */
    public function getAssignmentsForUsers(array $userIds): array
    {
        $userIds = array_values(array_unique(array_filter(array_map('intval', $userIds), fn ($id) => $id > 0)));
        if ($userIds === []) {
            return [];
        }

        $placeholders = implode(', ', array_fill(0, count($userIds), '?'));
        $result = [];

        try {
            $sql = "
                SELECT
                    ur.user_id,
                    r.id AS role_id,
                    r.name,
                    COALESCE(r.code, r.name) AS code,
                    r.is_system,
                    r.is_active AS role_is_active,
                    COALESCE(ur.expired_at, ur.expires_at) AS expires_at,
                    ur.is_active,
                    ur.deleted_at
                FROM user_roles ur
                JOIN roles r ON r.id = ur.role_id
                WHERE ur.user_id IN ($placeholders)
                ORDER BY ur.user_id ASC, r.sort_order ASC, r.id ASC
            ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($userIds);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (\Throwable $e) {
            $sql = "
                SELECT
                    ur.user_id,
                    r.id AS role_id,
                    r.name,
                    r.name AS code,
                    r.is_system,
                    1 AS role_is_active,
                    ur.expires_at,
                    1 AS is_active,
                    NULL AS deleted_at
                FROM user_roles ur
                JOIN roles r ON r.id = ur.role_id
                WHERE ur.user_id IN ($placeholders)
                ORDER BY ur.user_id ASC, r.id ASC
            ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($userIds);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        }

        foreach ($rows as $row) {
            $userId = (int) ($row['user_id'] ?? 0);
            if (!isset($result[$userId])) {
                $result[$userId] = [];
            }

            $expiresAt = $row['expires_at'] ?? null;
            $isExpired = $expiresAt !== null && strtotime((string) $expiresAt) !== false && strtotime((string) $expiresAt) <= time();
            $isAssignmentActive = ((int) ($row['is_active'] ?? 1) === 1) && empty($row['deleted_at']) && !$isExpired;

            $result[$userId][] = [
                'role_id' => (int) ($row['role_id'] ?? 0),
                'name' => (string) ($row['name'] ?? ''),
                'code' => (string) ($row['code'] ?? ''),
                'is_system' => (int) ($row['is_system'] ?? 0) === 1,
                'role_is_active' => (int) ($row['role_is_active'] ?? 1) === 1,
                'is_assignment_active' => $isAssignmentActive,
                'expires_at' => $expiresAt,
                'is_expired' => $isExpired,
            ];
        }

        return $result;
    }
}

