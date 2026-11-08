-- Migration: Standardize booking status values to lowercase 6-state model
-- Date: 2026-04-02
-- Purpose: Align database enum with BookingStatus.php constants (pending, processing, paid, confirmed, cancelled, completed)

-- Step 0: Convert ENUM to VARCHAR temporarily to allow any value during transition
ALTER TABLE `bookings` 
MODIFY COLUMN `status` VARCHAR(50) NOT NULL DEFAULT 'pending';

-- Step 1: Map existing statuses to new lowercase values
-- 'Pending' -> 'pending'
-- 'Confirmed' -> 'confirmed'
-- 'Expired' -> 'cancelled' (expired should map to cancelled state)
-- 'Cancelled' -> 'cancelled'
UPDATE `bookings` SET `status` = LOWER(`status`) WHERE `status` IS NOT NULL;
UPDATE `bookings` SET `status` = 'canonical_pending' WHERE `status` = 'pending';
UPDATE `bookings` SET `status` = 'canonical_confirmed' WHERE `status` = 'confirmed';
UPDATE `bookings` SET `status` = 'canonical_cancelled' WHERE `status` IN ('cancelled', 'expired');
UPDATE `bookings` SET `status` = REPLACE(`status`, 'canonical_', '');

-- Step 2: Update the bookings table enum to support 6 new values with lowercase
-- This ensures ONLY these values are allowed going forward
ALTER TABLE `bookings` 
MODIFY COLUMN `status` ENUM('pending', 'processing', 'paid', 'confirmed', 'cancelled', 'completed') NOT NULL DEFAULT 'pending';

-- Step 3: Verify all bookings have valid status after migration
SELECT 
    status, 
    COUNT(*) as count 
FROM `bookings` 
WHERE `deleted_at` IS NULL 
GROUP BY `status`
ORDER BY `status`;

-- Expected result after migration:
-- All bookings should have status: pending, confirmed, processing, paid, or cancelled
-- No rows should remain with capitalized values like 'Pending', 'Confirmed', 'Expired', or 'Cancelled'

