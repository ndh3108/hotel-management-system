<?php

namespace App\Services;

use App\Core\Database;
use App\Core\BookingStatus;
use PDO;

class AvailabilityService
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function projectDay(string $date): array
    {
        $sql = "
            SELECT
                r.id AS room_id,
                ? AS snapshot_date,
                COALESCE(r.display_status, LOWER(r.status)) AS status,
                SUM(CASE WHEN COALESCE(NULLIF(b.booking_status, ''), b.status) = 'pending_payment' THEN 1 ELSE 0 END) AS reserved_count,
                SUM(CASE WHEN COALESCE(NULLIF(b.booking_status, ''), b.status) IN ('checked_in','checked_out') THEN 1 ELSE 0 END) AS occupied_count,
                SUM(CASE WHEN COALESCE(r.display_status, LOWER(r.status)) = 'maintenance' THEN 1 ELSE 0 END) AS maintenance_count,
                SUM(CASE WHEN COALESCE(r.display_status, LOWER(r.status)) IN ('blocked','out_of_service','damaged') THEN 1 ELSE 0 END) AS blocked_count
            FROM rooms r
            LEFT JOIN booking_details bd ON bd.room_id = r.id
                AND bd.check_in_date < DATE_ADD(?, INTERVAL 1 DAY)
                AND bd.check_out_date > ?
            LEFT JOIN bookings b ON b.id = bd.booking_id
            GROUP BY r.id, status
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$date, $date, $date]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function persistProjection(string $date): void
    {
        $rows = $this->projectDay($date);
        $stmt = $this->pdo->prepare("
            INSERT INTO room_availability_projection
            (room_id, date, status, reserved_count, occupied_count, maintenance_count, blocked_count)
            VALUES (?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
            status = VALUES(status),
            reserved_count = VALUES(reserved_count),
            occupied_count = VALUES(occupied_count),
            maintenance_count = VALUES(maintenance_count),
            blocked_count = VALUES(blocked_count)
        ");
        foreach ($rows as $row) {
            $stmt->execute([
                (int) $row['room_id'],
                $date,
                (string) $row['status'],
                (int) $row['reserved_count'],
                (int) $row['occupied_count'],
                (int) $row['maintenance_count'],
                (int) $row['blocked_count'],
            ]);
        }
    }
}

