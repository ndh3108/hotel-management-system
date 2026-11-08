<?php
/**
 * Cron Job: Hủy các đơn hàng chưa thanh toán quá hạn
 * Được thiết kế để chạy tự động mỗi phút qua luồng Crontab.
 */

// Đổi đường dẫn cho phù hợp tuỳ vị trí chạy script
$baseDir = __DIR__ . '/../';
require_once $baseDir . 'vendor/autoload.php';

use App\Core\BookingStatus;

try {
    $pdo = new PDO('mysql:host=localhost;dbname=hotel;charset=utf8mb4', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Lấy các booking đã hết hạn
    // Chú ý: Bảng bookings có quan hệ khoá ngoại với booking_details. 
    // Nếu ON DELETE CASCADE đã được cấu hình, chỉ cần DELETE.
    // Xoá booking đã quá hạn
    $stmt1 = $pdo->prepare("
        DELETE FROM bookings 
        WHERE status = :status 
        AND expired_at IS NOT NULL 
        AND expired_at < NOW()
    ");
    $stmt1->execute(['status' => BookingStatus::PENDING_PAYMENT]);
    $deletedBookings = $stmt1->rowCount();

    // Xoá service_bookings quá hạn 
    $stmt2 = $pdo->prepare("
        DELETE FROM service_bookings 
        WHERE status = :status 
        AND expired_at IS NOT NULL 
        AND expired_at < NOW()
    ");
    $stmt2->execute(['status' => BookingStatus::PENDING_PAYMENT]);
    $deletedServices = $stmt2->rowCount();

    echo "[" . date('Y-m-d H:i:s') . "] Removed $deletedBookings expired room bookings and $deletedServices expired service bookings.\n";

} catch (Exception $e) {
    echo "[" . date('Y-m-d H:i:s') . "] Error canceling unpaid bookings: " . $e->getMessage() . "\n";
}

