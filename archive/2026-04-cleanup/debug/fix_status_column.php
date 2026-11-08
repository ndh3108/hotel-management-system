<?php

/**
 * Direct Database Fix
 * Fix the booking status column and data directly
 */

$dbConfig = require 'Config/database.php';

try {
    $dsn = "mysql:host={$dbConfig['host']}:{$dbConfig['port']};dbname={$dbConfig['dbname']};charset={$dbConfig['charset']}";
    $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], $dbConfig['options']);

    echo "========================================\n";
    echo "Database Status Fix\n";
    echo "========================================\n\n";

    // Step 1: First, let's see what status values we actually have
    echo "Current status values:\n";
    $currentStatuses = $pdo->query("SELECT DISTINCT status FROM bookings")->fetchAll();
    foreach ($currentStatuses as $status) {
        echo "  - '" . ($status['status'] ?? '(empty)') . "'\n";
    }
    echo "\n";

    // Step 2: Convert all to lowercase and valid enum values
    echo "Converting status values to valid lowercase enum...\n";

    // First, make it VARCHAR to allow any value
    $alterToVarchar = "ALTER TABLE `bookings` MODIFY COLUMN `status` VARCHAR(50)";
    $pdo->exec($alterToVarchar);
    echo "✓ Converted status column to VARCHAR\n";

    // Then update the values - with fallback to 'pending' if empty
    $updateStatuses = [
        "UPDATE `bookings` SET `status` = 'pending' WHERE status = 'Pending' OR status IS NULL OR status = ''",
        "UPDATE `bookings` SET `status` = 'confirmed' WHERE status = 'Confirmed'",
        "UPDATE `bookings` SET `status` = 'cancelled' WHERE status IN ('Expired', 'Cancelled')"
    ];

    foreach ($updateStatuses as $updateSql) {
        $pdo->exec($updateSql);
    }
    echo "✓ Updated all status values to lowercase\n";

    // Step 3: Verify updates
    echo "\nStatus values after update:\n";
    $updatedStatuses = $pdo->query("SELECT status, COUNT(*) as count FROM bookings GROUP BY status")->fetchAll();
    foreach ($updatedStatuses as $status) {
        echo "  - '{$status['status']}': {$status['count']} bookings\n";
    }

    // Step 4: Convert back to ENUM with new values
    echo "\nConverting back to ENUM with new values...\n";
    $alterToEnum = "ALTER TABLE `bookings` MODIFY COLUMN `status` ENUM('pending', 'processing', 'paid', 'confirmed', 'cancelled', 'completed') NOT NULL DEFAULT 'pending'";
    $pdo->exec($alterToEnum);
    echo "✓ Converted status column to correct ENUM\n";

    // Step 5: Final verification
    echo "\nFinal status values:\n";
    $finalStatuses = $pdo->query("SELECT status, COUNT(*) as count FROM bookings WHERE deleted_at IS NULL GROUP BY status ORDER BY status")->fetchAll();
    foreach ($finalStatuses as $status) {
        echo "  - '{$status['status']}': {$status['count']} bookings\n";
    }

    // Check column definition
    echo "\nColumn definition:\n";
    $columnInfo = $pdo->query("SHOW COLUMNS FROM bookings WHERE Field = 'status'")->fetch();
    echo "  Type: " . $columnInfo['Type'] . "\n";
    echo "  Default: " . $columnInfo['Default'] . "\n";

    echo "\n========================================\n";
    echo "✓ Database fix completed successfully!\n";
    echo "========================================\n";
} catch (Exception $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}

