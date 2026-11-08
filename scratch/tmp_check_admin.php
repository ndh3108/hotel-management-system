<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require __DIR__ . '/../Public/bootstrap.php';
$pdo = App\Core\Database::getInstance()->getConnection();
$stmt = $pdo->query("SELECT id, email, role_id, status FROM users LIMIT 10");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
var_dump($rows);
