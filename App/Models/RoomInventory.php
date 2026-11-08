<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class RoomInventory extends Model
{
    protected string $table = 'room_inventory';

    protected array $fillable = [
        'room_id',
        'room_type_id',
        'item_id',
        'quantity',
        'price_override',
        'is_active',
        'deleted_at'
    ];

    public function getPaginatedWithRoomType(int $cursor = 0, int $limit = 10): array
    {
        $limit = max(1, $limit);
        $fetchLimit = $limit + 1;

        $sql = "
            SELECT ri.*, ii.name AS item_name, ii.default_price, rt.name AS room_type_name, r.room_number
            FROM `{$this->table}` ri
            INNER JOIN `inventory_items` ii ON ii.id = ri.item_id
            LEFT JOIN `room_types` rt ON rt.id = ri.room_type_id
            LEFT JOIN `rooms` r ON r.id = ri.room_id
            WHERE ri.deleted_at IS NULL";

        if ($cursor > 0) {
            $sql .= " AND ri.id < :cursor";
        }

        $sql .= " ORDER BY ri.id DESC LIMIT :limit_rows";

        $stmt = $this->pdo->prepare($sql);
        if ($cursor > 0) {
            $stmt->bindValue(':cursor', $cursor, PDO::PARAM_INT);
        }
        $stmt->bindValue(':limit_rows', $fetchLimit, PDO::PARAM_INT);
        $stmt->execute();

        return $this->buildCursorPage($stmt->fetchAll(PDO::FETCH_ASSOC) ?: [], $limit);
    }

    public function getByRoom(int $roomId): array
    {
        $sql = "
            SELECT ri.*, ii.name as item_name, ii.default_price
            FROM {$this->table} ri
            JOIN inventory_items ii ON ii.id = ri.item_id
            WHERE ri.room_id = ? AND ri.deleted_at IS NULL AND ri.is_active = 1
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$roomId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function getByRoomType(int $roomTypeId): array
    {
        $sql = "
            SELECT ri.*, ii.name as item_name, ii.default_price
            FROM {$this->table} ri
            JOIN inventory_items ii ON ii.id = ri.item_id
            WHERE ri.room_type_id = ? AND ri.room_id IS NULL AND ri.deleted_at IS NULL AND ri.is_active = 1
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$roomTypeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}

