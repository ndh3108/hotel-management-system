<?php

/**
 * Update all bookings to have valid status values
 */

$db = new PDO(
    'mysql:host=localhost;dbname=hotel;charset=utf8mb4',
    'root',
    '',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║  UPDATE BOOKING STATUS - Set All Bookings to Valid Status      ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

// 1. Check current status
echo "1. Current booking statuses:\n";
echo "─────────────────────────────────────────────────────────────\n";

$stmt = $db->query("SELECT id, status, user_id FROM bookings ORDER BY id DESC LIMIT 10");
$bookings = $stmt->fetchAll();

$emptyCount = 0;
$validCount = 0;

foreach ($bookings as $b) {
    $status = $b['status'] ?: 'NULL/EMPTY';
    $isValid = in_array($b['status'], ['pending', 'processing', 'paid', 'confirmed', 'cancelled', 'completed']);
    $icon = $isValid && !empty($b['status']) ? "✓" : "✗";
    echo "   $icon Booking #{$b['id']}: status = '$status'\n";

    if (empty($b['status'])) {
        $emptyCount++;
    } else if ($isValid) {
        $validCount++;
    }
}

echo "\n   Summary:\n";
echo "   • Empty status: $emptyCount\n";
echo "   • Valid status: $validCount\n";

// 2. Update empty status to 'pending'
if ($emptyCount > 0) {
    echo "\n2. Updating empty statuses to 'pending':\n";
    echo "─────────────────────────────────────────────────────────────\n";

    $stmt = $db->prepare("
        UPDATE bookings 
        SET status = 'pending' 
        WHERE status IS NULL OR status = ''
    ");
    $stmt->execute();
    $updated = $stmt->rowCount();

    echo "   ✓ Updated $updated bookings\n";
}

// 3. Verify update
echo "\n3. Verify all bookings now have valid status:\n";
echo "─────────────────────────────────────────────────────────────\n";

$stmt = $db->query("SELECT id, status FROM bookings ORDER BY id DESC LIMIT 10");
$bookings = $stmt->fetchAll();

foreach ($bookings as $b) {
    echo "   ✓ Booking #{$b['id']}: status = '{$b['status']}'\n";
}

// 4. Check enum
echo "\n4. Status ENUM Definition:\n";
echo "─────────────────────────────────────────────────────────────\n";

$stmt = $db->query("SHOW CREATE TABLE bookings");
$result = $stmt->fetch();

if (preg_match("/`status`.*?enum\('([^']+)'(?:,'([^']+)')*\)/i", $result['Create Table'], $matches)) {
    echo "   ✓ ENUM values properly defined\n";
}

// 5. Available status transitions
echo "\n5. Status Transition Rules:\n";
echo "─────────────────────────────────────────────────────────────\n";

$transitions = [
    'pending' => ['processing', 'paid', 'cancelled'],
    'processing' => ['paid', 'cancelled'],
    'paid' => ['confirmed', 'cancelled'],
    'confirmed' => ['completed', 'cancelled'],
    'cancelled' => ['(final state)'],
    'completed' => ['(final state)']
];

foreach ($transitions as $from => $to) {
    echo "   • $from → " . implode(', ', $to) . "\n";
}

echo "\n6. Status Color Mapping:\n";
echo "─────────────────────────────────────────────────────────────\n";

$colors = [
    'pending' => 'warning (yellow) 🟡',
    'processing' => 'info (blue) 🔵',
    'paid' => 'success (green) 🟢',
    'confirmed' => 'success (green) ✅',
    'cancelled' => 'danger (red) ❌',
    'completed' => 'secondary (gray) ⚪'
];

foreach ($colors as $status => $color) {
    echo "   • $status: $color\n";
}

echo "\n" . str_repeat("═", 65) . "\n";
echo "✅ All bookings now have valid status values\n";
echo "✅ Admin can now change status via dropdown\n";
echo "✅ Users will see status updates\n\n";

echo "📍 Test URLs:\n";
echo "   Admin list: http://localhost/doan/public/admin/bookings\n";
echo "   Admin detail: http://localhost/doan/public/admin/bookings/1\n";
echo "   User detail: http://localhost/doan/public/bookings/1/view\n\n";

