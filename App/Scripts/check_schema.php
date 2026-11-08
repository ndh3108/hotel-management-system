<?php
require_once __DIR__ . '/../../App/Core/Database.php';
$pdo = \App\Core\Database::getInstance()->getConnection();
$stmt = $pdo->query('DESCRIBE inventory_items');
print_r($stmt->fetchAll(\PDO::FETCH_ASSOC));

