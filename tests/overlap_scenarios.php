<?php

// Simple CLI script to verify 5 key overlap scenarios against BookingService.
// Run from project root:
//   php tests/overlap_scenarios.php

use App\Core\Database;
use App\Services\BookingService;

require __DIR__ . '/../Public/bootstrap.php';

$pdo = Database::getInstance()->getConnection();
$service = new BookingService();

/**
 * Helper: create a booking directly via SQL for a given room and interval.
 */
function seedBooking(PDO $pdo, int $userId, int $roomId, string $checkIn, string $checkOut, string $status = 'Pending'): int
{
    $pdo->beginTransaction();
    $stmt = $pdo->prepare("INSERT INTO bookings (user_id, room_qty, booking_code, total_initial_price, status) VALUES (?, 1, ?, 0, ?)");
    $code = 'TEST-' . time() . '-' . rand(100, 999);
    $stmt->execute([$userId, $code, $status]);
    $bookingId = (int)$pdo->lastInsertId();

    $stmt2 = $pdo->prepare("INSERT INTO booking_details (booking_id, room_id, check_in_date, check_out_date, price_per_night) VALUES (?, ?, ?, ?, ?)");
    $stmt2->execute([$bookingId, $roomId, $checkIn, $checkOut, 0]);
    $pdo->commit();
    return $bookingId;
}

function printResult(string $name, bool $ok): void
{
    echo sprintf("[%s] %s\n", $ok ? 'PASS' : 'FAIL', $name);
}

// Config: chỉnh lại cho đúng user/room test trong DB của bạn.
$testUserId = 1;
$testRoomId = 1;

echo "=== Overlap scenarios test ===\n";

// Clean previous test data (optional, depends on your DB policy)
$pdo->exec("DELETE FROM booking_details WHERE booking_id IN (SELECT id FROM bookings WHERE booking_code LIKE 'TEST-%')");
$pdo->exec("DELETE FROM bookings WHERE booking_code LIKE 'TEST-%'");

// Base booking: 2026-04-10 12:00 -> 2026-04-12 12:00
$baseIn = '2026-04-10 12:00:00';
$baseOut = '2026-04-12 12:00:00';
seedBooking($pdo, $testUserId, $testRoomId, $baseIn, $baseOut, 'Confirmed');

// 1) Back-to-back before: new ends exactly at old start (OK)
$ok1 = $service->isRoomAvailable(
    $testRoomId,
    '2026-04-08 12:00:00',
    '2026-04-10 12:00:00'
);
printResult('Case 1: back-to-back before (OK)', $ok1 === true);

// 2) Back-to-back after: new starts exactly at old end (OK)
$ok2 = $service->isRoomAvailable(
    $testRoomId,
    '2026-04-12 12:00:00',
    '2026-04-14 12:00:00'
);
printResult('Case 2: back-to-back after (OK)', $ok2 === true);

// 3) Strict overlap inside: new fully inside existing (BLOCK)
$ok3 = $service->isRoomAvailable(
    $testRoomId,
    '2026-04-11 00:00:00',
    '2026-04-11 23:59:59'
);
printResult('Case 3: fully inside (BLOCK)', $ok3 === false);

// 4) Partial overlap start: new starts before and ends inside existing (BLOCK)
$ok4 = $service->isRoomAvailable(
    $testRoomId,
    '2026-04-09 12:00:00',
    '2026-04-11 00:00:00'
);
printResult('Case 4: overlap at start (BLOCK)', $ok4 === false);

// 5) Partial overlap end: new starts inside and ends after existing (BLOCK)
$ok5 = $service->isRoomAvailable(
    $testRoomId,
    '2026-04-11 00:00:00',
    '2026-04-13 12:00:00'
);
printResult('Case 5: overlap at end (BLOCK)', $ok5 === false);

echo "=== Done ===\n";


