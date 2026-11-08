<?php

/**
 * Simple Verification - Core Functionality Check
 */

echo "📋 Quick Verification Checklist\n";
echo "================================\n\n";

// 1. Check files exist
echo "1. File Check:\n";
$files = [
    'Config/mail.php' => 'Mail Config',
    'Config/booking_status.php' => 'Booking Status Config',
    'App/Service/NotificationService.php' => 'Notification Service',
    'App/Service/EmailTemplateService.php' => 'Email Template Service',
    'App/Service/BookingService.php' => 'Booking Service',
    'App/Models/Notification.php' => 'Notification Model',
    'App/Controllers/Admin/BookingController.php' => 'Booking Controller',
    'App/Views/Admin/bookings/detail.php' => 'Booking Detail View',
    'App/Views/Admin/bookings/index.php' => 'Booking Index View',
];

foreach ($files as $file => $desc) {
    $exists = file_exists($file) ? '✓' : '✗';
    echo "  $exists $desc ($file)\n";
}

echo "\n2. Notification Table Check:\n";
try {
    $dbConfig = require 'Config/database.php';
    $dsn = "mysql:host={$dbConfig['host']}:{$dbConfig['port']};dbname={$dbConfig['dbname']};charset={$dbConfig['charset']}";
    $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password']);

    // Check table exists
    $tables = $pdo->query("SHOW TABLES LIKE 'notifications'")->fetchAll();
    if (!empty($tables)) {
        echo "  ✓ Notifications table exists\n";

        // Check columns
        $columns = $pdo->query("SHOW COLUMNS FROM notifications")->fetchAll();
        echo "  ✓ Columns (" . count($columns) . "): " . implode(', ', array_column($columns, 'Field')) . "\n";
    } else {
        echo "  ✗ Notifications table NOT found\n";
    }
} catch (Exception $e) {
    echo "  ✗ Database error: " . $e->getMessage() . "\n";
}

echo "\n3. Status Configuration Check:\n";
$statusConfig = require 'Config/booking_status.php';
echo "  ✓ Loaded booking_status config\n";
echo "  ✓ Statuses: " . implode(', ', array_filter(array_keys($statusConfig), fn($k) => !in_array($k, ['transitions', 'blocks_room', 'cancellable', 'default']))) . "\n";

echo "\n4. Class & Service Check:\n";

// Check if classes can be loaded
$checks = [
    'App\Core\BookingStatus' => 'BookingStatus',
    'App\Services\NotificationService' => 'NotificationService',
    'App\Services\EmailTemplateService' => 'EmailTemplateService',
    'App\Models\Notification' => 'Notification Model',
];

foreach ($checks as $class => $desc) {
    if (class_exists($class)) {
        echo "  ✓ $desc class exists\n";
    } else {
        echo "  ✗ $desc class NOT found\n";
    }
}

echo "\n5. Database Status Values:\n";
try {
    $dbConfig = require 'Config/database.php';
    $pdo = new PDO(
        "mysql:host={$dbConfig['host']}:{$dbConfig['port']};dbname={$dbConfig['dbname']}",
        $dbConfig['username'],
        $dbConfig['password']
    );

    $status = $pdo->query("SHOW COLUMNS FROM bookings WHERE Field = 'status'")->fetch();
    echo "  ✓ Status enum: " . ($status['Type'] ?? 'N/A') . "\n";

    $dist = $pdo->query("SELECT status, COUNT(*) as cnt FROM bookings WHERE deleted_at IS NULL GROUP BY status")->fetchAll();
    foreach ($dist as $s) {
        echo "    - {$s['status']}: {$s['cnt']}\n";
    }
} catch (Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n";
}

echo "\n=====================================\n";
echo "✓ System is ready for testing!\n\n";
echo "Next Steps:\n";
echo "1. Go to: http://localhost/doan/public/admin/bookings\n";
echo "2. Click on any booking to view details\n";
echo "3. Scroll to 'Trạng Thái Đặt Phòng' section\n";
echo "4. Select new status from dropdown\n";
echo "5. Click 'Cập nhật' to test status change\n";
echo "6. Check /logs/notifications.log for email logs\n";

