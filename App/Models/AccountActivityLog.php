<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class AccountActivityLog extends Model
{
    protected string $table = 'account_activity_logs';

    protected array $fillable = [
        'user_id',
        'action',
        'description',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    public function getRecentByUserId(int $userId, int $limit = 10): array
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM account_activity_logs
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


