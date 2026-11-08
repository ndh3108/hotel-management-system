<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class UserProfile extends Model
{
    protected string $table = 'user_profiles';

    protected array $fillable = [
        'user_id',
        'avatar_url',
        'address_line',
        'ward',
        'district',
        'city',
        'country',
        'postal_code',
        'admin_note',
        'profile_completion_percent',
        'email_verified_at',
        'phone_verified_at',
        'last_login_at',
        'last_login_ip',
        'created_at',
        'updated_at',
    ];

    public function findByUserId(int $userId): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM user_profiles WHERE user_id = :user_id LIMIT 1");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}


