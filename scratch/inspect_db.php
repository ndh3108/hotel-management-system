<?php
$conf = include 'Config/database.php';
$dsn = "mysql:host={$conf['host']};dbname={$conf['dbname']};charset={$conf['charset']}";
$pdo = new PDO($dsn, $conf['username'], $conf['password']);

$tables = ['rooms', 'inventory_damage', 'inventory_items', 'room_types', 'booking_details'];

foreach ($tables as $t) {
    echo "\nTable: $t\n";
    $stmt = $pdo->query("DESCRIBE $t");
    while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
        printf("%-20s %-20s %-10s %s\n", $r['Field'], $r['Type'], $r['Null'], $r['Default']);
    }
}

