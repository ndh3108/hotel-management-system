<?php
require __DIR__ . '/Public/bootstrap.php';
$pdo = \App\Core\Database::getInstance()->getConnection();
foreach (['roles','permissions','role_permissions','user_roles'] as $t) {
    $stmt = $pdo->query("SHOW TABLES LIKE '$t'");
    echo $t . ':' . ($stmt->fetchColumn() ? 'yes' : 'no') . PHP_EOL;
}

