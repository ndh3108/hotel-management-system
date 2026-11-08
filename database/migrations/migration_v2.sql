SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

-- ========================================================
-- MODULE: VOUCHERS
-- ========================================================
CREATE TABLE IF NOT EXISTS `vouchers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL UNIQUE,
  `type` enum('PERCENT','FIXED_AMOUNT') NOT NULL DEFAULT 'PERCENT',
  `value` decimal(18,2) NOT NULL,
  `max_discount` decimal(18,2) DEFAULT NULL,
  `min_order_value` decimal(18,2) DEFAULT 0.00,
  `room_type_id` int(11) DEFAULT NULL,
  `valid_from` datetime DEFAULT NULL,
  `valid_until` datetime DEFAULT NULL,
  `max_uses` int(11) DEFAULT NULL, -- Tổng lượt dùng
  `max_uses_per_user` int(11) DEFAULT 1, -- Số lần được dùng bởi 1 người
  `used_count` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Bảng tra cứu Voucher - Người dùng để check max_uses_per_user
CREATE TABLE IF NOT EXISTS `user_vouchers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `voucher_id` int(11) NOT NULL,
  `used_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `booking_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- ========================================================
-- MODULE: USERS & LOYALTY
-- ========================================================
ALTER TABLE `users`
ADD COLUMN IF NOT EXISTS `current_points` int(11) DEFAULT 0 AFTER `email`,
ADD COLUMN IF NOT EXISTS `total_points_earned` int(11) DEFAULT 0 AFTER `current_points`,
ADD COLUMN IF NOT EXISTS `membership_id` int(11) DEFAULT 1 AFTER `total_points_earned`;


-- ========================================================
-- MODULE: ROOM INVENTORY (FIX SCHEMA)
-- ========================================================
DROP TABLE IF EXISTS `room_inventory`;
CREATE TABLE `room_inventory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_type_id` int(11) DEFAULT NULL,
  `item_name` varchar(255) NOT NULL,
  `default_quantity` int(11) DEFAULT 1,
  `price_per_unit` decimal(18,2) DEFAULT 0.00,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- ========================================================
-- MODULE: ROOMS (HOLDING & LOCKING)
-- ========================================================
ALTER TABLE `rooms` 
MODIFY `status` ENUM('Available', 'Hold', 'Occupied', 'Cleaning', 'Maintenance') DEFAULT 'Available',
ADD COLUMN IF NOT EXISTS `locked_at` datetime DEFAULT NULL AFTER `status`,
ADD COLUMN IF NOT EXISTS `locked_by_session` varchar(255) DEFAULT NULL AFTER `locked_at`,
ADD COLUMN IF NOT EXISTS `version` INT(11) DEFAULT 1 AFTER `locked_by_session`;


-- ========================================================
-- MODULE: BOOKINGS & INVOICES
-- ========================================================
ALTER TABLE `bookings` 
MODIFY `status` ENUM('Pending', 'Confirmed', 'Checked-in', 'Checked-out', 'Expired', 'Cancelled') DEFAULT 'Pending';

ALTER TABLE `booking_details`
ADD INDEX IF NOT EXISTS `idx_booking_room_range` (`room_id`, `check_in_date`, `check_out_date`);

ALTER TABLE `invoices`
ADD UNIQUE KEY `uniq_invoice_booking` (`booking_id`);

ALTER TABLE `invoices`
ADD COLUMN IF NOT EXISTS `surcharge_amount` decimal(18,2) DEFAULT 0.00 AFTER `total_service_amount`,
ADD COLUMN IF NOT EXISTS `points_redeemed_discount` decimal(18,2) DEFAULT 0.00 AFTER `discount_amount`,
ADD COLUMN IF NOT EXISTS `voucher_discount` decimal(18,2) DEFAULT 0.00 AFTER `points_redeemed_discount`;

ALTER TABLE `reviews`
ADD COLUMN IF NOT EXISTS `status` ENUM('Pending', 'Approved') DEFAULT 'Pending' AFTER `comment`,
ADD COLUMN IF NOT EXISTS `approved_at` datetime DEFAULT NULL AFTER `status`;

COMMIT;

