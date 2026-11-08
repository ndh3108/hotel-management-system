<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/Public/bootstrap.php';

$bookingId = 7;
$service = new \App\Services\BookingService();

try {
    $result = $service->checkIn($bookingId);
    if ($result) {
        echo "Check-in successful for booking #$bookingId\n";
        
        // Verify snapshot
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM booking_inventory_items WHERE booking_detail_id = (SELECT id FROM booking_details WHERE booking_id = ? LIMIT 1)");
        $stmt->execute([$bookingId]);
        $snapshot = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "Snapshot count: " . count($snapshot) . "\n";
        print_r($snapshot);

    } else {
        echo "Check-in failed for booking #$bookingId\n";
    }
} catch (\Exception $e) {
    echo "Error: ", $e->getMessage(), "\n";
    echo $e->getTraceAsString(), "\n";
}

