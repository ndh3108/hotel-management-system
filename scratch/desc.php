<?php
$pdo = new PDO('mysql:host=localhost;dbname=hotel;charset=utf8mb4', 'root', '');
print_r($pdo->query('DESCRIBE booking_details')->fetchAll(PDO::FETCH_ASSOC));

