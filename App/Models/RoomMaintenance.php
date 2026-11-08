<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class RoomMaintenance extends Model
{
    protected string $table = 'room_maintenance';

    protected array $fillable = [
        'room_id',
        'maintenance_date',
        'description',
        'performed_by',
        'cost',
        'priority',
        'status'
    ];

    public function getByRoom(int $roomId): array
    {
        $sql = "
            SELECT rm.*, u.full_name as performer_name
            FROM {$this->table} rm
            LEFT JOIN users u ON u.id = rm.performed_by
            WHERE rm.room_id = ?
            ORDER BY rm.maintenance_date DESC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$roomId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}

