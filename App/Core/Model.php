<?php

namespace App\Core;

use PDO;
use PDOException;

/**
 * Model cha - Tất cả model trong App\Models kế thừa từ class này.
 * Kết nối DB qua Database::getInstance(), cung cấp CRUD chuẩn.
 */
abstract class Model
{
    protected PDO $pdo;

    /** Tên bảng (bắt buộc khai báo ở model con) */
    protected string $table = '';

    /** Khóa chính, mặc định 'id' */
    protected string $primaryKey = 'id';

    /** Các cột được phép gán hàng loạt (create/update) */
    protected array $fillable = [];

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    /**
     * Danh sách tất cả bản ghi
     * @return array<int, array>
     */
    public function getAll(): array
    {
        $sql = "SELECT * FROM `{$this->table}`";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Chi tiết một bản ghi theo id
     * @param int|string $id
     * @return array<string, mixed>|null
     */
    public function findById($id): ?array
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE `{$this->primaryKey}` = ? LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Thêm bản ghi mới. Chỉ gán các cột trong $fillable.
     * @param array<string, mixed> $data
     * @return int|false Last insert id hoặc false khi thất bại
     */
    public function create(array $data)
    {
        $data = $this->filterFillable($data);
        if (empty($data)) {
            return false;
        }
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');
        $sql = sprintf(
            'INSERT INTO `%s` (`%s`) VALUES (%s)',
            $this->table,
            implode('`, `', $columns),
            implode(', ', $placeholders)
        );
        $stmt = $this->pdo->prepare($sql);
        $ok = $stmt->execute(array_values($data));
        
        if ($ok && $this->table !== 'audit_logs' && class_exists('\App\Domain\Services\AuditLogService')) {
            $id = (int) $this->pdo->lastInsertId();
            $logger = new \App\Domain\Services\AuditLogService();
            $logger->logAction('CREATE', $this->table, $id, null, $data);
            return $id;
        }
        
        return $ok ? (int) $this->pdo->lastInsertId() : false;
    }

    /**
     * Cập nhật bản ghi theo id. Chỉ cập nhật các cột trong $fillable.
     * @param int|string $id
     * @param array<string, mixed> $data
     * @return bool
     */
    public function update($id, array $data): bool
    {
        $data = $this->filterFillable($data);
        if (empty($data)) {
            return false;
        }
        
        $oldValue = null;
        if ($this->table !== 'audit_logs' && class_exists('\App\Domain\Services\AuditLogService')) {
            $oldRow = $this->findById($id);
            $oldValue = is_array($oldRow) ? $oldRow : null;
        }

        $set = [];
        foreach (array_keys($data) as $col) {
            $set[] = "`{$col}` = ?";
        }
        $sql = sprintf(
            'UPDATE `%s` SET %s WHERE `%s` = ?',
            $this->table,
            implode(', ', $set),
            $this->primaryKey
        );
        $stmt = $this->pdo->prepare($sql);
        $params = array_values($data);
        $params[] = $id;
        
        $ok = $stmt->execute($params);
        if ($ok && $this->table !== 'audit_logs' && class_exists('\App\Domain\Services\AuditLogService')) {
            $logger = new \App\Domain\Services\AuditLogService();
            $logger->logAction('UPDATE', $this->table, (int)$id, $oldValue, $data);
        }
        return $ok;
    }

    /**
     * Xóa bản ghi theo id
     * @param int|string $id
     * @return bool
     */
    public function delete($id): bool
    {
        $oldValue = null;
        if ($this->table !== 'audit_logs' && class_exists('\App\Domain\Services\AuditLogService')) {
            $oldRow = $this->findById($id);
            $oldValue = is_array($oldRow) ? $oldRow : null;
        }
        
        $sql = "DELETE FROM `{$this->table}` WHERE `{$this->primaryKey}` = ?";
        $stmt = $this->pdo->prepare($sql);
        $ok = $stmt->execute([$id]);
        
        if ($ok && $this->table !== 'audit_logs' && class_exists('\App\Domain\Services\AuditLogService')) {
            $logger = new \App\Domain\Services\AuditLogService();
            $logger->logAction('DELETE', $this->table, (int)$id, $oldValue, null);
        }
        return $ok;
    }

    /**
     * Chỉ giữ lại các key nằm trong $fillable
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    protected function filterFillable(array $data): array
    {
        if (empty($this->fillable)) {
            return $data;
        }
        return array_intersect_key($data, array_flip($this->fillable));
    }

    /**
     * Count total records in the table
     * @return int
     */
    public function count(): int
    {
        $sql = "SELECT COUNT(*) FROM `{$this->table}`";
        $stmt = $this->pdo->query($sql);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Normalize keyset pagination result.
     *
     * @param array<int, array<string, mixed>> $rows
     * @return array{items: array<int, array<string, mixed>>, has_more: bool, next_last_id: int|null}
     */
    protected function buildCursorPage(array $rows, int $limit, string $idKey = 'id'): array
    {
        $hasMore = count($rows) > $limit;
        if ($hasMore) {
            array_pop($rows);
        }

        $nextLastId = null;
        if ($hasMore && !empty($rows)) {
            $lastRow = end($rows);
            $nextLastId = isset($lastRow[$idKey]) ? (int) $lastRow[$idKey] : null;
        }

        return [
            'items' => $rows,
            'has_more' => $hasMore,
            'next_last_id' => $nextLastId,
        ];
    }

    /**
     * Normalize offset-based pagination result.
     *
     * @param array<int, array<string, mixed>> $rows
     * @return array{items: array<int, array<string, mixed>>, current_page: int, per_page: int, total_items: int, total_pages: int}
     */
    protected function buildPageResult(array $rows, int $page, int $perPage, int $totalItems): array
    {
        $perPage = max(1, $perPage);
        $totalItems = max(0, $totalItems);
        $totalPages = max(1, (int) ceil($totalItems / $perPage));
        $page = max(1, min($page, $totalPages));

        return [
            'items' => $rows,
            'current_page' => $page,
            'per_page' => $perPage,
            'total_items' => $totalItems,
            'total_pages' => $totalPages,
        ];
    }

    /**
     * Get the PDO connection
     * @return PDO
     */
    public function getConnection(): PDO
    {
        return $this->pdo;
    }

    // Transaction Wrappers
    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    public function rollBack(): bool
    {
        return $this->pdo->rollBack();
    }
}

