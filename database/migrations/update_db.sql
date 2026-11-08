CREATE TABLE IF NOT EXISTS `room_status_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_id` int(11) NOT NULL,
  `old_status` varchar(50) DEFAULT NULL,
  `new_status` varchar(50) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `rooms` MODIFY `status` ENUM('Available', 'Occupied', 'Cleaning', 'Maintenance') DEFAULT 'Available';

-- Update Audit Logs for RBAC requirement
ALTER TABLE `audit_logs` 
ADD COLUMN `record_id` INT DEFAULT NULL AFTER `table_name`,
ADD COLUMN `ip_address` VARCHAR(45) DEFAULT NULL AFTER `new_value`,
ADD COLUMN `user_agent` VARCHAR(255) DEFAULT NULL AFTER `ip_address`,
CHANGE COLUMN `timestamp` `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP;

-- Update Users table for Security requirement
ALTER TABLE `users`
ADD COLUMN `password_changed_at` DATETIME DEFAULT NULL AFTER `password_hash`,
ADD COLUMN `failed_login_attempts` INT DEFAULT 0 AFTER `password_changed_at`,
ADD COLUMN `locked_until` DATETIME DEFAULT NULL AFTER `failed_login_attempts`;

-- Remove role_id from users since we use N-N relation via user_roles
-- ALTER TABLE `users` DROP COLUMN `role_id`;


-- ========================================================
-- MODULE 2: ROOM & INVENTORY MIGRATION
-- ========================================================

-- 1. Updates to room_types
ALTER TABLE `room_types`
ADD COLUMN IF NOT EXISTS `slug` varchar(255) DEFAULT NULL AFTER `name`,
ADD COLUMN IF NOT EXISTS `area_sqm` decimal(10,2) DEFAULT 0.00 AFTER `description`;

-- 2. Amenities Updates
-- Adding icon_url if not exist (already in hotel.sql but make sure it is there)
-- ALTER TABLE `amenities` ADD COLUMN IF NOT EXISTS `icon_url` text DEFAULT NULL;

-- 3. Room Images Gallery
CREATE TABLE IF NOT EXISTS `room_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_type_id` int(11) DEFAULT NULL,
  `image_url` text NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `sort_order` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_rim_rt` (`room_type_id`),
  CONSTRAINT `fk_rim_rt` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 4. Room Inventory (Standard items per room type)
CREATE TABLE IF NOT EXISTS `room_inventory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_type_id` int(11) DEFAULT NULL,
  `item_name` varchar(255) NOT NULL,
  `standard_qty` int(11) DEFAULT 1,
  `unit_price` decimal(18,2) DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `fk_ri_rt` (`room_type_id`),
  CONSTRAINT `fk_ri_rt` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 5. Loss and Damages (Penalties charged to bookings)
CREATE TABLE IF NOT EXISTS `loss_and_damages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_id` int(11) DEFAULT NULL,
  `room_id` int(11) DEFAULT NULL,
  `item_name` varchar(255) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `penalty_amount` decimal(18,2) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

