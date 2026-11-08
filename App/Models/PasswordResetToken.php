<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class PasswordResetToken extends Model
{
    protected string $table = 'password_reset_tokens';

    protected array $fillable = [
        'user_id',
        'token',
        'expires_at',
        'used_at',
        'created_at',
    ];

    public function findValidToken(string $token): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM password_reset_tokens
            WHERE token = :token
              AND used_at IS NULL
              AND expires_at > NOW()
            ORDER BY id DESC
            LIMIT 1
        ");
        $stmt->execute(['token' => $token]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}


