<?php
$pdo = new PDO('mysql:host=localhost;dbname=hotel;charset=utf8mb4', 'root', '');
$stmt = $pdo->query("DESCRIBE room_type_amenities");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

