<?php
require 'vendor/autoload.php';
try {
    $pdo = new PDO('mysql:host=localhost;dbname=hotel', 'root', '');
    $tables = ['room_types', 'room_images', 'services', 'service_types', 'images', 'room_type_inventory'];
    foreach ($tables as $table) {
        echo "\n--- $table ---\n";
        $stmt = $pdo->query("DESCRIBE $table");
        if ($stmt) {
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
                echo "{$r['Field']} ({$r['Type']}) {$r['Extra']}\n";
            }
        } else {
            echo "Table not found\n";
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

