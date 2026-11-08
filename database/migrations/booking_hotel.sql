-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 13, 2026 at 07:44 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `booking_hotel`
--

-- --------------------------------------------------------

--
-- Table structure for table `amenities`
--

CREATE TABLE `amenities` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `icon` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `amenities`
--

INSERT INTO `amenities` (`id`, `name`, `icon`) VALUES
(1, 'Wifi miễn phí', 'fa-wifi'),
(2, 'Điều hòa', 'fa-snowflake'),
(3, 'Tivi', 'fa-tv'),
(4, 'Minibar', 'fa-wine-glass'),
(5, 'Bồn tắm', 'fa-bath'),
(6, 'Ban công', 'fa-sun');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `check_in` date DEFAULT NULL,
  `check_out` date DEFAULT NULL,
  `total_price` decimal(12,2) DEFAULT NULL,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp(),
  `type` enum('room','event') DEFAULT 'room',
  `event_type` varchar(50) DEFAULT NULL,
  `payment_menthod` varchar(50) DEFAULT NULL,
  `payment_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `check_in`, `check_out`, `total_price`, `status`, `created_at`, `type`, `event_type`, `payment_menthod`, `payment_at`) VALUES
(1, 3, '2026-02-01', '2026-02-03', 3300000.00, 'confirmed', '2026-03-12 05:57:17', 'room', NULL, 'Credit Card', '2026-02-01 10:00:00'),
(2, 4, '2026-02-05', '2026-02-07', 5000000.00, 'pending', '2026-03-12 05:57:17', 'room', NULL, NULL, NULL),
(3, 5, '2026-02-10', '2026-02-12', 16000000.00, 'confirmed', '2026-03-12 05:57:17', 'room', NULL, 'Momo', '2026-02-04 14:00:00'),
(4, 3, '2026-03-01', '2026-03-03', 1000000.00, 'pending', '2026-03-12 15:20:47', 'room', NULL, NULL, NULL),
(5, 3, '2026-03-02', '2026-03-03', 500000.00, 'cancelled', '2026-03-12 15:21:11', 'room', NULL, NULL, NULL),
(6, 3, '2026-03-01', '2026-03-04', 1500000.00, 'pending', '2026-03-12 15:35:04', 'room', NULL, NULL, NULL),
(7, 3, '2026-03-10', '2026-03-13', 1500000.00, 'pending', '2026-03-12 15:47:50', 'room', NULL, NULL, NULL),
(8, 3, '2026-03-29', '2026-03-31', 1000000.00, 'cancelled', '2026-03-12 15:51:37', 'room', NULL, NULL, NULL),
(9, 3, '2026-04-01', '2026-04-02', 500000.00, 'confirmed', '2026-03-12 16:10:08', 'room', NULL, NULL, NULL),
(10, 3, '2026-03-15', '2026-03-16', 500000.00, 'confirmed', '2026-03-12 16:18:22', 'room', NULL, NULL, NULL),
(11, 3, '2026-03-20', '2026-03-21', 500000.00, 'confirmed', '2026-03-12 16:44:29', 'room', NULL, NULL, NULL),
(12, 3, '2026-03-10', '2026-03-12', 1000000.00, 'pending', '2026-03-12 17:08:52', 'room', NULL, NULL, NULL),
(13, 3, '2026-03-27', '2026-03-28', 500000.00, 'pending', '2026-03-12 17:11:39', 'room', NULL, NULL, NULL),
(14, 3, '2026-03-05', '2026-03-06', 500000.00, 'pending', '2026-03-13 12:10:58', 'room', NULL, NULL, NULL),
(15, 3, '2026-03-01', '2026-03-02', 2500000.00, 'pending', '2026-03-13 12:21:34', 'room', NULL, NULL, NULL),
(16, 3, '2026-03-10', '2026-03-11', 500000.00, 'pending', '2026-03-13 12:37:58', 'room', NULL, NULL, NULL),
(17, 3, '2026-03-20', '2026-03-21', 500000.00, 'pending', '2026-03-13 12:44:38', 'room', NULL, NULL, NULL),
(18, 3, '2026-03-03', '2026-03-04', 800000.00, 'pending', '2026-03-13 13:03:27', 'room', NULL, NULL, NULL),
(19, 3, '2026-03-10', '2026-03-11', 1500000.00, 'confirmed', '2026-03-13 13:13:17', 'room', NULL, NULL, NULL),
(20, 3, '2026-03-07', '2026-03-08', 500000.00, 'confirmed', '2026-03-13 13:32:18', 'room', NULL, NULL, NULL),
(21, 3, '2026-03-16', '2026-03-17', 500000.00, 'pending', '2026-03-13 13:37:41', 'room', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `booking_details`
--

CREATE TABLE `booking_details` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `price` decimal(12,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking_details`
--

INSERT INTO `booking_details` (`id`, `booking_id`, `room_id`, `price`) VALUES
(1, 1, 3, 500000.00),
(2, 2, 13, 1500000.00),
(3, 3, 17, 8000000.00),
(4, 4, 1, 1000000.00),
(5, 5, 3, 500000.00),
(6, 6, 2, 1500000.00),
(7, 7, 2, 1500000.00),
(8, 8, 2, 1000000.00),
(9, 9, 1, 500000.00),
(10, 10, 1, 500000.00),
(11, 11, 1, 500000.00),
(12, 12, 1, 1000000.00),
(13, 13, 1, 500000.00),
(14, 14, 1, 500000.00),
(15, 15, 14, 2500000.00),
(16, 16, 3, 500000.00),
(17, 17, 2, 500000.00),
(18, 18, 5, 800000.00),
(19, 19, 13, 1500000.00),
(20, 20, 1, 500000.00),
(21, 21, 1, 500000.00);

-- --------------------------------------------------------

--
-- Table structure for table `booking_service`
--

CREATE TABLE `booking_service` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price_at_time` decimal(10,2) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','paid','cancelled') DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `id` int(11) NOT NULL,
  `code` varchar(20) NOT NULL,
  `discount_type` enum('fixed','percent') NOT NULL,
  `discount_value` decimal(12,2) NOT NULL,
  `min_order_value` decimal(12,2) DEFAULT 0.00,
  `valid_from` date DEFAULT NULL,
  `valid_to` date DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `coupons`
--

INSERT INTO `coupons` (`id`, `code`, `discount_type`, `discount_value`, `min_order_value`, `valid_from`, `valid_to`, `is_active`) VALUES
(1, 'WELCOME30', 'percent', 30.00, 0.00, '2026-01-01', '2026-12-31', 1),
(2, 'HOTEL500', 'fixed', 500000.00, 5000000.00, '2026-02-01', '2026-03-31', 1);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `method` varchar(50) DEFAULT NULL,
  `amount` decimal(12,2) DEFAULT NULL,
  `status` enum('pending','success','failed') DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  `currency` varchar(3) DEFAULT NULL,
  `result_code` varchar(20) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `booking_id`, `method`, `amount`, `status`, `paid_at`, `currency`, `result_code`, `message`, `created_at`) VALUES
(1, 1, 'Credit Card', 3300000.00, 'success', '2026-02-01 10:00:00', 'VND', NULL, NULL, '2026-03-12 05:57:17'),
(2, 3, 'Momo', 16000000.00, 'success', '2026-02-04 14:00:00', 'VND', NULL, NULL, '2026-03-12 05:57:17');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `room_id`, `rating`, `comment`, `created_at`) VALUES
(1, 3, 3, 5, 'Phòng rất sạch sẽ, nhân viên phục vụ tận tình.', '2026-03-12 05:57:17'),
(2, 4, 13, 4, 'View biển cực đẹp, phòng hơi nhỏ một chút nhưng vẫn ok.', '2026-03-12 05:57:17'),
(3, 5, 17, 5, 'Phòng cực kỳ sang trọng, xứng đáng với giá tiền.', '2026-03-12 05:57:17');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(1, 'Admin'),
(3, 'Customer'),
(2, 'Manager');

-- --------------------------------------------------------

--
-- Table structure for table `room_details`
--

CREATE TABLE `room_details` (
  `id` int(11) NOT NULL,
  `room_type_id` int(11) NOT NULL,
  `room_number` varchar(20) DEFAULT NULL,
  `floor` int(11) DEFAULT NULL,
  `status` enum('available','booked','maintenance') DEFAULT 'available',
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_details`
--

INSERT INTO `room_details` (`id`, `room_type_id`, `room_number`, `floor`, `status`, `image_path`) VALUES
(1, 1, '101', 1, 'available', NULL),
(2, 1, '102', 1, 'available', NULL),
(3, 1, '103', 1, 'booked', NULL),
(4, 1, '104', 1, 'available', NULL),
(5, 2, '201', 2, 'available', NULL),
(6, 2, '202', 2, 'available', NULL),
(7, 2, '203', 2, 'available', NULL),
(8, 2, '204', 2, 'maintenance', NULL),
(9, 3, '301', 3, 'available', NULL),
(10, 3, '302', 3, 'available', NULL),
(11, 3, '303', 3, 'available', NULL),
(12, 3, '304', 3, 'available', NULL),
(13, 3, '305', 3, 'booked', NULL),
(14, 4, '401', 4, 'available', NULL),
(15, 4, '402', 4, 'available', NULL),
(16, 4, '403', 4, 'available', NULL),
(17, 5, '501', 5, 'available', NULL),
(18, 5, '502', 5, 'available', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `room_images`
--

CREATE TABLE `room_images` (
  `id` int(11) NOT NULL,
  `room_type_id` int(11) NOT NULL,
  `room_id` int(11) DEFAULT NULL,
  `image_path` varchar(255) NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_images`
--

INSERT INTO `room_images` (`id`, `room_type_id`, `room_id`, `image_path`, `is_primary`) VALUES
(1, 1, NULL, '/assets/image/standard_single.jpg', 1),
(2, 2, NULL, '/assets/image/standard_double.jpg', 1),
(3, 3, NULL, '/assets/image/image.png', 1),
(4, 4, NULL, '/assets/image/family_suite.jpg', 1),
(5, 5, NULL, '/assets/image/presidential.jpg', 1);

-- --------------------------------------------------------

--
-- Table structure for table `room_types`
--

CREATE TABLE `room_types` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `size_m2` int(11) DEFAULT NULL,
  `capacity` int(11) DEFAULT NULL,
  `base_price` decimal(12,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_types`
--

INSERT INTO `room_types` (`id`, `name`, `description`, `size_m2`, `capacity`, `base_price`) VALUES
(1, 'Standard Single', 'Phòng đơn tiêu chuẩn, đầy đủ tiện nghi cơ bản.', 20, 1, 500000.00),
(2, 'Standard Double', 'Phòng đôi tiêu chuẩn, không gian ấm cúng, phù hợp cặp đôi.', 25, 2, 800000.00),
(3, 'Deluxe Ocean View', 'Phòng Deluxe với tầm nhìn hướng biển tuyệt đẹp, ban công rộng.', 35, 2, 1500000.00),
(4, 'Family Suite', 'Phòng Suite dành cho gia đình, có phòng khách riêng.', 50, 4, 2500000.00),
(5, 'Presidential Suite', 'Phòng Tổng thống sang trọng bậc nhất, đầy đủ tiện ích xa hoa.', 120, 2, 8000000.00);

-- --------------------------------------------------------

--
-- Table structure for table `room_type_amenities`
--

CREATE TABLE `room_type_amenities` (
  `id` int(11) NOT NULL,
  `room_type_id` int(11) NOT NULL,
  `amenity_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_type_amenities`
--

INSERT INTO `room_type_amenities` (`id`, `room_type_id`, `amenity_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 3),
(4, 2, 1),
(5, 2, 2),
(6, 2, 3),
(7, 2, 4),
(8, 3, 1),
(9, 3, 2),
(10, 3, 3),
(11, 3, 4),
(12, 3, 5),
(13, 3, 6),
(14, 4, 1),
(15, 4, 2),
(16, 4, 3),
(17, 4, 4),
(18, 4, 5),
(19, 4, 6),
(20, 5, 1),
(21, 5, 2),
(22, 5, 3),
(23, 5, 4),
(24, 5, 5),
(25, 5, 6);

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `price` decimal(12,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `type` enum('addon','standalone') DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `name`, `price`, `description`, `type`, `is_active`) VALUES
(1, 'Buffet Sáng', 150000.00, 'Buffet sáng tiêu chuẩn quốc tế tại nhà hàng tầng 1', 'addon', 1),
(2, 'Đưa đón sân bay', 300000.00, 'Xe riêng đón tiễn sân bay Tân Sơn Nhất', 'addon', 1),
(3, 'Dịch vụ Spa trọn gói', 800000.00, 'Liệu trình massage 90 phút thư giãn', 'standalone', 1),
(4, 'Giặt ủi nhanh', 50000.00, 'Giặt và sấy khô quần áo lấy trong ngày', 'addon', 1),
(5, 'Thuê xe máy', 150000.00, 'Thuê xe máy di chuyển tự do trong thành phố (24h)', 'standalone', 1),
(6, 'Gym & Fitness', 100000.00, 'Sử dụng phòng Gym hiện đại tại tầng thượng', 'addon', 1);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `description`) VALUES
(1, 'site_name', 'Booking Hotel', 'Tên website'),
(2, 'contact_email', 'contact@bookinghotel.com', 'Email liên hệ'),
(3, 'contact_phone', '(028) 1234 5678', 'Số điện thoại hỗ trợ'),
(4, 'address', '123 Đường ABC, Quận 1, TP.HCM', 'Địa chỉ khách sạn');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `status` tinyint(4) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role_id`, `full_name`, `email`, `password`, `phone`, `status`, `created_at`) VALUES
(1, 1, 'Hệ thống Admin', 'admin@bookinghotel.com', '$2y$10$2bvu3V2bxth5cEwhs9nQU.gLKauNnqMq5Yh2YfUEvDsv2lb8ewKOC', '0901234567', 0, '2026-03-12 05:57:17'),
(2, 2, 'Nguyễn Văn Quản Lý', 'manager@bookinghotel.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0907654321', 1, '2026-03-12 05:57:17'),
(3, 3, 'Trần Thị Khách Hàng', 'khachhang@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0912345678', 1, '2026-03-12 05:57:17'),
(4, 3, 'Lê Văn Dũng', 'dung.le@yahoo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0988776655', 1, '2026-03-12 05:57:17'),
(5, 3, 'Phạm Minh Tuấn', 'tuan.pham@outlook.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0944332211', 1, '2026-03-12 05:57:17');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `amenities`
--
ALTER TABLE `amenities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `booking_details`
--
ALTER TABLE `booking_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `booking_service`
--
ALTER TABLE `booking_service`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `room_details`
--
ALTER TABLE `room_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_type_id` (`room_type_id`);

--
-- Indexes for table `room_images`
--
ALTER TABLE `room_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_type_id` (`room_type_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `room_types`
--
ALTER TABLE `room_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `room_type_amenities`
--
ALTER TABLE `room_type_amenities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_type_id` (`room_type_id`),
  ADD KEY `amenity_id` (`amenity_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `amenities`
--
ALTER TABLE `amenities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `booking_details`
--
ALTER TABLE `booking_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `booking_service`
--
ALTER TABLE `booking_service`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `room_details`
--
ALTER TABLE `room_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `room_images`
--
ALTER TABLE `room_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `room_types`
--
ALTER TABLE `room_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `room_type_amenities`
--
ALTER TABLE `room_type_amenities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `booking_details`
--
ALTER TABLE `booking_details`
  ADD CONSTRAINT `booking_details_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`),
  ADD CONSTRAINT `booking_details_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `room_details` (`id`);

--
-- Constraints for table `booking_service`
--
ALTER TABLE `booking_service`
  ADD CONSTRAINT `booking_service_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`),
  ADD CONSTRAINT `booking_service_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `room_details` (`id`);

--
-- Constraints for table `room_details`
--
ALTER TABLE `room_details`
  ADD CONSTRAINT `room_details_ibfk_1` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`id`);

--
-- Constraints for table `room_images`
--
ALTER TABLE `room_images`
  ADD CONSTRAINT `room_images_ibfk_1` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`id`),
  ADD CONSTRAINT `room_images_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `room_details` (`id`);

--
-- Constraints for table `room_type_amenities`
--
ALTER TABLE `room_type_amenities`
  ADD CONSTRAINT `rta_ibfk_1` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`id`),
  ADD CONSTRAINT `rta_ibfk_2` FOREIGN KEY (`amenity_id`) REFERENCES `amenities` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

