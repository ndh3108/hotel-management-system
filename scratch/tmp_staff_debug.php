<?php
require __DIR__ . '/../Public/bootstrap.php';
$pdo = App\Core\Database::getInstance()->getConnection();
$email = 'phuc123@gmail.com';
$stmt = $pdo->prepare("SELECT id, email, role_id, status, full_name, failed_login_attempts, locked_until FROM users WHERE email = ? LIMIT 1");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
echo "USER\n";
var_export($user);
echo "\n\nUSER_ROLES\n";
if ($user) {
  $stmt = $pdo->prepare("SELECT * FROM user_roles WHERE user_id = ?");
  $stmt->execute([$user['id']]);
  var_export($stmt->fetchAll(PDO::FETCH_ASSOC));
  echo "\n\nROLES\n";
  $stmt = $pdo->prepare("SELECT r.* FROM roles r JOIN user_roles ur ON ur.role_id = r.id WHERE ur.user_id = ?");
  $stmt->execute([$user['id']]);
  var_export($stmt->fetchAll(PDO::FETCH_ASSOC));
  echo "\n\nPERMISSIONS\n";
  $sql = "SELECT DISTINCT p.* FROM permissions p JOIN role_permissions rp ON rp.permission_id = p.id JOIN user_roles ur ON ur.role_id = rp.role_id WHERE ur.user_id = ?";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([$user['id']]);
  var_export($stmt->fetchAll(PDO::FETCH_ASSOC));
}
