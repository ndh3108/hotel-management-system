<?php

namespace App\Domain\Services;

use App\Core\Database;
use App\Models\Room;
use App\Services\AvailabilityService;
use PDO;

class CronService
{
    private PDO $pdo;
    private AvailabilityService $availabilityService;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
        $this->availabilityService = new AvailabilityService();
    }

    /**
     * Chạy các tác vụ background ngầm (Pseudo-Cron)
     */
    public function run(): void
    {
        // 1. Nhả phòng Hold sau 15 phút không thanh toán / xác nhận
        $this->releaseExpiredHolds();
        $this->expirePendingBookings();

        // 2. Snapshot availability theo giờ và theo ngày để dashboard realtime ổn định.
        if ($this->shouldRun('availability_hourly_snapshot', 60)) {
            $this->availabilityService->persistProjection(date('Y-m-d'));
            $this->markRun('availability_hourly_snapshot');
        }
        if ($this->shouldRun('availability_next_day_snapshot', 1440)) {
            $this->availabilityService->persistProjection(date('Y-m-d', strtotime('+1 day')));
            $this->markRun('availability_next_day_snapshot');
        }
    }

    private function releaseExpiredHolds(): void
    {
        // Find rooms with status 'Hold' and locked_at < NOW() - 10 MIN
        // Assuming locked_at is the time it was held
        $stmt = $this->pdo->query("
            SELECT id FROM rooms 
            WHERE status = 'Hold' 
            AND locked_at IS NOT NULL 
            AND locked_at < DATE_SUB(NOW(), INTERVAL 10 MINUTE)
        ");
        $expiredRooms = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (!empty($expiredRooms)) {
            $ids = implode(',', array_map('intval', $expiredRooms));
            $this->pdo->exec("
                UPDATE rooms 
                SET status = 'Available', locked_at = NULL, locked_by_session = NULL 
                WHERE id IN ($ids)
            ");
            
            // Xóa các booking nháp liên kết với phiên Hold này (nếu có)
            // (Tuỳ logic, hiện tại room nhả Hold là đủ cho booking engine)
        }
    }

    private function expirePendingBookings(): void
    {
        $this->pdo->exec("
            UPDATE bookings 
            SET status = 'expired',
                booking_status = 'expired',
                payment_status = 'overdue'
            WHERE COALESCE(NULLIF(booking_status, ''), status) IN ('pending', 'processing', 'pending_payment')
            AND created_at < DATE_SUB(NOW(), INTERVAL 5 MINUTE)
        ");
    }

    private function shouldRun(string $jobName, int $minIntervalMinutes): bool
    {
        $stmt = $this->pdo->prepare("
            SELECT created_at
            FROM system_logs
            WHERE level = 'CRON' AND message = :message
            ORDER BY id DESC
            LIMIT 1
        ");
        $stmt->execute([':message' => $jobName]);
        $lastRunAt = $stmt->fetchColumn();
        if (!$lastRunAt) {
            return true;
        }
        return (time() - strtotime((string) $lastRunAt)) >= ($minIntervalMinutes * 60);
    }

    private function markRun(string $jobName): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO system_logs (level, message, context, created_at)
            VALUES ('CRON', :message, :context, NOW())
        ");
        $stmt->execute([
            ':message' => $jobName,
            ':context' => json_encode(['job' => $jobName, 'at' => date('c')], JSON_UNESCAPED_UNICODE),
        ]);
    }
}

