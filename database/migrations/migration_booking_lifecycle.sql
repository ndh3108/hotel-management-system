-- =========================================================
-- Migration: Booking Lifecycle Standardization
-- Date: 2026-04-14
-- Description: Chuẩn hoá ENUM status cho bookings & service_bookings
--              Tạo bảng late_checkout_rules cho phí trễ checkout
-- =========================================================

-- 1. Chuẩn hoá bookings.status → 4 trạng thái: pending, paid, complete, cancel
-- Step 1a: Convert old values to new values (using VARCHAR temporarily)
ALTER TABLE `bookings` MODIFY COLUMN `status` VARCHAR(50) NOT NULL DEFAULT 'pending';

UPDATE `bookings` SET `status` = 'pending' WHERE `status` IN ('processing');
UPDATE `bookings` SET `status` = 'paid' WHERE `status` IN ('confirmed');
UPDATE `bookings` SET `status` = 'complete' WHERE `status` IN ('completed');
UPDATE `bookings` SET `status` = 'cancel' WHERE `status` IN ('cancelled');
UPDATE `bookings` SET `status` = 'cancel' WHERE `status` = '' OR `status` IS NULL;

-- Step 1b: Convert to new ENUM
ALTER TABLE `bookings` MODIFY COLUMN `status` ENUM('pending','paid','complete','cancel') NOT NULL DEFAULT 'pending';

-- 2. Chuẩn hoá service_bookings.status → 4 trạng thái (lowercase)
ALTER TABLE `service_bookings` MODIFY COLUMN `status` VARCHAR(50) DEFAULT 'pending';

UPDATE `service_bookings` SET `status` = 'pending' WHERE UPPER(`status`) = 'PENDING';
UPDATE `service_bookings` SET `status` = 'paid' WHERE UPPER(`status`) IN ('CONFIRMED', 'PAID');
UPDATE `service_bookings` SET `status` = 'cancel' WHERE UPPER(`status`) = 'CANCELLED';
UPDATE `service_bookings` SET `status` = 'complete' WHERE UPPER(`status`) = 'COMPLETED';

ALTER TABLE `service_bookings` MODIFY COLUMN `status` ENUM('pending','paid','complete','cancel') DEFAULT 'pending';

-- 3. Đảm bảo service_bookings có cột expired_at
ALTER TABLE `service_bookings` ADD COLUMN IF NOT EXISTS `expired_at` DATETIME DEFAULT NULL AFTER `status`;

-- 4. Tạo bảng late_checkout_rules (phí phạt trễ checkout theo mốc giờ)
CREATE TABLE IF NOT EXISTS `late_checkout_rules` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `from_hour` TINYINT UNSIGNED NOT NULL COMMENT 'Giờ bắt đầu áp dụng (VD: 12)',
  `to_hour` TINYINT UNSIGNED NOT NULL COMMENT 'Giờ kết thúc áp dụng (VD: 15)',
  `percent` TINYINT UNSIGNED NOT NULL COMMENT 'Phần trăm giá phòng phạt (VD: 30 = 30%)',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Seed dữ liệu mặc định cho late_checkout_rules
INSERT INTO `late_checkout_rules` (`from_hour`, `to_hour`, `percent`) VALUES
  (12, 15, 30),
  (15, 18, 50),
  (18, 24, 100);

-- 5. Thêm cột late_checkout_fee vào bảng invoices
ALTER TABLE `invoices` ADD COLUMN IF NOT EXISTS `late_checkout_fee` DECIMAL(18,2) DEFAULT 0.00 AFTER `discount_amount`;

-- 6. Cập nhật final_total formula (nếu có late_checkout_fee cũ = 0 thì không đổi)
-- (Không cần update data cũ vì default = 0.00)

-- =========================================================
-- DONE
-- =========================================================

