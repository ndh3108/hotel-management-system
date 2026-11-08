<?php
$pdo = new PDO('mysql:host=localhost;dbname=hotel;charset=utf8mb4', 'root', '');
$q1 = $pdo->query("SHOW COLUMNS FROM service_bookings LIKE 'status'")->fetch(PDO::FETCH_ASSOC);
$q2 = $pdo->query("SHOW COLUMNS FROM bookings LIKE 'status'")->fetch(PDO::FETCH_ASSOC);

print_r($q1);
print_r($q2);

