<?php

/**
 * Check room_inventory table structure
 */

$db = new PDO(
    'mysql:host=localhost;dbname=hotel;charset=utf8mb4',
    'root',
    '',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

echo "Checking room_inventory table...\n\n";

// Check table structure
$stmt = $db->query("SHOW COLUMNS FROM room_inventory");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($columns)) {
    echo "❌ room_inventory table does not exist!\n";
    exit(1);
}

echo "Columns in room_inventory:\n";
foreach ($columns as $col) {
    echo "  - {$col['Field']} ({$col['Type']})\n";
}

// Check if room_type_id exists
$hasRoomTypeId = false;
foreach ($columns as $col) {
    if ($col['Field'] === 'room_type_id') {
        $hasRoomTypeId = true;
        break;
    }
}

echo "\n" . ($hasRoomTypeId ? "✓" : "✗") . " Has room_type_id: " . ($hasRoomTypeId ? 'YES' : 'NO') . "\n";

// Get table data to understand structure
$stmt = $db->query("SELECT * FROM room_inventory LIMIT 3");
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!empty($data)) {
    echo "\nSample data:\n";
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
}

