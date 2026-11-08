<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class UserSessionLog extends Model
{
    protected string $table = 'user_session_logs';

    protected array $fillable = [
        'user_id',
        'session_id',
        'ip_address',
        'user_agent',
        'last_activity_at',
        'logged_out_at',
        'is_current',
        'created_at',
    ];

    public function findActiveBySessionId(string $sessionId): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM user_session_logs
            WHERE session_id = :session_id
              AND logged_out_at IS NULL
            ORDER BY id DESC
            LIMIT 1
        ");
        $stmt->execute(['session_id' => $sessionId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function findLatestBySessionId(string $sessionId): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM user_session_logs
            WHERE session_id = :session_id
            ORDER BY id DESC
            LIMIT 1
        ");
        $stmt->execute(['session_id' => $sessionId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getRecentByUserId(int $userId, int $limit = 10): array
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM user_session_logs
            WHERE user_id = :user_id
            ORDER BY id DESC
            LIMIT :limit_rows
        ");
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit_rows', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}


