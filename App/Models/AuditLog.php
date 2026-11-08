<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class AuditLog extends Model
{
    protected string $table = 'audit_logs';

    /**
     * Log an action in the system
     */
    public function log(int $userId, string $action, string $tableName, ?int $recordId, ?array $oldValue, ?array $newValue, ?string $ipAddress, ?string $userAgent): bool
    {
        $sql = "INSERT INTO audit_logs (user_id, action, table_name, record_id, old_value, new_value, ip_address, user_agent) 
                VALUES (:user_id, :action, :table_name, :record_id, :old_value, :new_value, :ip_address, :user_agent)";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'user_id' => $userId,
            'action' => $action,
            'table_name' => $tableName,
            'record_id' => $recordId,
            'old_value' => $oldValue ? json_encode($oldValue, JSON_UNESCAPED_UNICODE) : null,
            'new_value' => $newValue ? json_encode($newValue, JSON_UNESCAPED_UNICODE) : null,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent
        ]);
    }

    /**
     * Get cursor logs with dynamic filters
     *
     * @return array{items: array<int, array<string, mixed>>, has_more: bool, next_cursor_id: int|null}
     */
    public function getCursorLogs(array $filters = [], ?int $cursorId = null, int $limit = 50): array
    {
        $sql = "SELECT a.*, u.full_name as user_name 
                FROM audit_logs a 
                LEFT JOIN users u ON a.user_id = u.id 
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['user_id'])) {
            $sql .= " AND a.user_id = :user_id";
            $params['user_id'] = $filters['user_id'];
        }
        
        if (!empty($filters['action'])) {
            $sql .= " AND a.action = :action";
            $params['action'] = $filters['action'];
        }
        
        if (!empty($filters['table_name'])) {
            $sql .= " AND a.table_name = :table_name";
            $params['table_name'] = $filters['table_name'];
        }
        
        if (!empty($filters['start_date'])) {
            $sql .= " AND a.created_at >= :start_date";
            $params['start_date'] = $filters['start_date'] . ' 00:00:00';
        }

        if (!empty($filters['end_date'])) {
            $sql .= " AND a.created_at <= :end_date";
            $params['end_date'] = $filters['end_date'] . ' 23:59:59';
        }

        if ($cursorId !== null && $cursorId > 0) {
            $sql .= " AND a.id < :cursor_id";
            $params['cursor_id'] = $cursorId;
        }

        $sql .= " ORDER BY a.created_at DESC, a.id DESC LIMIT " . (int) ($limit + 1);
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        $hasMore = count($rows) > $limit;
        if ($hasMore) {
            array_pop($rows);
        }

        $nextCursorId = null;
        if ($hasMore && !empty($rows)) {
            $lastRow = end($rows);
            $nextCursorId = isset($lastRow['id']) ? (int) $lastRow['id'] : null;
        }

        return [
            'items' => $rows,
            'has_more' => $hasMore,
            'next_cursor_id' => $nextCursorId,
        ];
    }
}

