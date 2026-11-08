<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class InventoryDamage extends Model
{
    protected string $table = 'inventory_damage';

    protected array $fillable = [
        'booking_id',
        'room_id',
        'booking_detail_id',
        'item_id',
        'item_name_at_report',
        'quantity',
        'price',
        'price_at_report',
        'price_at_approval',
        'status',
        'is_fixed',
        'is_billed',
        'reported_by',
        'approved_by',
        'notes'
    ];

    public const STATUS_REPORTED = 'Reported';
    public const STATUS_REVIEWED = 'Reviewed';
    public const STATUS_APPROVED = 'Approved';
    public const STATUS_REJECTED = 'Rejected';
    public const STATUS_BILLED   = 'Billed';

    public function getByBookingDetailId(int $detailId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT idmg.*, ii.name as catalog_name
            FROM {$this->table} idmg
            LEFT JOIN inventory_items ii ON ii.id = idmg.item_id
            WHERE idmg.booking_detail_id = ?
            ORDER BY idmg.created_at DESC
        ");
        $stmt->execute([$detailId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Lấy tổng giá trị hư tổn ĐÃ DUYỆT nhưng CHƯA TÍNH TIỀN (is_billed = 0)
     */
    public function getApprovedUnbilledTotal(int $bookingId): float
    {
        $sql = "
            SELECT SUM(price_at_approval * quantity) 
            FROM {$this->table}
            WHERE booking_id = ? 
              AND status = 'Approved' 
              AND is_billed = 0
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$bookingId]);
        return (float) ($stmt->fetchColumn() ?: 0.0);
    }

    /**
     * Mark damages as billed within the checkout transaction
     */
    public function markAsBilled(int $bookingId): bool
    {
        $sql = "
            UPDATE {$this->table} 
            SET is_billed = 1, status = 'Billed'
            WHERE booking_id = ? 
              AND status = 'Approved' 
              AND is_billed = 0
        ";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$bookingId]);
    }
}

