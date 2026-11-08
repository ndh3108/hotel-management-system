<?php
require_once __DIR__ . '/../../App/Core/Database.php';
$pdo = \App\Core\Database::getInstance()->getConnection();

try {
    $pdo->exec("ALTER TABLE inventory_damage MODIFY COLUMN status ENUM('Reported','Reviewed','Approved','Rejected','Paid','Replaced') DEFAULT 'Reported'");
    echo "Updated inventory_damage status ENUM successfully.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

