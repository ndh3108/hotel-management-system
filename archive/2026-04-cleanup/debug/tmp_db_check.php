<?php
$pdo = new PDO('mysql:host=localhost;dbname=hotel;charset=utf8mb4', 'root', '');
$pdo->exec("ALTER TABLE `rooms` MODIFY COLUMN `status` ENUM('Available','Occupied','Cleaning','Maintenance','Hold') DEFAULT 'Available'");
$pdo->exec("ALTER TABLE `rooms` ADD COLUMN `locked_at` DATETIME DEFAULT NULL");
$pdo->exec("ALTER TABLE `rooms` ADD COLUMN `locked_by_session` VARCHAR(255) DEFAULT NULL");
echo "Updated rooms table";

