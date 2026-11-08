<?php
require_once __DIR__ . '/../vendor/autoload.php';

$pdo = new PDO('mysql:host=localhost;dbname=hotel;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

try {
    // 1. Create late_checkout_rules
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS late_checkout_rules (
            id INT AUTO_INCREMENT PRIMARY KEY,
            from_hour INT NOT NULL,
            to_hour INT DEFAULT NULL,
            percent INT NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    $stmt = $pdo->query("SELECT COUNT(*) FROM late_checkout_rules");
    if ($stmt->fetchColumn() == 0) {
        $pdo->exec("
            INSERT INTO late_checkout_rules (from_hour, to_hour, percent) VALUES 
            (12, 15, 30),
            (15, 18, 50),
            (18, NULL, 100)
        ");
    }

    $pdo->exec("ALTER TABLE bookings MODIFY status VARCHAR(50) DEFAULT 'pending_payment'");
    $pdo->exec("ALTER TABLE service_bookings MODIFY status VARCHAR(50) DEFAULT 'pending_payment'");
    
    $pdo->exec("UPDATE bookings SET status='pending_payment' WHERE status='pending' OR status='PENDING_PAYMENT'");
    $pdo->exec("UPDATE bookings SET status='confirmed' WHERE status IN ('processing', 'paid', 'confirmed', 'CONFIRMED')");
    $pdo->exec("UPDATE bookings SET status='cancelled' WHERE status IN ('cancelled', 'CANCELLED')");
    $pdo->exec("UPDATE bookings SET status='completed' WHERE status IN ('completed', 'COMPLETED')");

    $pdo->exec("UPDATE service_bookings SET status='pending_payment' WHERE status='pending' OR status='PENDING_PAYMENT'");
    $pdo->exec("UPDATE service_bookings SET status='confirmed' WHERE status IN ('processing', 'paid', 'confirmed', 'CONFIRMED')");
    $pdo->exec("UPDATE service_bookings SET status='cancelled' WHERE status IN ('cancelled', 'CANCELLED')");
    $pdo->exec("UPDATE service_bookings SET status='completed' WHERE status IN ('completed', 'COMPLETED')");

    $pdo->exec("ALTER TABLE bookings MODIFY status ENUM('pending_payment', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending_payment'");
    $pdo->exec("ALTER TABLE service_bookings MODIFY status ENUM('pending_payment', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending_payment'");

    // 3. Add late_checkout_fee_amount to invoices if not exists
    $stmt = $pdo->query("SHOW COLUMNS FROM invoices LIKE 'late_checkout_fee_amount'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE invoices ADD COLUMN late_checkout_fee_amount DECIMAL(18,2) DEFAULT 0 AFTER discount_amount");
    }

    echo "Migration successful!\n";

} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}

