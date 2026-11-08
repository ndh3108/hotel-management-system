<?php
require __DIR__ . '/../Public/bootstrap.php';
$pdo = App\Core\Database::getInstance()->getConnection();
$email = 'phuc123@gmail.com';
$stmt = $pdo->prepare("SELECT id, email, role_id, status, failed_login_attempts, locked_until FROM users WHERE email = ? LIMIT 1");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$stmt = $pdo->prepare("SELECT * FROM user_roles WHERE user_id = ?");
$stmt->execute([(int)($user['id'] ?? 0)]);
$userRoles = $stmt->fetchAll(PDO::FETCH_ASSOC);
var_export(['user' => $user, 'user_roles' => $userRoles]);
