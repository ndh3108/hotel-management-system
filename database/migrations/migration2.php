<?php
$pdo = new PDO('mysql:host=localhost;dbname=hotel;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = "
CREATE TABLE IF NOT EXISTS `role_permissions` (
  `role_id` INT NOT NULL,
  `permission_id` INT NOT NULL,
  PRIMARY KEY (`role_id`, `permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `user_roles` (
  `user_id` INT NOT NULL,
  `role_id` INT NOT NULL,
  `expires_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`user_id`, `role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Populate user_roles with existing user.role_id
INSERT IGNORE INTO `user_roles` (`user_id`, `role_id`)
SELECT `id`, `role_id` FROM `users` WHERE `role_id` IS NOT NULL;
";

$pdo->exec($sql);

$seed = "
INSERT IGNORE INTO `permissions` (`id`, `name`, `block_module`) VALUES 
(1, 'USER_MANAGE', 'Users'),
(2, 'ROOM_MANAGE', 'Rooms'),
(3, 'BOOKING_CREATE', 'Bookings'),
(4, 'BOOKING_VIEW_ALL', 'Bookings'),
(5, 'INVOICE_CREATE', 'Invoices'),
(6, 'REPORT_VIEW', 'Reports'),
(7, 'ROOM_STATUS_UPDATE', 'Rooms'),
(8, 'CONTENT_MANAGE', 'Content');

INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 1, id FROM permissions;

INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(3, 3), (3, 4), (3, 5),
(4, 7);
";

$pdo->exec($seed);
echo "Migration OK\n";

