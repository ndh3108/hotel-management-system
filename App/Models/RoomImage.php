<?php

namespace App\Models;

use App\Core\Model;

class RoomImage extends Model
{
    protected string $table = 'room_images';

    protected array $fillable = [
        'room_type_id',
        'room_id',
        'image_url',
        'is_primary',
    ];

    public function deleteByRoomTypeId(int $roomTypeId): bool
    {
        $sql = "DELETE FROM `{$this->table}` WHERE `room_type_id` = ? AND `room_id` IS NULL";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$roomTypeId]);
    }

    public function deleteByRoomId(int $roomId): bool
    {
        $sql = "DELETE FROM `{$this->table}` WHERE `room_id` = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$roomId]);
    }

    public function getByRoomId(int $roomId): array
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE `room_id` = ? ORDER BY is_primary DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$roomId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    public function getByRoomTypeId(int $roomTypeId): array
    {
        $sql = "
            SELECT rv.*
            FROM `{$this->table}` rv
            JOIN bookings b ON rv.booking_id = b.id
            WHERE b.room_type_id = ?
            ORDER BY rv.created_at DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$roomTypeId]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }
}

