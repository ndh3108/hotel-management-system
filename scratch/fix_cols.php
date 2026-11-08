<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=hotel;charset=utf8mb4', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    $pdo->exec("ALTER TABLE bookings DROP COLUMN expires_at");
    $pdo->exec("ALTER TABLE service_bookings CHANGE expires_at expired_at DATETIME NULL");
    echo "Columns fixed";

} catch (PDOException $e) {
    echo "Query Error: " . $e->getMessage() . "\n";
}

