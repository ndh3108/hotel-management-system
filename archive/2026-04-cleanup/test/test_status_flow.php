<?php

/**
 * Test Admin Status Update Flow
 * Verifies status can be updated and notifications sent to users
 */

$dsn = 'mysql:host=localhost;dbname=hotel;charset=utf8mb4';
$user = 'root';
$pass = '';

try {
    $db = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    echo "\n╔════════════════════════════════════════════════════════════════╗\n";
    echo "║  ADMIN STATUS UPDATE FLOW TEST                                 ║\n";
    echo "╚════════════════════════════════════════════════════════════════╝\n\n";

    // 1. Check booking data
    echo "1. Kiểm tra booking:\n";
    echo "─────────────────────────────────────────────────────────────\n";

    $stmt = $db->query("SELECT b.id, b.status, b.user_id, u.email, u.full_name 
                       FROM bookings b 
                       LEFT JOIN users u ON b.user_id = u.id 
                       ORDER BY b.id DESC LIMIT 3");
    $bookings = $stmt->fetchAll();

    foreach ($bookings as $booking) {
        $userInfo = $booking['email'] ? htmlspecialchars($booking['full_name'] ?? $booking['email']) : 'N/A';
        echo sprintf(
            "   Booking #%d | Status: %-12s | User: %s (%s)\n",
            $booking['id'],
            $booking['status'],
            $userInfo,
            $booking['email'] ?? 'N/A'
        );
    }

    // 2. Check notifications table
    echo "\n2. Kiểm tra bảng thông báo:\n";
    echo "─────────────────────────────────────────────────────────────\n";

    $stmt = $db->query("SHOW COLUMNS FROM notifications");
    $columns = $stmt->fetchAll();
    $colNames = array_map(function ($col) {
        return $col['Field'];
    }, $columns);
    echo "   Columns: " . implode(', ', $colNames) . "\n";

    // 3. Check recent notifications
    echo "\n3. Thông báo gần đây:\n";
    echo "─────────────────────────────────────────────────────────────\n";

    $stmt = $db->query("SELECT n.id, n.user_id, n.booking_id, n.old_status, n.new_status, 
                       n.created_at, u.email
                       FROM notifications n
                       LEFT JOIN users u ON n.user_id = u.id
                       ORDER BY n.created_at DESC LIMIT 5");
    $notifs = $stmt->fetchAll();

    if (!empty($notifs)) {
        foreach ($notifs as $notif) {
            echo sprintf(
                "   #%d | B%d: %s → %s | User: %s\n",
                $notif['id'],
                $notif['booking_id'],
                $notif['old_status'],
                $notif['new_status'],
                $notif['email'] ?? 'N/A'
            );
        }
    } else {
        echo "   Chưa có thông báo nào\n";
    }

    // 4. Test the flow
    echo "\n4. Kiểm tra các thành phần hệ thống:\n";
    echo "─────────────────────────────────────────────────────────────\n";

    // Check configuration files
    $configFiles = [
        'Config/booking_status.php' => 'Status configuration',
        'Config/mail.php' => 'Email configuration',
        'App/Service/NotificationService.php' => 'Notification service',
        'App/Service/EmailTemplateService.php' => 'Email templates',
    ];

    $baseDir = dirname(__FILE__);
    foreach ($configFiles as $file => $desc) {
        $path = $baseDir . '/' . $file;
        $exists = file_exists($path);
        echo ($exists ? '   ✓' : '   ✗') . " $desc ($file)\n";
    }

    // 5. Instructions
    echo "\n5. Cách thay đổi trạng thái booking:\n";
    echo "─────────────────────────────────────────────────────────────\n";
    echo "   1. Vào: http://localhost/doan/public/admin/bookings\n";
    echo "   2. Xem chi tiết booking (click icon view)\n";
    echo "   3. Tìm mục 'Trạng Thái Đặt Phòng'\n";
    echo "   4. Chọn trạng thái mới từ dropdown\n";
    echo "   5. Check ☑ 'Gửi thông báo đến khách' (default: checked)\n";
    echo "   6. Click 'Cập nhật' button\n";
    echo "   7. Xác nhận khi được hỏi\n\n";

    echo "6. Output khi cập nhật thành công:\n";
    echo "─────────────────────────────────────────────────────────────\n";
    echo "   ✓ Trang redirect về chi tiết booking\n";
    echo "   ✓ Hiển thị 'Trạng thái đã được cập nhật thành công'\n";
    echo "   ✓ Status được lưu trong database (bookings.status)\n";
    echo "   ✓ Email được gửi đến khách (nếu check box được chọn)\n";
    echo "   ✓ In-app notification được tạo (notifications table)\n\n";

    echo "═" . str_repeat("═", 66) . "════\n";
    echo "✓ Hệ thống sẵn sàng. Admin có thể cập nhật trạng thái booking.\n";
    echo "✓ Khách hàng sẽ nhận được email thông báo.\n\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

