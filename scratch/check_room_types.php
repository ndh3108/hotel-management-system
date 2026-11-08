<?php
$pdo = new PDO('mysql:host=localhost;dbname=hotel;charset=utf8mb4', 'root', '');
$q = $pdo->query('SHOW COLUMNS FROM room_types')->fetchAll(PDO::FETCH_ASSOC);
print_r($q);

