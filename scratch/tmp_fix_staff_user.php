<?php
require __DIR__ . '/../Public/bootstrap.php';
$pdo = App\Core\Database::getInstance()->getConnection();
$email = 'phuc123@gmail.com';
$newPassword = 'staff123';
$passwordHash = password_hash($newPassword, PASSWORD_BCRYPT);

$pdo->beginTransaction();
try {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $userId = (int) $stmt->fetchColumn();
    if ($userId <= 0) {
        throw new RuntimeException('User not found');
    }

    $stmt = $pdo->prepare("UPDATE users SET password_hash = ?, role_id = 2, failed_login_attempts = 0, locked_until = NULL WHERE id = ?");
    $stmt->execute([$passwordHash, $userId]);

    $pdo->prepare("DELETE FROM user_roles WHERE user_id = ?")->execute([$userId]);
    $pdo->prepare("INSERT INTO user_roles (user_id, role_id, expires_at) VALUES (?, 2, NULL)")->execute([$userId]);

    try {
        $pdo->exec("UPDATE rbac_permission_versions SET permission_version = COALESCE(permission_version, 1) + 1, updated_at = NOW() WHERE id = 1");
    } catch (Throwable $e) {
        // ignore if table/column missing
    }

    $pdo->commit();
    echo json_encode([
        'success' => true,
        'email' => $email,
        'password' => $newPassword,
        'role_id' => 2,
        'user_id' => $userId,
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit(1);
}
