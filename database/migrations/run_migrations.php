<?php

/**
 * Database Migration Runner
 * Execute SQL migration files to update database schema
 */

// Load database config
$dbConfig = require 'Config/database.php';

try {
    // Connect to database
    $dsn = "mysql:host={$dbConfig['host']}:{$dbConfig['port']};dbname={$dbConfig['dbname']};charset={$dbConfig['charset']}";
    $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], $dbConfig['options']);

    echo "✓ Connected to database: {$dbConfig['dbname']}\n";

    // Run status standardization migration
    echo "\n========================================\n";
    echo "Migration 1: Status Standardization\n";
    echo "========================================\n";

    $statusMigrationFile = 'migration_status_standardization.sql';
    if (file_exists($statusMigrationFile)) {
        $sql = file_get_contents($statusMigrationFile);

        // Split SQL by semicolon and execute each statement
        $statements = array_filter(
            array_map('trim', explode(';', $sql)),
            function ($s) {
                return !empty($s) && !str_starts_with($s, '--');
            }
        );

        foreach ($statements as $index => $statement) {
            if (str_starts_with(trim($statement), 'SELECT')) {
                // Handle SELECT statements
                $result = $pdo->query($statement);
                $rows = $result->fetchAll();
                echo "\n✓ Query Results:\n";
                foreach ($rows as $row) {
                    echo "  Status: {$row['status']}, Count: {$row['count']}\n";
                }
            } else {
                // Handle UPDATE/ALTER statements
                try {
                    $pdo->exec($statement);
                    echo "✓ Executed statement " . ($index + 1) . "\n";
                } catch (PDOException $e) {
                    if (strpos($e->getMessage(), 'already exists') !== false) {
                        echo "⚠ Skipped: Column/constraint already exists\n";
                    } else {
                        throw $e;
                    }
                }
            }
        }
        echo "\n✓ Status standardization migration completed successfully!\n";
    } else {
        echo "✗ Status migration file not found: {$statusMigrationFile}\n";
    }

    // Run notifications table migration
    echo "\n========================================\n";
    echo "Migration 2: Notifications Table\n";
    echo "========================================\n";

    $notificationsMigrationFile = 'migration_notifications_table.sql';
    if (file_exists($notificationsMigrationFile)) {
        $sql = file_get_contents($notificationsMigrationFile);

        // For CREATE TABLE, execute as a whole to preserve formatting
        try {
            $pdo->exec($sql);
            echo "✓ Notifications table created successfully\n";
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'already exists') !== false) {
                echo "⚠ Table already exists, skipping creation\n";
            } else {
                throw $e;
            }
        }
        echo "\n✓ Notifications table migration completed successfully!\n";
    } else {
        echo "✗ Notifications migration file not found: {$notificationsMigrationFile}\n";
    }

    echo "\n========================================\n";
    echo "✓ All migrations completed successfully!\n";
    echo "========================================\n";
} catch (PDOException $e) {
    echo "\n✗ Database Error: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}

