<?php

namespace App\Domain\Services;

class AuditLogService
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = \App\Core\Database::getInstance()->getConnection();
    }

    /**
     * Tự động lưu log nếu HTTP method là POST, PUT, DELETE 
     * Hoặc có thể gọi thủ công khi thay đổi dữ liệu quan trọng
     */
    public function logAction($action, $table, $entityId = null, $userId = null, $data = [])
    {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO audit_logs (action, table_name, entity_id, user_id, data, created_at)
                VALUES (:action, :table_name, :entity_id, :user_id, :data, NOW())
            ");

            $stmt->execute([
                'action' => $action,
                'table_name' => $table,
                'entity_id' => $entityId,
                'user_id' => $userId,
                'data' => json_encode($data)
            ]);

        } catch (\Exception $e) {
            error_log('Audit log error: ' . $e->getMessage());
        }
    }
    
    public function getLogs(array $filters = [], int $page = 1, int $limit = 50): array
    {
        $auditLogModel = new \App\Models\AuditLog();
        $offset = ($page - 1) * $limit;
        return $auditLogModel->getLogs($filters, $limit, $offset);
    }

    public function getCursorLogs(array $filters = [], ?int $cursorId = null, int $limit = 50): array
    {
        $auditLogModel = new \App\Models\AuditLog();
        return $auditLogModel->getCursorLogs($filters, $cursorId, $limit);
    }
}

