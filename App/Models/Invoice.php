<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Invoice extends Model
{
    protected string $table = 'invoices';

    protected array $fillable = [
        'booking_id',
        'room_amount',
        'service_amount',
        'addon_amount',
        'late_fee',
        'damage_fee',
        'discount_amount',
        'prepaid_amount',
        'final_total',
        'balance_due',
        'status',
    ];

    public function findLatestByBookingId(int $bookingId): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM invoices WHERE booking_id = ? ORDER BY id DESC LIMIT 1");
        $stmt->execute([$bookingId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
}

