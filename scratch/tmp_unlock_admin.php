<?php
require __DIR__ . '/../Public/bootstrap.php';
$pdo = App\Core\Database::getInstance()->getConnection();
$stmt = $pdo->prepare("UPDATE users SET locked_until = NULL, failed_login_attempts = 0 WHERE email = ?");
$stmt->execute(['admin@hotel.com']);
echo 'rows=' . $stmt->rowCount();
