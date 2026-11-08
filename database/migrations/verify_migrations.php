<?php

/**
 * Database Verification Script
 * Verify that migrations were applied correctly
 */

$dbConfig = require 'Config/database.php';

try {
    $dsn = "mysql:host={$dbConfig['host']}:{$dbConfig['port']};dbname={$dbConfig['dbname']};charset={$dbConfig['charset']}";
    $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], $dbConfig['options']);

    echo "========================================\n";
    echo "Database Verification Report\n";
    echo "========================================\n\n";

    // 1. Check booking status enum values
    echo "1. Booking Status Enum Values:\n";
    echo "   Expected: pending, processing, paid, confirmed, cancelled, completed\n";
    $result = $pdo->query("SHOW COLUMNS FROM bookings WHERE Field = 'status'")->fetch();
    echo "   Current: " . (isset($result['Type']) ? $result['Type'] : 'N/A') . "\n\n";

    // 2. Check booking status distribution
    echo "2. Booking Status Distribution:\n";
    $statuses = $pdo->query("SELECT status, COUNT(*) as count FROM bookings WHERE deleted_at IS NULL GROUP BY status ORDER BY status")->fetchAll();
    foreach ($statuses as $status) {
        echo "   - {$status['status']}: {$status['count']} bookings\n";
    }
    echo "\n";

    // 3. Check notifications table exists
    echo "3. Notifications Table:\n";
    $tables = $pdo->query("SHOW TABLES LIKE 'notifications'")->fetchAll();
    if (!empty($tables)) {
        echo "   ✓ Table exists\n";
        echo "   ✓ Columns: ";
        $columns = $pdo->query("SHOW COLUMNS FROM notifications")->fetchAll();
        echo implode(', ', array_column($columns, 'Field')) . "\n";
        echo "   ✓ Row count: " . $pdo->query("SELECT COUNT(*) as count FROM notifications")->fetch()['count'] . " notifications\n";
    } else {
        echo "   ✗ Table does not exist\n";
    }
    echo "\n";

    // 4. Check for inconsistent status values
    echo "4. Status Consistency Check:\n";
    $invalidStatuses = $pdo->query("SELECT status, COUNT(*) as count FROM bookings WHERE status NOT IN ('pending', 'processing', 'paid', 'confirmed', 'cancelled', 'completed') AND deleted_at IS NULL GROUP BY status")->fetchAll();

    if (empty($invalidStatuses)) {
        echo "   ✓ All statuses are valid (no capitalized values found)\n";
    } else {
        echo "   ⚠ Found invalid status values:\n";
        foreach ($invalidStatuses as $status) {
            echo "     - {$status['status']}: {$status['count']} bookings\n";
        }
    }
    echo "\n";

    // 5. Verify notification indexes
    echo "5. Notification Indexes:\n";
    $indexes = $pdo->query("SHOW INDEXES FROM notifications")->fetchAll();
    echo "   Indexes: " . count($indexes) . "\n";
    foreach ($indexes as $idx) {
        if ($idx['Key_name'] !== 'PRIMARY') {
            echo "   - {$idx['Key_name']} on {$idx['Column_name']}\n";
        }
    }

    echo "\n========================================\n";
    echo "✓ Database verification completed\n";
    echo "========================================\n";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}

