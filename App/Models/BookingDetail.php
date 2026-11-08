<?php

namespace App\Models;

use App\Core\Model;
use App\Core\BookingStatus;
use PDO;

/**
 * Model BookingDetail - Bảng booking_details.
 * Overlap check: dùng Service/BookingService::getOverlappingBookings (SQL interval overlap).
 */
class BookingDetail extends Model
{
    protected string $table = 'booking_details';

    protected array $fillable = [
        'booking_id',
        'room_id',
        'check_in_date',
        'check_out_date',
        'price_per_night',
    ];

    /**
     * Kiểm tra room_id có bị trùng lịch trong [check_in, check_out] không.
     * Thực tế gọi BookingService::isRoomAvailable; giữ method này để dùng từ Model nếu cần.
     */
    public function hasOverlap(int $room_id, string $check_in, string $check_out, ?int $exclude_booking_id = null): bool
    {
        $blockStatuses = BookingStatus::statusesThatBlockRoom();
        $placeholders = implode(',', array_fill(0, count($blockStatuses), '?'));
        $sql = "
            SELECT 1
            FROM bookings b
            JOIN booking_details bd ON b.id = bd.booking_id
            WHERE bd.room_id = ?
            AND b.status IN ($placeholders)
            AND bd.check_in_date < ?
            AND bd.check_out_date > ?
        ";
        $bind = array_merge([$room_id], $blockStatuses, [$check_out, $check_in]);
        if ($exclude_booking_id !== null) {
            $sql .= " AND b.id != ?";
            $bind[] = $exclude_booking_id;
        }
        $sql .= " LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($bind);
        return (bool) $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

