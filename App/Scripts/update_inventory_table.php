<?php
require_once __DIR__ . '/../../App/Core/Database.php';
$pdo = \App\Core\Database::getInstance()->getConnection();

try {
    $pdo->exec("ALTER TABLE inventory_items ADD COLUMN category VARCHAR(100) AFTER name");
    echo "Added category\n";
} catch (Exception $e) {}

try {
    $pdo->exec("ALTER TABLE inventory_items ADD COLUMN unit VARCHAR(50) AFTER category");
    echo "Added unit\n";
} catch (Exception $e) {}

try {
    $pdo->exec("ALTER TABLE inventory_items ADD COLUMN stock_quantity INT DEFAULT 0 AFTER unit");
    echo "Added stock_quantity\n";
} catch (Exception $e) {}

