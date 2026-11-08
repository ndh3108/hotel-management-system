-- Deprecated: all schema/seed changes have been merged into hotel.sql.
-- Keep this file only for historical reference.

-- 1. Rename existing tables
RENAME TABLE `services` TO `addons`;
RENAME TABLE `service_categories` TO `addon_categories`;

-- 2. Create service_types
CREATE TABLE `service_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3. Create services
CREATE TABLE `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_type_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `capacity` int(11) DEFAULT 1,
  `price_per_hour` decimal(18,2) NOT NULL,
  `status` enum('AVAILABLE','MAINTENANCE') DEFAULT 'AVAILABLE',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 4. Create service_bookings
CREATE TABLE `service_bookings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `service_id` int(11) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `total_price` decimal(18,2) NOT NULL,
  `status` enum('PENDING','CONFIRMED','CANCELLED') DEFAULT 'PENDING',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 5. Create invoice_items
CREATE TABLE `invoice_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) NOT NULL,
  `type` enum('ROOM','SERVICE','DAMAGE') NOT NULL,
  `reference_id` int(11) NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `notes` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 5.1 Add indexes / foreign keys for integrity and performance
ALTER TABLE `services`
  ADD KEY `idx_services_type` (`service_type_id`),
  ADD CONSTRAINT `fk_services_service_type` FOREIGN KEY (`service_type_id`) REFERENCES `service_types` (`id`) ON DELETE SET NULL;

ALTER TABLE `service_bookings`
  ADD KEY `idx_service_bookings_service_id` (`service_id`),
  ADD KEY `idx_service_bookings_booking_id` (`booking_id`),
  ADD KEY `idx_service_bookings_user_id` (`user_id`),
  ADD KEY `idx_service_bookings_time` (`start_time`, `end_time`),
  ADD CONSTRAINT `fk_service_bookings_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_service_bookings_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_service_bookings_service` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;

ALTER TABLE `invoice_items`
  ADD KEY `idx_invoice_items_invoice_id` (`invoice_id`),
  ADD KEY `idx_invoice_items_reference` (`type`, `reference_id`),
  ADD CONSTRAINT `fk_invoice_items_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE;

-- 6. Insert permissions
INSERT INTO `permissions` (`name`, `block_module`) VALUES 
('CREATE_SERVICE_BOOKING', 'Service Bookings'),
('MANAGE_SERVICE_BOOKING', 'Service Bookings');

-- 7. Insert Role mapping
SET @create_p = (SELECT id FROM `permissions` WHERE `name` = 'CREATE_SERVICE_BOOKING');
SET @manage_p = (SELECT id FROM `permissions` WHERE `name` = 'MANAGE_SERVICE_BOOKING');

INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES 
(1, @create_p),
(1, @manage_p),
(2, @manage_p),
(3, @create_p);

