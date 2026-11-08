-- Migration: Create notifications table for in-app user notifications
-- Date: 2026-04-02
-- Purpose: Store booking status change notifications and other alerts for users

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'status_change',
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `old_status` varchar(50) DEFAULT NULL,
  `new_status` varchar(50) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `read_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `booking_id` (`booking_id`),
  KEY `is_read` (`is_read`),
  KEY `created_at` (`created_at`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create index for faster queries on unread notifications
CREATE INDEX idx_notifications_user_unread ON `notifications` (`user_id`, `is_read`);
CREATE INDEX idx_notifications_booking ON `notifications` (`booking_id`, `created_at` DESC);

-- Insert a sample notification to verify table creation
-- INSERT INTO `notifications` (`user_id`, `booking_id`, `type`, `subject`, `message`, `old_status`, `new_status`) 
-- VALUES (1, 1, 'status_change', 'Booking Status Updated', 'Your booking status has changed from pending to confirmed', 'pending', 'confirmed');

