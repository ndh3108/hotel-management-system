<?php
$pdo = new PDO('mysql:host=localhost;dbname=hotel;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = "
CREATE TABLE IF NOT EXISTS `roles` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(50) NOT NULL UNIQUE,
  `description` VARCHAR(255) DEFAULT NULL,
  `is_system` BOOLEAN DEFAULT FALSE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `permissions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(50) NOT NULL UNIQUE,
  `module` VARCHAR(50) NOT NULL,
  `description` VARCHAR(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `role_permissions` (
  `role_id` INT NOT NULL,
  `permission_id` INT NOT NULL,
  PRIMARY KEY (`role_id`, `permission_id`),
  FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `user_roles` (
  `user_id` INT NOT NULL,
  `role_id` INT NOT NULL,
  `expires_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`user_id`, `role_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NULL,
  `action` ENUM('CREATE', 'UPDATE', 'DELETE') NOT NULL,
  `table_name` VARCHAR(50) NOT NULL,
  `record_id` INT NOT NULL,
  `old_value` JSON NULL,
  `new_value` JSON NULL,
  `ip_address` VARCHAR(45) NULL,
  `user_agent` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `room_inventory_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `icon_url` VARCHAR(255) DEFAULT NULL,
  `standard_qty` INT DEFAULT 1,
  `unit_price` DECIMAL(10,2) NOT NULL,
  `category` ENUM('Furniture', 'Electronics', 'Minibar', 'Linen', 'Other') DEFAULT 'Other'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `room_type_inventory` (
  `room_type_id` INT NOT NULL,
  `inventory_item_id` INT NOT NULL,
  `quantity` INT NOT NULL DEFAULT 1,
  PRIMARY KEY (`room_type_id`, `inventory_item_id`),
  FOREIGN KEY (`room_type_id`) REFERENCES `room_types`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`inventory_item_id`) REFERENCES `room_inventory_items`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `loyalty_memberships` (
  `user_id` INT PRIMARY KEY,
  `tier` ENUM('Bronze', 'Silver', 'Gold') DEFAULT 'Bronze',
  `total_points` INT DEFAULT 0,
  `current_points` INT DEFAULT 0,
  `last_upgraded_at` DATETIME NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `vouchers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(50) NOT NULL UNIQUE,
  `type` ENUM('PERCENT', 'FIXED_AMOUNT') NOT NULL,
  `discount_value` DECIMAL(10,2) NOT NULL,
  `max_discount` DECIMAL(10,2) NULL,
  `min_order_value` DECIMAL(10,2) DEFAULT 0,
  `room_type_id` INT NULL,
  `valid_from` DATETIME NOT NULL,
  `valid_until` DATETIME NOT NULL,
  `max_uses` INT DEFAULT 1,
  `used_count` INT DEFAULT 0,
  `is_active` BOOLEAN DEFAULT TRUE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Fallback in case roles was created without is_system 
";

$pdo->exec($sql);

try {
    $pdo->exec("ALTER TABLE `roles` ADD COLUMN `is_system` BOOLEAN DEFAULT TRUE");
} catch(Exception $e) {}
try {
    $pdo->exec("ALTER TABLE `rooms` ADD COLUMN `version` INT DEFAULT 1");
} catch(Exception $e) {}


$seed = "
INSERT IGNORE INTO `roles` (`id`, `name`, `description`, `is_system`) VALUES 
(1, 'Admin', 'Admin System', 1),
(2, 'Manager', 'Manager', 1),
(3, 'Receptionist', 'Reception', 1),
(4, 'Housekeeping', 'Housekeeping', 1),
(5, 'Guest', 'Guest', 1);

INSERT IGNORE INTO `permissions` (`id`, `code`, `module`, `description`) VALUES 
(1, 'USER_MANAGE', 'Users', 'Manage Users'),
(2, 'ROOM_MANAGE', 'Rooms', 'Manage Rooms/Inventory'),
(3, 'BOOKING_CREATE', 'Bookings', 'Create booking'),
(4, 'BOOKING_VIEW_ALL', 'Bookings', 'View all bookings'),
(5, 'INVOICE_CREATE', 'Invoices', 'Checkout invoice'),
(6, 'REPORT_VIEW', 'Reports', 'View reports'),
(7, 'ROOM_STATUS_UPDATE', 'Rooms', 'Housekeeping view/update room'),
(8, 'CONTENT_MANAGE', 'Content', 'Manage articles and map');

INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 1, id FROM permissions;

INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(3, 3), (3, 4), (3, 5),
(4, 7);
";

$pdo->exec($seed);
echo "Migration OK\n";

