<?php
require __DIR__ . '/../Public/bootstrap.php';
$pdo = App\Core\Database::getInstance()->getConnection();
$stmt = $pdo->prepare("SELECT id, email, password_hash, password FROM users WHERE email = ? LIMIT 1");
$stmt->execute(['admin@hotel.com']);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
var_dump($row);
