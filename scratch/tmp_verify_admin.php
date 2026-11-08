<?php
require __DIR__ . '/../Public/bootstrap.php';
$pdo = App\Core\Database::getInstance()->getConnection();
$stmt = $pdo->prepare("SELECT password_hash FROM users WHERE email = ? LIMIT 1");
$stmt->execute(['admin@hotel.com']);
$hash = $stmt->fetchColumn();
$candidates = ['123456','admin123','password','admin@hotel.com','123123','admin'];
foreach ($candidates as $candidate) {
    echo $candidate . ': ' . (password_verify($candidate, $hash) ? 'MATCH' : 'NO') . PHP_EOL;
}
