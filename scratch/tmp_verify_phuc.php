<?php
require __DIR__ . '/../Public/bootstrap.php';
$pdo = App\Core\Database::getInstance()->getConnection();
$stmt = $pdo->prepare("SELECT id, email, password_hash FROM users WHERE email = ? LIMIT 1");
$stmt->execute(['phuc123@gmail.com']);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
var_export($row);
echo PHP_EOL;
$candidates = ['123456','admin123','password','phuc123','123123','phuc06','12345678'];
foreach ($candidates as $candidate) {
    echo $candidate . ': ' . (password_verify($candidate, $row['password_hash']) ? 'MATCH' : 'NO') . PHP_EOL;
}
