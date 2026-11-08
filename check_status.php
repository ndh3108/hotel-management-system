<?php

/**
 * Quick Status Check & Update
 */

define('BASE_PATH', dirname(__FILE__));

// Database connection
$db = new PDO(
    'mysql:host=localhost;dbname=hotel;charset=utf8mb4',
    'root',
    '',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

echo "\n╔════════════════════════════════════════════════════════════════╗\n";
echo "║  BOOKING STATUS VERIFICATION & UPDATE\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

// 1. Check current bookings status
echo "1. Trạng thái hiện tại của bookings:\n";
echo "─────────────────────────────────────────────────────────────\n";

$stmt = $db->query('SELECT id, user_id, total_price, status, created_at FROM bookings LIMIT 10');
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Total: " . count($bookings) . " bookings\n\n";

foreach ($bookings as $booking) {
    printf(
        "📌 Booking #%d | Status: %-12s | Tiền: %.0f đ | User ID: %d\n",
        $booking['id'],
        $booking['status'],
        $booking['total_price'],
        $booking['user_id']
    );
}

// 2. Check status enum definition
echo "\n2. Status ENUM Definition:\n";
echo "─────────────────────────────────────────────────────────────\n";

$stmt = $db->query('SHOW CREATE TABLE bookings');
$create = $stmt->fetch(PDO::FETCH_ASSOC);
$sql = $create['Create Table'];

if (preg_match('/`status`.*?enum\(([^)]+)\)/i', $sql, $matches)) {
    $enumValues = preg_replace("/'/", '', $matches[1]);
    echo "✓ ENUM values: " . $enumValues . "\n";
} else {
    echo "✗ Could not find enum definition\n";
}

// 3. Verify database structure
echo "\n3. Database Tables Check:\n";
echo "─────────────────────────────────────────────────────────────\n";

$tables = ['notifications', 'booking_status_logs', 'audit_logs'];
$stmt = $db->query('SHOW TABLES');
$allTables = $stmt->fetchAll(PDO::FETCH_COLUMN);

foreach ($tables as $table) {
    $exists = in_array($table, $allTables);
    echo ($exists ? '✓' : '✗') . " $table\n";
}

// 4. Check notifications table
if (in_array('notifications', $allTables)) {
    echo "\n4. Notifications Table Structure:\n";
    echo "─────────────────────────────────────────────────────────────\n";

    $stmt = $db->query('SHOW COLUMNS FROM notifications');
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Columns: " . implode(', ', $columns) . "\n";

    $stmt = $db->query('SELECT COUNT(*) as count FROM notifications');
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Total notifications: " . $result['count'] . "\n";
}

// 5. Summary
echo "\n" . str_repeat('═', 64) . "\n";
echo "✓ Status verification completed\n\n";

echo "📝 Admin Can Change Status:\n";
echo "   1. Go to: http://localhost/doan/public/admin/bookings\n";
echo "   2. Click any booking to view detail\n";
echo "   3. Find 'Trạng Thái Đặt Phòng' section\n";
echo "   4. Select new status from dropdown\n";
echo "   5. Click 'Cập nhật' button\n\n";

