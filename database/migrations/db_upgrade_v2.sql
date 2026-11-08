-- Database Upgrade v2: Room & Inventory Production Grade
-- Goal: Normalize room features, implement maintenance tracking, and secure inventory damage workflow.

START TRANSACTION;

-- 1. Rooms Enhancement
ALTER TABLE `rooms` 
ADD COLUMN `location` VARCHAR(255) DEFAULT NULL AFTER `floor`;

-- 2. Room Features (Normalization)
CREATE TABLE IF NOT EXISTS `room_features` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `icon` VARCHAR(50) DEFAULT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `room_feature_assignments` (
    `room_id` INT(11) NOT NULL,
    `feature_id` INT(11) NOT NULL,
    PRIMARY KEY (`room_id`, `feature_id`),
    FOREIGN KEY (`room_id`) REFERENCES `rooms`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`feature_id`) REFERENCES `room_features`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3. Room Maintenance
CREATE TABLE IF NOT EXISTS `room_maintenance` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `room_id` INT(11) NOT NULL,
    `maintenance_date` DATE NOT NULL,
    `description` TEXT DEFAULT NULL,
    `performed_by` INT(11) DEFAULT NULL, -- FK to users.id
    `cost` DECIMAL(18,2) DEFAULT 0.00,
    `priority` ENUM('Low', 'Medium', 'High') DEFAULT 'Medium',
    `status` ENUM('Planned', 'In Progress', 'Completed') DEFAULT 'Planned',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`room_id`) REFERENCES `rooms`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`performed_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 4. Inventory Damage (Production Upgrade)
-- Step 4.1: Add new columns
ALTER TABLE `inventory_damage` 
ADD COLUMN `booking_id` INT(11) DEFAULT NULL AFTER `booking_detail_id`,
ADD COLUMN `room_id` INT(11) DEFAULT NULL AFTER `booking_id`,
ADD COLUMN `price_at_report` DECIMAL(18,2) DEFAULT NULL AFTER `price`,
ADD COLUMN `price_at_approval` DECIMAL(18,2) DEFAULT NULL AFTER `price_at_report`,
ADD COLUMN `is_fixed` TINYINT(1) DEFAULT 0 AFTER `status`,
ADD COLUMN `is_billed` TINYINT(1) DEFAULT 0 AFTER `is_fixed`,
ADD COLUMN `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`;

-- Step 4.2: Update status ENUM
ALTER TABLE `inventory_damage` 
MODIFY COLUMN `status` ENUM('Reported', 'Reviewed', 'Approved', 'Rejected', 'Billed') DEFAULT 'Reported';

-- Step 4.3: Migrate existing data where possible (optional but cleaner)
UPDATE `inventory_damage` SET `status` = 'Reported' WHERE `status` = 'pending';
UPDATE `inventory_damage` SET `status` = 'Approved' WHERE `status` = 'approved';

-- Step 4.4: Add Foreign Keys
ALTER TABLE `inventory_damage`
ADD CONSTRAINT `fk_damage_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings`(`id`) ON DELETE SET NULL,
ADD CONSTRAINT `fk_damage_room` FOREIGN KEY (`room_id`) REFERENCES `rooms`(`id`) ON DELETE SET NULL,
ADD CONSTRAINT `fk_damage_booking_detail` FOREIGN KEY (`booking_detail_id`) REFERENCES `booking_details`(`id`) ON DELETE CASCADE;

-- Step 4.5: Add Production Indexes
CREATE INDEX `idx_damage_booking` ON `inventory_damage` (`booking_id`);
CREATE INDEX `idx_damage_room` ON `inventory_damage` (`room_id`);
CREATE INDEX `idx_damage_status` ON `inventory_damage` (`status`);
CREATE INDEX `idx_damage_billed` ON `inventory_damage` (`is_billed`);

-- Step 4.6: Unique constraint to prevent duplicate spam
-- We use a combination of booking, room, item, and day to allow reporting but prevent accidental double-reporting same hour/minute
CREATE UNIQUE INDEX `idx_damage_prevent_spam` ON `inventory_damage` (`booking_id`, `room_id`, `item_id`, `created_at`);

COMMIT;

