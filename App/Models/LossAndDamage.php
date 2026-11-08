<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class LossAndDamage extends Model
{
    protected string $table = 'loss_and_damages';

    protected array $fillable = [
        'booking_id',
        'room_id',
        'item_name',
        'quantity',
        'penalty_amount',
        'notes'
    ];

    public function getByBookingId(int $bookingId): array
    {
        $sql = "
            SELECT *
            FROM `{$this->table}`
            WHERE booking_id = ?
            ORDER BY created_at DESC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$bookingId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}

