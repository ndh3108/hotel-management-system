<?php
require __DIR__ . '/../Public/bootstrap.php';
$pdo = App\Core\Database::getInstance()->getConnection();
$stmt = $pdo->prepare("SELECT DISTINCT p.name FROM permissions p JOIN role_permissions rp ON rp.permission_id = p.id WHERE rp.role_id = 2 ORDER BY p.name ASC");
$stmt->execute();
var_export($stmt->fetchAll(PDO::FETCH_COLUMN));
