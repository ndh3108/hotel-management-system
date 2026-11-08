<?php
require __DIR__ . '/../Public/bootstrap.php';
$pdo = App\Core\Database::getInstance()->getConnection();
$email = 'phuc123@gmail.com';
$newPassword = 'staff123';
$passwordHash = password_hash($newPassword, PASSWORD_BCRYPT);

$stmt = $pdo->prepare("SELECT id, email, role_id, failed_login_attempts, locked_until FROM users WHERE email = ? LIMIT 1");
$stmt->execute([$email]);
$before = $stmt->fetch(PDO::FETCH_ASSOC);

$updateUser = $pdo->prepare("UPDATE users SET password_hash = ?, role_id = 2, failed_login_attempts = 0, locked_until = NULL WHERE email = ?");
$updateUser->execute([$passwordHash, $email]);
$userRows = $updateUser->rowCount();

$selectId = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
$selectId->execute([$email]);
$userId = (int) $selectId->fetchColumn();

$deleteRoles = $pdo->prepare("DELETE FROM user_roles WHERE user_id = ?");
$deleteRoles->execute([$userId]);
$deleted = $deleteRoles->rowCount();

$insertRole = $pdo->prepare("INSERT INTO user_roles (user_id, role_id, expires_at) VALUES (?, 2, NULL)");
$insertRole->execute([$userId]);
$inserted = $insertRole->rowCount();

$stmt = $pdo->prepare("SELECT id, email, role_id, failed_login_attempts, locked_until, password_hash FROM users WHERE email = ? LIMIT 1");
$stmt->execute([$email]);
$after = $stmt->fetch(PDO::FETCH_ASSOC);
$stmt = $pdo->prepare("SELECT * FROM user_roles WHERE user_id = ?");
$stmt->execute([$userId]);
$roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
  'before' => $before,
  'user_update_rows' => $userRows,
  'deleted_roles' => $deleted,
  'inserted_roles' => $inserted,
  'after' => $after,
  'user_roles' => $roles,
  'password_test' => password_verify($newPassword, (string)($after['password_hash'] ?? '')),
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
