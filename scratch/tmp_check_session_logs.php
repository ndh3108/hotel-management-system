<?php
require __DIR__ . '/../Public/bootstrap.php';
$pdo = App\Core\Database::getInstance()->getConnection();
$stmt = $pdo->query("SHOW TABLES LIKE 'user_session_logs'");
$exists = (bool) $stmt->fetchColumn();
var_export(['user_session_logs_exists' => $exists]);
if ($exists) {
    echo PHP_EOL;
    $stmt = $pdo->query("SELECT * FROM user_session_logs ORDER BY id DESC LIMIT 10");
    var_export($stmt->fetchAll(PDO::FETCH_ASSOC));
}
