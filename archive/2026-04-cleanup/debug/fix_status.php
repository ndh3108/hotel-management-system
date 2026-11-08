<?php
header('Content-Type: application/json');

try {
    $db = new PDO(
        'mysql:host=localhost;dbname=hotel;charset=utf8mb4',
        'root',
        '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Check current status
    $stmt = $db->query("SELECT id, status, CHAR_LENGTH(status) as len FROM bookings WHERE id IN (1,2,3,4,5,6) ORDER BY id DESC");
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Update empty status
    $stmt = $db->prepare("UPDATE bookings SET status = 'pending' WHERE (status IS NULL OR status = '')");
    $stmt->execute();
    $updated = $stmt->rowCount();

    // Check after update
    $stmt = $db->query("SELECT id, status FROM bookings WHERE id IN (1,2,3,4,5,6) ORDER BY id DESC");
    $bookingsAfter = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'before' => $bookings,
        'updated' => $updated,
        'after' => $bookingsAfter,
        'success' => true
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()], JSON_PRETTY_PRINT);
}

