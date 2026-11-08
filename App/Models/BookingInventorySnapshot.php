<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class BookingInventorySnapshot extends Model
{
    protected string $table = 'booking_inventory_items';

    protected array $fillable = [
        'booking_detail_id',
        'item_id',
        'item_name',
        'quantity',
        'price_at_booking'
    ];

    public function getByBookingDetailId(int $detailId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE booking_detail_id = ?");
        $stmt->execute([$detailId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}

