<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Attraction extends Model
{
    protected string $table = 'attractions';

    protected array $fillable = [
        'name',
        'type',
        'distance_km',
        'description',
        'image_url',
    ];

    public function getAllOrdered(): array
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY distance_km ASC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}

