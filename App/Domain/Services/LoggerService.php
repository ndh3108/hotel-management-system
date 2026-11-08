<?php

namespace App\Domain\Services;

use App\Core\Database;
use PDO;

class LoggerService
{
    private ?PDO $pdo = null;
    private static string $logFile = BASE_PATH . '/storage/logs/app.log';

    public function __construct()
    {
        try {
            $this->pdo = Database::getInstance()->getConnection();
        } catch (\Throwable $e) {
            self::error("Database connection failed for LoggerService: " . $e->getMessage());
        }
    }

    /**
     * Log structured data to Database
     */
    public function log(array $data): void
    {
        if (!$this->pdo) return;

        $sql = "INSERT INTO system_logs (trace_id, actor_id, role, action, resource_type, resource_id, before_json, after_json, status, severity, ip, user_agent, created_at)
                VALUES (:trace_id, :actor_id, :role, :action, :resource_type, :resource_id, :before_json, :after_json, :status, :severity, :ip, :user_agent, :created_at)";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':trace_id' => $data['trace_id'] ?? bin2hex(random_bytes(16)),
                ':actor_id' => $data['actor_id'] ?? null,
                ':role' => $data['role'] ?? null,
                ':action' => $data['action'] ?? 'UNKNOWN',
                ':resource_type' => $data['resource_type'] ?? null,
                ':resource_id' => $data['resource_id'] ?? null,
                ':before_json' => isset($data['before']) ? json_encode($data['before'], JSON_UNESCAPED_UNICODE) : null,
                ':after_json' => isset($data['after']) ? json_encode($data['after'], JSON_UNESCAPED_UNICODE) : null,
                ':status' => $data['status'] ?? 'SUCCESS',
                ':severity' => $data['severity'] ?? 'INFO',
                ':ip' => $data['ip'] ?? $_SERVER['REMOTE_ADDR'] ?? null,
                ':user_agent' => $data['user_agent'] ?? $_SERVER['HTTP_USER_AGENT'] ?? null,
                ':created_at' => date('Y-m-d H:i:s')
            ]);
        } catch (\Throwable $e) {
            self::error("SQL Logging failed: " . $e->getMessage());
        }
    }

    /**
     * Centralized File Logging
     */
    public static function writeToFile(string $level, string $message, array $context = []): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $contextString = !empty($context) ? ' | Context: ' . json_encode($context, JSON_UNESCAPED_UNICODE) : '';
        $logEntry = "[$timestamp] [$level] $message $contextString" . PHP_EOL;
        
        file_put_contents(self::$logFile, $logEntry, FILE_APPEND);
    }

    public static function info(string $message, array $context = []): void
    {
        self::writeToFile('INFO', $message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        self::writeToFile('ERROR', $message, $context);
    }

    public static function debug(string $message, array $context = []): void
    {
        self::writeToFile('DEBUG', $message, $context);
    }

    public function getLogs(array $filters = [], int $limit = 100): array
    {
        if (!$this->pdo) return [];

        $where = ['1=1'];
        $params = [];

        if (!empty($filters['resource_type'])) {
            $where[] = 'resource_type = :resource_type';
            $params[':resource_type'] = $filters['resource_type'];
        }
        if (!empty($filters['severity'])) {
            $where[] = 'severity = :severity';
            $params[':severity'] = $filters['severity'];
        }

        $whereSql = implode(' AND ', $where);
        $sql = "SELECT sl.*, u.full_name as actor_name 
                FROM system_logs sl 
                LEFT JOIN users u ON sl.actor_id = u.id 
                WHERE {$whereSql} 
                ORDER BY sl.created_at DESC 
                LIMIT :limit";

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}


