<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=hotel;charset=utf8mb4', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    echo "Starting DB Migration...\n";

    // Update old enum values to lowercase equivalent if possible, otherwise alter will clear them
    $pdo->exec("UPDATE service_bookings SET status = 'pending' WHERE status = 'PENDING'");
    $pdo->exec("UPDATE service_bookings SET status = 'confirmed' WHERE status = 'CONFIRMED'");
    $pdo->exec("UPDATE service_bookings SET status = 'cancelled' WHERE status = 'CANCELLED'");

    // 1. Alter service_bookings ENUM
    $pdo->exec("ALTER TABLE service_bookings MODIFY COLUMN status ENUM('pending', 'processing', 'paid', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending'");
    echo "- Updated service_bookings status ENUM\n";

    // 2. Add expires_at column
    try { $pdo->exec("ALTER TABLE bookings ADD COLUMN expires_at DATETIME NULL"); } catch(Exception $e) {}
    try { $pdo->exec("ALTER TABLE service_bookings ADD COLUMN expires_at DATETIME NULL"); } catch(Exception $e) {}
    echo "- Added expires_at columns\n";

    // 3. Create late_checkout_rules
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS late_checkout_rules (
            id INT AUTO_INCREMENT PRIMARY KEY,
            from_hour INT NOT NULL,
            to_hour INT NOT NULL,
            percent INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    // Insert defaults if empty
    $count = $pdo->query("SELECT COUNT(*) FROM late_checkout_rules")->fetchColumn();
    if ($count == 0) {
        $stmt = $pdo->prepare("INSERT INTO late_checkout_rules (from_hour, to_hour, percent) VALUES (?, ?, ?)");
        $stmt->execute([12, 15, 30]);
        $stmt->execute([15, 18, 50]);
        $stmt->execute([18, 24, 100]);
        echo "- Inserted default late_checkout_rules\n";
    }

    echo "Migration completed successfully!\n";
} catch (PDOException $e) {
    echo "Query Error: " . $e->getMessage() . "\n";
}

