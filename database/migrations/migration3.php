<?php
$pdo = new PDO('mysql:host=localhost;dbname=hotel;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    $pdo->exec("ALTER TABLE `room_images` ADD COLUMN `sort_order` INT DEFAULT 0");
} catch(Exception $e) {}

$sql = "
CREATE TABLE IF NOT EXISTS `room_type_amenities` (
  `room_type_id` INT NOT NULL,
  `amenity_id` INT NOT NULL,
  PRIMARY KEY (`room_type_id`, `amenity_id`),
  FOREIGN KEY (`room_type_id`) REFERENCES `room_types`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`amenity_id`) REFERENCES `amenities`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

$pdo->exec($sql);
echo "Migration 3 OK\n";

