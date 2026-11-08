<?php

namespace App\Models;

use App\Core\Model;

class InventoryItem extends Model
{
    protected string $table = 'inventory_items';

    protected array $fillable = [
        'name',
        'default_price',
        'is_active',
        'deleted_at'
    ];

    public function getAllActive(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM {$this->table} WHERE deleted_at IS NULL ORDER BY name ASC");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }
}

