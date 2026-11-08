-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th4 12, 2026 lúc 05:14 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `hotel`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `addons`
--

CREATE TABLE `addons` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(18,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `addon_categories`
--

CREATE TABLE `addon_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `addon_categories`
--

INSERT INTO `addon_categories` (`id`, `name`) VALUES
(1, 'Food'),
(2, 'Spa'),
(3, 'Laundry');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `amenities`
--

CREATE TABLE `amenities` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `icon_url` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `amenities`
--

INSERT INTO `amenities` (`id`, `name`, `icon_url`) VALUES
(1, 'Wifi', 'fa-wifi'),
(2, 'Mini Bar', 'fa-wine-glass'),
(3, 'Bathtub', 'fa-bath'),
(4, 'Air Conditioning', 'fa-snowflake'),
(5, 'Smart TV 4K', 'fa-tv'),
(6, 'High-speed Wi-Fi', 'fa-signal'),
(7, 'Nespresso Machine', 'fa-coffee'),
(8, 'Private Balcony', 'fa-mountain-sun'),
(9, 'Electronic Safe', 'fa-vault'),
(10, 'Hair Dryer', 'fa-wind'),
(11, 'Luxury Toiletries', 'fa-pump-soap'),
(12, 'King Size Bed', 'fa-bed'),
(13, 'Work Desk', 'fa-desktop'),
(14, 'Ironing Facilities', 'fa-shirt'),
(15, 'Netflix Access', 'fa-film'),
(16, 'Panoramic View', 'fa-clapperboard'),
(17, 'Premium Sound System', 'fa-music'),
(18, 'Rain Shower', 'fa-shower'),
(19, 'Complimentary Water', 'fa-bottle-water'),
(20, 'Sauna Access', 'fa-hot-tub-person'),
(21, 'Private Home Cinema', 'fa-video'),
(22, 'Yoga Studio', 'fa-child-reaching'),
(23, 'Game Console (PS5)', 'fa-gamepad'),
(24, 'Wine Cellar Access', 'fa-wine-bottle'),
(25, 'Kitchenette', 'fa-kitchen-set'),
(26, 'Heated Flooring', 'fa-socks'),
(27, 'Smart Lighting', 'fa-lightbulb'),
(28, 'Voice Control (Alexa)', 'fa-microphone-lines'),
(29, 'Oversized Balcony', 'fa-mountain-sun'),
(30, 'Pet-Friendly Kit', 'fa-paw'),
(31, 'Electric Vehicle Charging', 'fa-charging-station'),
(32, 'Soundproof Walls', 'fa-volume-xmark'),
(33, 'Library Corner', 'fa-book'),
(34, 'Indoor Vertical Garden', 'fa-leaf'),
(35, 'In-room Exercise Bike', 'fa-bicycle'),
(36, 'Direct Beach Access', 'fa-umbrella-beach'),
(37, 'Stargazing Telescope', 'fa-telescope'),
(38, 'Personal Butler Service', 'fa-user-tie'),
(39, 'Sleep Aid Machine', 'fa-moon');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `articles`
--

CREATE TABLE `articles` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `content` text NOT NULL,
  `excerpt` text DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `status` enum('Draft','Pending Review','Published') DEFAULT 'Draft',
  `author_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `articles`
--

INSERT INTO `articles` (`id`, `category_id`, `title`, `slug`, `content`, `excerpt`, `meta_title`, `meta_description`, `status`, `author_id`, `created_at`) VALUES
(1, 1, '10 Điểm đến không thể bỏ qua khi tới Đà Nẵng', NULL, 'Du khách không nên bỏ qua Bà Nà Hills, Cầu Rồng...', NULL, NULL, NULL, 'Draft', 1, '2026-03-22 13:42:29'),
(2, 2, 'Khuyến mãi mùa hè rực rỡ', NULL, 'Giảm ngay 20% cho khách hàng Gold...', NULL, NULL, NULL, 'Draft', 1, '2026-03-22 13:42:29'),
(3, 1, 'Bài viết mẫu 1', 'bai-viet-mau-1', 'Nội dung bài viết mẫu 1. Hệ thống hỗ trợ in đậm và heading...', 'Trích đoạn ngắn bài viết 1', NULL, NULL, 'Draft', 1, '2026-03-22 21:42:29'),
(4, 1, 'Bài viết mẫu 2', 'bai-viet-mau-2', 'Nội dung bài viết mẫu 2. Hệ thống hỗ trợ in đậm và heading...', 'Trích đoạn ngắn bài viết 2', NULL, NULL, 'Draft', 1, '2026-03-22 21:42:29'),
(5, 1, 'Bài viết mẫu 3', 'bai-viet-mau-3', 'Nội dung bài viết mẫu 3. Hệ thống hỗ trợ in đậm và heading...', 'Trích đoạn ngắn bài viết 3', NULL, NULL, 'Published', 1, '2026-03-22 21:42:29'),
(6, 1, 'Bài viết mẫu 4', 'bai-viet-mau-4', 'Nội dung bài viết mẫu 4. Hệ thống hỗ trợ in đậm và heading...', 'Trích đoạn ngắn bài viết 4', NULL, NULL, 'Draft', 1, '2026-03-22 21:42:29'),
(7, 1, 'Bài viết mẫu 5', 'bai-viet-mau-5', 'Nội dung bài viết mẫu 5. Hệ thống hỗ trợ in đậm và heading...', 'Trích đoạn ngắn bài viết 5', NULL, NULL, 'Draft', 1, '2026-03-22 21:42:29'),
(8, 1, 'Bài viết mẫu 6', 'bai-viet-mau-6', 'Nội dung bài viết mẫu 6. Hệ thống hỗ trợ in đậm và heading...', 'Trích đoạn ngắn bài viết 6', NULL, NULL, 'Published', 1, '2026-03-22 21:42:29'),
(9, 1, 'Bài viết mẫu 7', 'bai-viet-mau-7', 'Nội dung bài viết mẫu 7. Hệ thống hỗ trợ in đậm và heading...', 'Trích đoạn ngắn bài viết 7', NULL, NULL, 'Draft', 1, '2026-03-22 21:42:29'),
(10, 1, 'Bài viết mẫu 8', 'bai-viet-mau-8', 'Nội dung bài viết mẫu 8. Hệ thống hỗ trợ in đậm và heading...', 'Trích đoạn ngắn bài viết 8', NULL, NULL, 'Draft', 1, '2026-03-22 21:42:29'),
(11, 1, 'Bài viết mẫu 9', 'bai-viet-mau-9', 'Nội dung bài viết mẫu 9. Hệ thống hỗ trợ in đậm và heading...', 'Trích đoạn ngắn bài viết 9', NULL, NULL, 'Published', 1, '2026-03-22 21:42:29'),
(12, 1, 'Bài viết mẫu 10', 'bai-viet-mau-10', 'Nội dung bài viết mẫu 10. Hệ thống hỗ trợ in đậm và heading...', 'Trích đoạn ngắn bài viết 10', NULL, NULL, 'Draft', 1, '2026-03-22 21:42:29'),
(13, 1, 'Bài viết mẫu 11', 'bai-viet-mau-11', 'Nội dung bài viết mẫu 11. Hệ thống hỗ trợ in đậm và heading...', 'Trích đoạn ngắn bài viết 11', NULL, NULL, 'Draft', 1, '2026-03-22 21:42:29'),
(14, 1, 'Bài viết mẫu 12', 'bai-viet-mau-12', 'Nội dung bài viết mẫu 12. Hệ thống hỗ trợ in đậm và heading...', 'Trích đoạn ngắn bài viết 12', NULL, NULL, 'Published', 1, '2026-03-22 21:42:29'),
(15, 1, 'Bài viết mẫu 13', 'bai-viet-mau-13', 'Nội dung bài viết mẫu 13. Hệ thống hỗ trợ in đậm và heading...', 'Trích đoạn ngắn bài viết 13', NULL, NULL, 'Draft', 1, '2026-03-22 21:42:29'),
(16, 1, 'Bài viết mẫu 14', 'bai-viet-mau-14', 'Nội dung bài viết mẫu 14. Hệ thống hỗ trợ in đậm và heading...', 'Trích đoạn ngắn bài viết 14', NULL, NULL, 'Draft', 1, '2026-03-22 21:42:29'),
(17, 1, 'Bài viết mẫu 15', 'bai-viet-mau-15', 'Nội dung bài viết mẫu 15. Hệ thống hỗ trợ in đậm và heading...', 'Trích đoạn ngắn bài viết 15', NULL, NULL, 'Published', 1, '2026-03-22 21:42:29'),
(18, 1, 'Bài viết mẫu 16', 'bai-viet-mau-16', 'Nội dung bài viết mẫu 16. Hệ thống hỗ trợ in đậm và heading...', 'Trích đoạn ngắn bài viết 16', NULL, NULL, 'Draft', 1, '2026-03-22 21:42:29'),
(19, 1, 'Bài viết mẫu 17', 'bai-viet-mau-17', 'Nội dung bài viết mẫu 17. Hệ thống hỗ trợ in đậm và heading...', 'Trích đoạn ngắn bài viết 17', NULL, NULL, 'Draft', 1, '2026-03-22 21:42:29'),
(20, 1, 'Bài viết mẫu 18', 'bai-viet-mau-18', 'Nội dung bài viết mẫu 18. Hệ thống hỗ trợ in đậm và heading...', 'Trích đoạn ngắn bài viết 18', NULL, NULL, 'Published', 1, '2026-03-22 21:42:29'),
(21, 1, 'Bài viết mẫu 19', 'bai-viet-mau-19', 'Nội dung bài viết mẫu 19. Hệ thống hỗ trợ in đậm và heading...', 'Trích đoạn ngắn bài viết 19', NULL, NULL, 'Draft', 1, '2026-03-22 21:42:29'),
(22, 1, 'Bài viết mẫu 20', 'bai-viet-mau-20', 'Nội dung bài viết mẫu 20. Hệ thống hỗ trợ in đậm và heading...', 'Trích đoạn ngắn bài viết 20', NULL, NULL, 'Draft', 1, '2026-03-22 21:42:29');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `article_categories`
--

CREATE TABLE `article_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `article_categories`
--

INSERT INTO `article_categories` (`id`, `name`) VALUES
(1, 'Du lịch'),
(2, 'Khuyến mãi'),
(3, 'Tin tức khách sạn'),
(4, 'Chuyên mục 1'),
(5, 'Chuyên mục 2'),
(6, 'Chuyên mục 3'),
(7, 'Chuyên mục 4'),
(8, 'Chuyên mục 5'),
(9, 'Chuyên mục 6'),
(10, 'Chuyên mục 7'),
(11, 'Chuyên mục 8'),
(12, 'Chuyên mục 9'),
(13, 'Chuyên mục 10'),
(14, 'Chuyên mục 11'),
(15, 'Chuyên mục 12'),
(16, 'Chuyên mục 13'),
(17, 'Chuyên mục 14'),
(18, 'Chuyên mục 15'),
(19, 'Chuyên mục 16'),
(20, 'Chuyên mục 17'),
(21, 'Chuyên mục 18'),
(22, 'Chuyên mục 19'),
(23, 'Chuyên mục 20'),
(24, 'Chuyên mục 1'),
(25, 'Chuyên mục 2'),
(26, 'Chuyên mục 3'),
(27, 'Chuyên mục 4'),
(28, 'Chuyên mục 5'),
(29, 'Chuyên mục 6'),
(30, 'Chuyên mục 7'),
(31, 'Chuyên mục 8'),
(32, 'Chuyên mục 9'),
(33, 'Chuyên mục 10'),
(34, 'Chuyên mục 11'),
(35, 'Chuyên mục 12'),
(36, 'Chuyên mục 13'),
(37, 'Chuyên mục 14'),
(38, 'Chuyên mục 15'),
(39, 'Chuyên mục 16'),
(40, 'Chuyên mục 17'),
(41, 'Chuyên mục 18'),
(42, 'Chuyên mục 19'),
(43, 'Chuyên mục 20');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `article_tags`
--

CREATE TABLE `article_tags` (
  `article_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `attractions`
--

CREATE TABLE `attractions` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` enum('Di tích','Ẩm thực','Giải trí','Thiên nhiên') DEFAULT 'Di tích',
  `distance_km` decimal(5,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image_url` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `attractions`
--

INSERT INTO `attractions` (`id`, `name`, `type`, `distance_km`, `description`, `image_url`) VALUES
(1, 'Bãi biển Mỹ Khê', 'Di tích', 0.50, 'Bãi biển đẹp nhất khu vực', NULL),
(2, 'Cầu Rồng', 'Di tích', 2.00, 'Biểu tượng của thành phố', NULL),
(3, 'Bà Nà Hills', 'Di tích', 25.00, 'Khu du lịch nổi tiếng trên mây', NULL),
(4, 'Ngũ Hành Sơn', 'Di tích', 8.50, 'Quần thể núi đá vôi ven biển', NULL),
(5, 'Chùa Linh Ứng', 'Di tích', 10.00, 'Ngôi chùa linh thiêng với tượng Phật Bà cao nhất VN', NULL),
(6, 'Địa điểm du lịch 1', 'Thiên nhiên', 20.50, 'Mô tả về điểm đến 1', NULL),
(7, 'Địa điểm du lịch 2', 'Giải trí', 38.50, 'Mô tả về điểm đến 2', NULL),
(8, 'Địa điểm du lịch 3', 'Di tích', 2.50, 'Mô tả về điểm đến 3', NULL),
(9, 'Địa điểm du lịch 4', 'Giải trí', 36.50, 'Mô tả về điểm đến 4', NULL),
(10, 'Địa điểm du lịch 5', 'Giải trí', 19.50, 'Mô tả về điểm đến 5', NULL),
(11, 'Địa điểm du lịch 6', 'Giải trí', 3.50, 'Mô tả về điểm đến 6', NULL),
(12, 'Địa điểm du lịch 7', 'Thiên nhiên', 8.50, 'Mô tả về điểm đến 7', NULL),
(13, 'Địa điểm du lịch 8', 'Ẩm thực', 20.50, 'Mô tả về điểm đến 8', NULL),
(14, 'Địa điểm du lịch 9', 'Ẩm thực', 24.50, 'Mô tả về điểm đến 9', NULL),
(15, 'Địa điểm du lịch 10', 'Giải trí', 39.50, 'Mô tả về điểm đến 10', NULL),
(16, 'Địa điểm du lịch 11', 'Ẩm thực', 49.50, 'Mô tả về điểm đến 11', NULL),
(17, 'Địa điểm du lịch 12', 'Giải trí', 22.50, 'Mô tả về điểm đến 12', NULL),
(18, 'Địa điểm du lịch 13', 'Di tích', 26.50, 'Mô tả về điểm đến 13', NULL),
(19, 'Địa điểm du lịch 14', 'Giải trí', 49.50, 'Mô tả về điểm đến 14', NULL),
(20, 'Địa điểm du lịch 15', 'Ẩm thực', 41.50, 'Mô tả về điểm đến 15', NULL),
(21, 'Địa điểm du lịch 16', 'Di tích', 11.50, 'Mô tả về điểm đến 16', NULL),
(22, 'Địa điểm du lịch 17', 'Di tích', 42.50, 'Mô tả về điểm đến 17', NULL),
(23, 'Địa điểm du lịch 18', 'Thiên nhiên', 2.50, 'Mô tả về điểm đến 18', NULL),
(24, 'Địa điểm du lịch 19', 'Ẩm thực', 21.50, 'Mô tả về điểm đến 19', NULL),
(25, 'Địa điểm du lịch 20', 'Thiên nhiên', 20.50, 'Mô tả về điểm đến 20', NULL),
(26, 'Địa điểm du lịch 1', 'Thiên nhiên', 13.50, 'Mô tả về điểm đến 1', NULL),
(27, 'Địa điểm du lịch 2', 'Di tích', 41.50, 'Mô tả về điểm đến 2', NULL),
(28, 'Địa điểm du lịch 3', 'Di tích', 37.50, 'Mô tả về điểm đến 3', NULL),
(29, 'Địa điểm du lịch 4', 'Di tích', 11.50, 'Mô tả về điểm đến 4', NULL),
(30, 'Địa điểm du lịch 5', 'Thiên nhiên', 33.50, 'Mô tả về điểm đến 5', NULL),
(31, 'Địa điểm du lịch 6', 'Giải trí', 36.50, 'Mô tả về điểm đến 6', NULL),
(32, 'Địa điểm du lịch 7', 'Thiên nhiên', 10.50, 'Mô tả về điểm đến 7', NULL),
(33, 'Địa điểm du lịch 8', 'Thiên nhiên', 24.50, 'Mô tả về điểm đến 8', NULL),
(34, 'Địa điểm du lịch 9', 'Thiên nhiên', 36.50, 'Mô tả về điểm đến 9', NULL),
(35, 'Địa điểm du lịch 10', 'Di tích', 39.50, 'Mô tả về điểm đến 10', NULL),
(36, 'Địa điểm du lịch 11', 'Ẩm thực', 14.50, 'Mô tả về điểm đến 11', NULL),
(37, 'Địa điểm du lịch 12', 'Di tích', 3.50, 'Mô tả về điểm đến 12', NULL),
(38, 'Địa điểm du lịch 13', 'Thiên nhiên', 18.50, 'Mô tả về điểm đến 13', NULL),
(39, 'Địa điểm du lịch 14', 'Di tích', 4.50, 'Mô tả về điểm đến 14', NULL),
(40, 'Địa điểm du lịch 15', 'Giải trí', 4.50, 'Mô tả về điểm đến 15', NULL),
(41, 'Địa điểm du lịch 16', 'Giải trí', 27.50, 'Mô tả về điểm đến 16', NULL),
(42, 'Địa điểm du lịch 17', 'Ẩm thực', 37.50, 'Mô tả về điểm đến 17', NULL),
(43, 'Địa điểm du lịch 18', 'Di tích', 7.50, 'Mô tả về điểm đến 18', NULL),
(44, 'Địa điểm du lịch 19', 'Di tích', 46.50, 'Mô tả về điểm đến 19', NULL),
(45, 'Địa điểm du lịch 20', 'Thiên nhiên', 31.50, 'Mô tả về điểm đến 20', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `table_name` varchar(100) DEFAULT NULL,
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL,
  `timestamp` datetime DEFAULT current_timestamp(),
  `record_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `table_name`, `old_value`, `new_value`, `timestamp`, `record_id`) VALUES
(1, 1, 'UPDATE', 'room_types', '{\"id\":1,\"name\":\"Single Room\",\"slug\":\"\",\"base_price\":\"500000.00\",\"capacity_adults\":1,\"capacity_children\":1,\"description\":\"kakakaakaka\",\"area_sqm\":\"14.00\"}', '{\"name\":\"Single Room\",\"slug\":\"\",\"description\":\"kakakaakaka\",\"capacity_adults\":\"1\",\"capacity_children\":\"1\",\"base_price\":\"500000.00\",\"area_sqm\":\"14.00\"}', '2026-04-12 12:33:55', 1),
(2, 1, 'CREATE', 'images', NULL, '{\"entity_type\":\"room_type\",\"entity_id\":1,\"image_url\":\"https:\\/\\/res.cloudinary.com\\/dxpn9dub0\\/image\\/upload\\/v1775990036\\/room_types\\/niygawgmd9fjvv6zee3g.jpg\",\"public_id\":\"room_types\\/niygawgmd9fjvv6zee3g\"}', '2026-04-12 12:33:57', 1),
(3, 1, 'CREATE', 'notifications', NULL, '{\"user_id\":1,\"booking_id\":8,\"type\":\"status_change\",\"subject\":\"Cập nhật trạng thái đặt phòng #BKG-1775985976-4614\",\"message\":\"Trạng thái của đặt phòng #BKG-1775985976-4614 đã được cập nhật từ \'\' thành \'Đã thanh toán\'.\",\"old_status\":\"\",\"new_status\":\"paid\",\"is_read\":0}', '2026-04-12 13:06:58', 3),
(4, 1, 'UPDATE', 'room_types', '{\"id\":1,\"name\":\"Single Room\",\"slug\":\"\",\"base_price\":\"500000.00\",\"capacity_adults\":1,\"capacity_children\":1,\"description\":\"kakakaakaka\",\"area_sqm\":\"14.00\"}', '{\"name\":\"Single Room\",\"slug\":\"\",\"description\":\"kakakaakaka\",\"capacity_adults\":\"1\",\"capacity_children\":\"1\",\"base_price\":\"500000.00\",\"area_sqm\":\"14.00\"}', '2026-04-12 13:08:43', 1),
(5, 1, 'CREATE', 'images', NULL, '{\"entity_type\":\"room_type_video\",\"entity_id\":1,\"image_url\":\"https:\\/\\/www.youtube.com\\/embed\\/MEYuDENERcw\",\"public_id\":\"\"}', '2026-04-12 13:08:43', 2),
(6, 1, 'UPDATE', 'rooms', '{\"id\":1,\"room_type_id\":1,\"room_number\":\"101\",\"floor\":1,\"status\":\"Available\",\"version\":1,\"locked_at\":null,\"locked_by_session\":null}', '{\"room_type_id\":\"1\",\"room_number\":\"101\",\"floor\":1,\"status\":\"Available\"}', '2026-04-12 13:42:21', 1),
(7, 1, 'CREATE', 'images', NULL, '{\"entity_type\":\"room\",\"entity_id\":1,\"image_url\":\"https:\\/\\/res.cloudinary.com\\/dxpn9dub0\\/image\\/upload\\/v1775994142\\/rooms\\/rwdvwxci86h7r1mt2uds.jpg\",\"public_id\":\"rooms\\/rwdvwxci86h7r1mt2uds\"}', '2026-04-12 13:42:22', 3),
(8, 1, 'UPDATE', 'users', '{\"id\":25,\"role_id\":null,\"membership_id\":1,\"full_name\":\"Phúc\",\"email\":\"phuc123@gmail.com\",\"phone\":null,\"password_hash\":\"$2y$10$xWUqqH4Pooc\\/FXa.SPN2bOVAxz53Iq47AQSruM8Tw0qnXVoLezhRK\",\"points\":0,\"status\":1,\"created_at\":\"2026-03-26 11:51:39\",\"deleted_at\":null,\"updated_at\":\"2026-03-26 11:51:39\"}', '{\"role_id\":\"2\",\"full_name\":\"Phúc\",\"email\":\"phuc123@gmail.com\",\"phone\":\"\",\"status\":\"active\"}', '2026-04-12 13:43:37', 25),
(9, 1, 'UPDATE', 'room_types', '{\"id\":2,\"name\":\"Double Room\",\"slug\":\"double-room\",\"base_price\":\"800000.00\",\"capacity_adults\":2,\"capacity_children\":1,\"description\":null,\"area_sqm\":\"0.00\"}', '{\"name\":\"Double Room\",\"slug\":\"double-room\",\"description\":\"\",\"capacity_adults\":\"2\",\"capacity_children\":\"1\",\"base_price\":\"800000.00\",\"area_sqm\":\"15\"}', '2026-04-12 16:25:31', 2),
(10, 1, 'CREATE', 'images', NULL, '{\"entity_type\":\"room_type\",\"entity_id\":2,\"image_url\":\"https:\\/\\/res.cloudinary.com\\/dxpn9dub0\\/image\\/upload\\/v1776003933\\/room_types\\/lpsqh69ao24ar5jn38rw.jpg\",\"public_id\":\"room_types\\/lpsqh69ao24ar5jn38rw\"}', '2026-04-12 16:25:34', 4),
(11, 1, 'UPDATE', 'users', '{\"id\":25,\"role_id\":2,\"membership_id\":1,\"full_name\":\"Phúc\",\"email\":\"phuc123@gmail.com\",\"phone\":\"\",\"password_hash\":\"$2y$10$xWUqqH4Pooc\\/FXa.SPN2bOVAxz53Iq47AQSruM8Tw0qnXVoLezhRK\",\"points\":0,\"status\":0,\"created_at\":\"2026-03-26 11:51:39\",\"deleted_at\":null,\"updated_at\":\"2026-04-12 18:43:37\"}', '{\"role_id\":\"2\",\"full_name\":\"Phúc\",\"email\":\"phuc123@gmail.com\",\"phone\":\"\",\"status\":\"active\"}', '2026-04-12 16:28:13', 25),
(12, 1, 'UPDATE', 'users', '{\"id\":24,\"role_id\":3,\"membership_id\":1,\"full_name\":\"Seeded User 20\",\"email\":\"user20_seed@hotel.com\",\"phone\":\"0900000020\",\"password_hash\":\"hash123\",\"points\":397,\"status\":1,\"created_at\":\"2026-03-22 21:42:29\",\"deleted_at\":null,\"updated_at\":\"2026-03-25 23:55:12\"}', '{\"role_id\":\"5\",\"full_name\":\"Seeded User 20\",\"email\":\"user20_seed@hotel.com\",\"phone\":\"0900000020\",\"status\":\"inactive\"}', '2026-04-12 16:28:27', 24),
(13, 1, 'UPDATE', 'users', '{\"id\":25,\"role_id\":2,\"membership_id\":1,\"full_name\":\"Phúc\",\"email\":\"phuc123@gmail.com\",\"phone\":\"\",\"password_hash\":\"$2y$10$xWUqqH4Pooc\\/FXa.SPN2bOVAxz53Iq47AQSruM8Tw0qnXVoLezhRK\",\"points\":0,\"status\":0,\"created_at\":\"2026-03-26 11:51:39\",\"deleted_at\":null,\"updated_at\":\"2026-04-12 18:43:37\"}', '{\"role_id\":\"2\",\"full_name\":\"Phúc\",\"email\":\"phuc123@gmail.com\",\"phone\":\"\",\"status\":\"active\"}', '2026-04-12 16:28:42', 25);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `room_type_id` int(11) DEFAULT NULL,
  `room_qty` int(11) DEFAULT NULL,
  `booking_code` varchar(50) NOT NULL,
  `voucher_id` int(11) DEFAULT NULL,
  `total_initial_price` decimal(18,2) DEFAULT 0.00,
  `status` enum('pending','processing','paid','confirmed','cancelled','completed') NOT NULL DEFAULT 'pending',
  `expired_at` datetime DEFAULT NULL,
  `guest_token` varchar(64) DEFAULT NULL,
  `idempotency_key` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `failed_login_attempts` int(11) DEFAULT 0,
  `locked_until` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `room_type_id`, `room_qty`, `booking_code`, `voucher_id`, `total_initial_price`, `status`, `expired_at`, `guest_token`, `idempotency_key`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, NULL, 3, 1, 'D95F7642', NULL, 2000000.00, '', '2026-03-26 06:16:41', 'ebe0e866b27112e7714b9957b521ae33', NULL, '2026-03-26 12:01:41', '2026-04-02 12:37:39', NULL),
(2, 1, NULL, 1, 'BKG-1775014045-2992', NULL, 1200000.00, '', NULL, NULL, NULL, '2026-04-01 10:27:25', '2026-04-02 12:37:39', NULL),
(3, 1, NULL, 1, 'BKG-1775098905-6408', NULL, 1000000.00, '', NULL, NULL, NULL, '2026-04-02 10:01:45', '2026-04-02 12:37:39', NULL),
(4, 1, NULL, 1, 'BKG-1775099405-1897', NULL, 700000.00, '', NULL, NULL, NULL, '2026-04-02 10:10:05', '2026-04-02 12:37:39', NULL),
(5, 1, NULL, 1, 'BKG-1775100554-1840', NULL, 16800000.00, '', NULL, NULL, NULL, '2026-04-02 10:29:14', '2026-04-02 12:37:39', NULL),
(6, 1, NULL, 1, 'BKG-1775102160-1441', NULL, 500000.00, 'completed', NULL, NULL, NULL, '2026-04-02 10:56:00', '2026-04-02 12:50:02', NULL),
(7, 1, NULL, 1, 'BKG-1775110446-4756', NULL, 500000.00, '', NULL, NULL, NULL, '2026-04-02 13:14:06', '2026-04-12 14:50:16', NULL),
(8, 1, NULL, 1, 'BKG-1775985976-4614', NULL, 0.00, 'paid', NULL, NULL, NULL, '2026-04-12 16:26:16', '2026-04-12 18:06:55', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `booking_details`
--

CREATE TABLE `booking_details` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `room_id` int(11) DEFAULT NULL,
  `check_in_date` datetime NOT NULL,
  `check_out_date` datetime NOT NULL,
  `actual_check_in` datetime DEFAULT NULL,
  `actual_check_out` datetime DEFAULT NULL,
  `price_per_night` decimal(18,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `booking_details`
--

INSERT INTO `booking_details` (`id`, `booking_id`, `room_id`, `check_in_date`, `check_out_date`, `actual_check_in`, `actual_check_out`, `price_per_night`) VALUES
(1, 1, NULL, '2026-04-27 14:00:00', '2026-04-29 12:00:00', NULL, NULL, 2000000.00),
(2, 2, 25, '2026-04-02 00:00:00', '2026-04-03 00:00:00', NULL, NULL, 1200000.00),
(3, 3, 6, '2026-04-02 05:01:45', '2026-04-04 05:01:45', NULL, NULL, 500000.00),
(4, 4, 50, '2026-04-03 05:10:05', '2026-04-04 05:10:05', NULL, NULL, 700000.00),
(5, 5, 7, '2026-04-02 05:29:14', '2026-04-23 05:29:14', NULL, NULL, 800000.00),
(6, 6, 1, '2026-04-10 05:56:00', '2026-04-11 05:56:00', NULL, NULL, 500000.00),
(7, 7, 1, '2026-04-02 08:14:06', '2026-04-03 08:14:06', NULL, NULL, 500000.00),
(8, 8, 29, '2026-04-21 00:00:00', '2026-04-23 00:00:00', NULL, NULL, 800000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `booking_logs`
--

CREATE TABLE `booking_logs` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `booking_logs`
--

INSERT INTO `booking_logs` (`id`, `booking_id`, `action`, `details`, `created_at`) VALUES
(1, 1, 'HOLD', 'Rooms held until 2026-03-26 06:16:41', '2026-03-26 12:01:41');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `review_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `content` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `images`
--

CREATE TABLE `images` (
  `id` int(11) NOT NULL,
  `entity_type` varchar(50) DEFAULT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `image_url` text DEFAULT NULL,
  `public_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `images`
--

INSERT INTO `images` (`id`, `entity_type`, `entity_id`, `image_url`, `public_id`, `created_at`) VALUES
(1, 'room_type', 1, 'https://res.cloudinary.com/dxpn9dub0/image/upload/v1775990036/room_types/niygawgmd9fjvv6zee3g.jpg', 'room_types/niygawgmd9fjvv6zee3g', '2026-04-12 10:33:57'),
(2, 'room_type_video', 1, 'https://www.youtube.com/embed/MEYuDENERcw', '', '2026-04-12 11:08:43'),
(3, 'room', 1, 'https://res.cloudinary.com/dxpn9dub0/image/upload/v1775994142/rooms/rwdvwxci86h7r1mt2uds.jpg', 'rooms/rwdvwxci86h7r1mt2uds', '2026-04-12 11:42:22'),
(4, 'room_type', 2, 'https://res.cloudinary.com/dxpn9dub0/image/upload/v1776003933/room_types/lpsqh69ao24ar5jn38rw.jpg', 'room_types/lpsqh69ao24ar5jn38rw', '2026-04-12 14:25:34');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `total_room_amount` decimal(18,2) DEFAULT 0.00,
  `total_service_amount` decimal(18,2) DEFAULT 0.00,
  `discount_amount` decimal(18,2) DEFAULT 0.00,
  `final_total` decimal(18,2) NOT NULL,
  `status` varchar(50) DEFAULT 'Unpaid'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `invoice_items`
--

CREATE TABLE `invoice_items` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `type` enum('ROOM','SERVICE','DAMAGE') NOT NULL,
  `reference_id` int(11) NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `loss_and_damages`
--

CREATE TABLE `loss_and_damages` (
  `id` int(11) NOT NULL,
  `booking_detail_id` int(11) DEFAULT NULL,
  `item_name` varchar(255) DEFAULT NULL,
  `penalty_amount` decimal(18,2) NOT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `loyalty_memberships`
--

CREATE TABLE `loyalty_memberships` (
  `user_id` int(11) NOT NULL,
  `tier` enum('Bronze','Silver','Gold') DEFAULT 'Bronze',
  `total_points` int(11) DEFAULT 0,
  `current_points` int(11) DEFAULT 0,
  `last_upgraded_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `memberships`
--

CREATE TABLE `memberships` (
  `id` int(11) NOT NULL,
  `tier_name` varchar(100) NOT NULL,
  `min_points` int(11) DEFAULT 0,
  `discount_percent` decimal(5,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `memberships`
--

INSERT INTO `memberships` (`id`, `tier_name`, `min_points`, `discount_percent`) VALUES
(1, 'Bronze', 0, 0.00),
(2, 'Silver', 1000, 5.00),
(3, 'Gold', 5000, 10.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
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
  `failed_login_attempts` int(11) DEFAULT 0,
  `locked_until` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `booking_id`, `type`, `subject`, `message`, `old_status`, `new_status`, `is_read`, `read_at`, `created_at`, `updated_at`) VALUES
(1, 1, 6, 'status_change', 'Cập nhật trạng thái đặt phòng #BKG-1775102160-1441', 'Trạng thái của đặt phòng #BKG-1775102160-1441 đã được cập nhật từ \'Đã thanh toán\' thành \'Đã xác nhận\'.', 'paid', 'confirmed', 0, NULL, '2026-04-02 12:40:20', '2026-04-02 12:40:20'),
(2, 1, 6, 'status_change', 'Cập nhật trạng thái đặt phòng #BKG-1775102160-1441', 'Trạng thái của đặt phòng #BKG-1775102160-1441 đã được cập nhật từ \'Đã xác nhận\' thành \'Hoàn thành\'.', 'confirmed', 'completed', 0, NULL, '2026-04-02 12:50:04', '2026-04-02 12:50:04'),
(3, 1, 8, 'status_change', 'Cập nhật trạng thái đặt phòng #BKG-1775985976-4614', 'Trạng thái của đặt phòng #BKG-1775985976-4614 đã được cập nhật từ \'\' thành \'Đã thanh toán\'.', '', 'paid', 0, NULL, '2026-04-12 18:06:57', '2026-04-12 18:06:57');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_services`
--

CREATE TABLE `order_services` (
  `id` int(11) NOT NULL,
  `booking_detail_id` int(11) DEFAULT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `total_amount` decimal(18,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_service_details`
--

CREATE TABLE `order_service_details` (
  `id` int(11) NOT NULL,
  `order_service_id` int(11) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price_at_order` decimal(18,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `amount_paid` decimal(18,2) NOT NULL,
  `payment_type` enum('Deposit','Final','Refund') DEFAULT NULL,
  `method` enum('CASH','BANK','VNPAY') DEFAULT NULL,
  `status` enum('Pending','Success','Failed') DEFAULT 'Success',
  `payment_ref` varchar(100) DEFAULT NULL,
  `payment_date` datetime DEFAULT current_timestamp(),
  `idempotency_key` varchar(100) DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `failed_login_attempts` int(11) DEFAULT 0,
  `locked_until` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `payment_logs`
--

CREATE TABLE `payment_logs` (
  `id` int(11) NOT NULL,
  `payment_id` int(11) DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `code` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `module` varchar(100) DEFAULT NULL,
  `block_module` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `permission_version` int(11) DEFAULT 1,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `permissions`
--

INSERT INTO `permissions` (`id`, `code`, `name`, `module`, `block_module`, `description`, `is_active`, `permission_version`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'ACCESS_DASHBOARD', 'Access_Dashboard', 'System', NULL, NULL, 1, 1, NULL, '2026-04-12 17:14:00', '2026-04-12 17:14:00'),
(2, 'MANAGE_ROOMS', 'Manage_Rooms', 'Rooms', NULL, NULL, 1, 1, NULL, '2026-04-12 17:14:00', '2026-04-12 17:14:00'),
(3, 'CREATE_BOOKING', 'Create_Booking', 'Bookings', NULL, NULL, 1, 1, NULL, '2026-04-12 17:14:00', '2026-04-12 17:14:00'),
(4, 'EXPORT_INVOICES', 'Export_Invoices', 'Finance', NULL, NULL, 1, 1, NULL, '2026-04-12 17:14:00', '2026-04-12 17:14:00'),
(5, 'BOOKING_CREATE', 'BOOKING_CREATE', 'Bookings', NULL, NULL, 1, 1, NULL, '2026-04-12 17:14:00', '2026-04-12 17:14:00'),
(6, 'BOOKING_VIEW', 'BOOKING_VIEW', 'Bookings', NULL, NULL, 1, 1, NULL, '2026-04-12 17:14:00', '2026-04-12 17:14:00'),
(7, 'PAYMENT_CREATE', 'PAYMENT_CREATE', 'Finance', NULL, NULL, 1, 1, NULL, '2026-04-12 17:14:00', '2026-04-12 17:14:00'),
(8, 'REPORT_VIEW', 'REPORT_VIEW', 'System', NULL, NULL, 1, 1, NULL, '2026-04-12 17:14:00', '2026-04-12 17:14:00'),
(9, 'USER_MANAGE', 'USER_MANAGE', 'System', NULL, NULL, 1, 1, NULL, '2026-04-12 17:14:00', '2026-04-12 17:14:00'),
(10, 'BOOKING_VIEW_OWN', 'BOOKING_VIEW_OWN', 'Bookings', NULL, NULL, 1, 1, NULL, '2026-04-12 17:14:00', '2026-04-12 17:14:00'),
(11, 'BOOKING_VIEW_ALL', 'BOOKING_VIEW_ALL', 'Bookings', NULL, NULL, 1, 1, NULL, '2026-04-12 17:14:00', '2026-04-12 17:14:00'),
(12, 'REVIEW_CREATE', 'REVIEW_CREATE', 'Rooms', NULL, NULL, 1, 1, NULL, '2026-04-12 17:14:00', '2026-04-12 17:14:00'),
(13, 'REVIEW_APPROVE', 'REVIEW_APPROVE', 'Rooms', NULL, NULL, 1, 1, NULL, '2026-04-12 17:14:00', '2026-04-12 17:14:00'),
(14, 'VIEW_DASHBOARD', 'VIEW_DASHBOARD', 'System', 'System', NULL, 1, 1, NULL, '2026-04-12 17:14:00', '2026-04-12 17:14:00'),
(15, 'MANAGE_ROOMS_ALT', 'MANAGE_ROOMS', 'Rooms', 'Rooms', NULL, 1, 1, NULL, '2026-04-12 17:14:00', '2026-04-12 17:14:00'),
(16, 'UPDATE_ROOM_STATUS', 'UPDATE_ROOM_STATUS', 'Rooms', 'Rooms', NULL, 1, 1, NULL, '2026-04-12 17:14:00', '2026-04-12 17:14:00'),
(17, 'CREATE_BOOKING_ALT', 'CREATE_BOOKING', 'Bookings', 'Bookings', NULL, 1, 1, NULL, '2026-04-12 17:14:00', '2026-04-12 17:14:00'),
(18, 'DELETE_BOOKING', 'DELETE_BOOKING', 'Bookings', 'Bookings', NULL, 1, 1, NULL, '2026-04-12 17:14:00', '2026-04-12 17:14:00'),
(19, 'VIEW_INVOICE', 'VIEW_INVOICE', 'Finance', 'Finance', NULL, 1, 1, NULL, '2026-04-12 17:14:00', '2026-04-12 17:14:00'),
(20, 'VIEW_INVENTORY', 'VIEW_INVENTORY', 'Inventory', 'Inventory', NULL, 1, 1, NULL, '2026-04-12 17:14:00', '2026-04-12 17:14:00'),
(21, 'MANAGE_INVENTORY', 'MANAGE_INVENTORY', 'Inventory', 'Inventory', NULL, 1, 1, NULL, '2026-04-12 17:14:00', '2026-04-12 17:14:00'),
(22, 'CREATE_SERVICE_BOOKING', 'CREATE_SERVICE_BOOKING', 'Service Bookings', 'Service Bookings', NULL, 1, 1, NULL, '2026-04-12 17:14:00', '2026-04-12 17:14:00'),
(23, 'MANAGE_SERVICE_BOOKING', 'MANAGE_SERVICE_BOOKING', 'Service Bookings', 'Service Bookings', NULL, 1, 1, NULL, '2026-04-12 17:14:00', '2026-04-12 17:14:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `point_history`
--

CREATE TABLE `point_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `points` int(11) NOT NULL,
  `type` enum('Earned','Redeemed','Expired') NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `content` text DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `created_at` datetime DEFAULT current_timestamp(),
  `submit_token` varchar(64) DEFAULT NULL,
  `rating_room` int(11) DEFAULT 5 CHECK (`rating_room` between 1 and 5),
  `rating_service` int(11) DEFAULT 5 CHECK (`rating_service` between 1 and 5),
  `rating_cleanliness` int(11) DEFAULT 5 CHECK (`rating_cleanliness` between 1 and 5),
  `rating_location` int(11) DEFAULT 5 CHECK (`rating_location` between 1 and 5),
  `image_urls` text DEFAULT NULL,
  `room_type_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_system` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`, `is_system`) VALUES
(1, 'Admin', 'Toàn quyền quản trị hệ thống', 1),
(2, 'Staff', 'Nhân viên vận hành, quản lý phòng', 1),
(3, 'Customer', 'Khách hàng sử dụng dịch vụ', 1),
(4, 'Housekeeping', 'Nhân viên dọn dẹp', 1),
(5, 'Guest', 'Guest', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `role_permissions`
--

CREATE TABLE `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `role_permissions`
--

INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 12),
(1, 13),
(1, 14),
(1, 15),
(1, 16),
(1, 17),
(1, 18),
(1, 19),
(1, 20),
(1, 21),
(1, 22),
(1, 23),
(2, 1),
(2, 2),
(2, 3),
(2, 5),
(2, 6),
(2, 11),
(2, 13),
(2, 14),
(2, 17),
(2, 19),
(2, 20),
(2, 23),
(3, 3),
(3, 4),
(3, 5),
(3, 6),
(3, 10),
(3, 12),
(3, 22),
(4, 7),
(4, 16),
(4, 20),
(4, 21);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `room_type_id` int(11) DEFAULT NULL,
  `room_number` varchar(50) NOT NULL,
  `floor` int(11) DEFAULT NULL,
  `status` enum('Available','Occupied','Cleaning','Maintenance','Hold') DEFAULT 'Available',
  `version` int(11) DEFAULT 1,
  `locked_at` datetime DEFAULT NULL,
  `locked_by_session` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `rooms`
--

INSERT INTO `rooms` (`id`, `room_type_id`, `room_number`, `floor`, `status`, `version`, `locked_at`, `locked_by_session`) VALUES
(1, 1, '101', 1, 'Available', 1, NULL, NULL),
(2, 1, '102', 1, 'Occupied', 1, NULL, NULL),
(3, 2, '201', 2, 'Available', 1, NULL, NULL),
(4, 1, '103', 1, 'Available', 1, NULL, NULL),
(5, 1, '104', 1, 'Cleaning', 1, NULL, NULL),
(6, 1, '105', 1, 'Available', 1, NULL, NULL),
(7, 2, '202', 2, 'Occupied', 1, NULL, NULL),
(8, 2, '203', 2, 'Available', 1, NULL, NULL),
(9, 2, '204', 2, 'Maintenance', 1, NULL, NULL),
(10, 3, '301', 3, 'Available', 1, NULL, NULL),
(11, 3, '302', 3, 'Available', 1, NULL, NULL),
(12, 23, '901', 9, 'Available', 1, NULL, NULL),
(13, 22, '902', 9, 'Available', 1, NULL, NULL),
(14, 21, '903', 9, 'Available', 1, NULL, NULL),
(15, 20, '904', 9, 'Available', 1, NULL, NULL),
(16, 19, '905', 9, 'Available', 1, NULL, NULL),
(17, 18, '906', 9, 'Available', 1, NULL, NULL),
(18, 17, '907', 9, 'Available', 1, NULL, NULL),
(19, 16, '908', 9, 'Available', 1, NULL, NULL),
(20, 15, '909', 9, 'Available', 1, NULL, NULL),
(21, 14, '910', 9, 'Available', 1, NULL, NULL),
(22, 13, '911', 9, 'Available', 1, NULL, NULL),
(23, 12, '912', 9, 'Available', 1, NULL, NULL),
(24, 11, '913', 9, 'Available', 1, NULL, NULL),
(25, 10, '914', 9, 'Available', 1, NULL, NULL),
(26, 9, '915', 9, 'Available', 1, NULL, NULL),
(27, 8, '916', 9, 'Available', 1, NULL, NULL),
(28, 7, '917', 9, 'Available', 1, NULL, NULL),
(29, 6, '918', 9, 'Available', 1, NULL, NULL),
(30, 5, '919', 9, 'Available', 1, NULL, NULL),
(31, 4, '920', 9, 'Available', 1, NULL, NULL),
(32, 23, '921', 9, 'Available', 1, NULL, NULL),
(33, 22, '922', 9, 'Available', 1, NULL, NULL),
(34, 21, '923', 9, 'Available', 1, NULL, NULL),
(35, 20, '924', 9, 'Available', 1, NULL, NULL),
(36, 19, '925', 9, 'Available', 1, NULL, NULL),
(37, 18, '926', 9, 'Available', 1, NULL, NULL),
(38, 17, '927', 9, 'Available', 1, NULL, NULL),
(39, 16, '928', 9, 'Available', 1, NULL, NULL),
(40, 15, '929', 9, 'Available', 1, NULL, NULL),
(41, 14, '930', 9, 'Available', 1, NULL, NULL),
(42, 13, '931', 9, 'Available', 1, NULL, NULL),
(43, 12, '932', 9, 'Available', 1, NULL, NULL),
(44, 11, '933', 9, 'Available', 1, NULL, NULL),
(45, 10, '934', 9, 'Available', 1, NULL, NULL),
(46, 9, '935', 9, 'Available', 1, NULL, NULL),
(47, 8, '936', 9, 'Available', 1, NULL, NULL),
(48, 7, '937', 9, 'Available', 1, NULL, NULL),
(49, 6, '938', 9, 'Available', 1, NULL, NULL),
(50, 5, '939', 9, 'Available', 1, NULL, NULL),
(51, 4, '940', 9, 'Available', 1, NULL, NULL),
(52, 43, '901', 9, 'Available', 1, NULL, NULL),
(53, 42, '902', 9, 'Available', 1, NULL, NULL),
(54, 41, '903', 9, 'Available', 1, NULL, NULL),
(55, 40, '904', 9, 'Available', 1, NULL, NULL),
(56, 39, '905', 9, 'Available', 1, NULL, NULL),
(57, 38, '906', 9, 'Available', 1, NULL, NULL),
(58, 37, '907', 9, 'Available', 1, NULL, NULL),
(59, 36, '908', 9, 'Available', 1, NULL, NULL),
(60, 35, '909', 9, 'Available', 1, NULL, NULL),
(61, 34, '910', 9, 'Available', 1, NULL, NULL),
(62, 33, '911', 9, 'Available', 1, NULL, NULL),
(63, 32, '912', 9, 'Available', 1, NULL, NULL),
(64, 31, '913', 9, 'Available', 1, NULL, NULL),
(65, 30, '914', 9, 'Available', 1, NULL, NULL),
(66, 29, '915', 9, 'Available', 1, NULL, NULL),
(67, 28, '916', 9, 'Available', 1, NULL, NULL),
(68, 27, '917', 9, 'Available', 1, NULL, NULL),
(69, 26, '918', 9, 'Available', 1, NULL, NULL),
(70, 25, '919', 9, 'Available', 1, NULL, NULL),
(71, 24, '920', 9, 'Available', 1, NULL, NULL),
(72, 43, '921', 9, 'Available', 1, NULL, NULL),
(73, 42, '922', 9, 'Available', 1, NULL, NULL),
(74, 41, '923', 9, 'Available', 1, NULL, NULL),
(75, 40, '924', 9, 'Available', 1, NULL, NULL),
(76, 39, '925', 9, 'Available', 1, NULL, NULL),
(77, 38, '926', 9, 'Available', 1, NULL, NULL),
(78, 37, '927', 9, 'Available', 1, NULL, NULL),
(79, 36, '928', 9, 'Available', 1, NULL, NULL),
(80, 35, '929', 9, 'Available', 1, NULL, NULL),
(81, 34, '930', 9, 'Available', 1, NULL, NULL),
(82, 33, '931', 9, 'Available', 1, NULL, NULL),
(83, 32, '932', 9, 'Available', 1, NULL, NULL),
(84, 31, '933', 9, 'Available', 1, NULL, NULL),
(85, 30, '934', 9, 'Available', 1, NULL, NULL),
(86, 29, '935', 9, 'Available', 1, NULL, NULL),
(87, 28, '936', 9, 'Available', 1, NULL, NULL),
(88, 27, '937', 9, 'Available', 1, NULL, NULL),
(89, 26, '938', 9, 'Available', 1, NULL, NULL),
(90, 25, '939', 9, 'Available', 1, NULL, NULL),
(91, 24, '940', 9, 'Available', 1, NULL, NULL),
(92, 44, '1001', 10, 'Available', 1, NULL, NULL),
(93, 44, '1002', 10, 'Available', 1, NULL, NULL),
(94, 44, '1003', 10, 'Available', 1, NULL, NULL),
(95, 45, '1101', 11, 'Available', 1, NULL, NULL),
(96, 45, '1102', 11, 'Available', 1, NULL, NULL),
(97, 45, '1103', 11, 'Available', 1, NULL, NULL),
(98, 46, '1201', 12, 'Available', 1, NULL, NULL),
(99, 46, '1202', 12, 'Available', 1, NULL, NULL),
(100, 46, '1203', 12, 'Available', 1, NULL, NULL),
(101, 47, '1301', 13, 'Available', 1, NULL, NULL),
(102, 47, '1302', 13, 'Available', 1, NULL, NULL),
(103, 47, '1303', 13, 'Available', 1, NULL, NULL),
(104, 48, '1401', 14, 'Available', 1, NULL, NULL),
(105, 48, '1402', 14, 'Available', 1, NULL, NULL),
(106, 48, '1403', 14, 'Available', 1, NULL, NULL),
(107, 49, '1501', 15, 'Available', 1, NULL, NULL),
(108, 49, '1502', 15, 'Available', 1, NULL, NULL),
(109, 49, '1503', 15, 'Available', 1, NULL, NULL),
(110, 50, '1601', 16, 'Available', 1, NULL, NULL),
(111, 50, '1602', 16, 'Available', 1, NULL, NULL),
(112, 50, '1603', 16, 'Available', 1, NULL, NULL),
(113, 51, '1701', 17, 'Available', 1, NULL, NULL),
(114, 51, '1702', 17, 'Available', 1, NULL, NULL),
(115, 51, '1703', 17, 'Available', 1, NULL, NULL),
(116, 52, '1801', 18, 'Available', 1, NULL, NULL),
(117, 52, '1802', 18, 'Available', 1, NULL, NULL),
(118, 52, '1803', 18, 'Available', 1, NULL, NULL),
(119, 53, '1901', 19, 'Available', 1, NULL, NULL),
(120, 53, '1902', 19, 'Available', 1, NULL, NULL),
(121, 53, '1903', 19, 'Available', 1, NULL, NULL),
(122, 54, '2001', 20, 'Available', 1, NULL, NULL),
(123, 54, '2002', 20, 'Available', 1, NULL, NULL),
(124, 54, '2003', 20, 'Available', 1, NULL, NULL),
(125, 55, '2101', 21, 'Available', 1, NULL, NULL),
(126, 55, '2102', 21, 'Available', 1, NULL, NULL),
(127, 55, '2103', 21, 'Available', 1, NULL, NULL),
(128, 56, '2201', 22, 'Available', 1, NULL, NULL),
(129, 56, '2202', 22, 'Available', 1, NULL, NULL),
(130, 56, '2203', 22, 'Available', 1, NULL, NULL),
(131, 57, '2301', 23, 'Available', 1, NULL, NULL),
(132, 57, '2302', 23, 'Available', 1, NULL, NULL),
(133, 57, '2303', 23, 'Available', 1, NULL, NULL),
(134, 58, '2401', 24, 'Available', 1, NULL, NULL),
(135, 58, '2402', 24, 'Available', 1, NULL, NULL),
(136, 58, '2403', 24, 'Available', 1, NULL, NULL),
(137, 59, '2501', 25, 'Available', 1, NULL, NULL),
(138, 59, '2502', 25, 'Available', 1, NULL, NULL),
(139, 59, '2503', 25, 'Available', 1, NULL, NULL),
(140, 60, '2601', 26, 'Available', 1, NULL, NULL),
(141, 60, '2602', 26, 'Available', 1, NULL, NULL),
(142, 60, '2603', 26, 'Available', 1, NULL, NULL),
(143, 61, '2701', 27, 'Available', 1, NULL, NULL),
(144, 61, '2702', 27, 'Available', 1, NULL, NULL),
(145, 61, '2703', 27, 'Available', 1, NULL, NULL),
(146, 62, '2801', 28, 'Available', 1, NULL, NULL),
(147, 62, '2802', 28, 'Available', 1, NULL, NULL),
(148, 62, '2803', 28, 'Available', 1, NULL, NULL),
(149, 63, '2901', 29, 'Available', 1, NULL, NULL),
(150, 63, '2902', 29, 'Available', 1, NULL, NULL),
(151, 63, '2903', 29, 'Available', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `roomtype_amenities`
--

CREATE TABLE `roomtype_amenities` (
  `room_type_id` int(11) NOT NULL,
  `amenity_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `roomtype_amenities`
--

INSERT INTO `roomtype_amenities` (`room_type_id`, `amenity_id`) VALUES
(2, 1),
(2, 2);

-- --------------------------------------------------------

CREATE TABLE `inventory_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `default_price` decimal(18,2) DEFAULT 0.00,
  `stock_quantity` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `inventory_items` (`name`, `default_price`, `stock_quantity`) VALUES
('Bóng đèn LED 9W', 45000.00, 50),
('Pin AAA (Cặp)', 15000.00, 100),
('Khăn tắm cao cấp', 120000.00, 30),
('Vỏ gối cotton', 65000.00, 40),
('Ly thủy tinh', 25000.00, 60),
('Bàn chải đánh răng', 5000.00, 200),
('Kem đánh răng nhỏ', 3000.00, 200),
('Dầu gội gói', 2000.00, 500),
('Sữa tắm gói', 2000.00, 500),
('Dép đi trong phòng', 15000.00, 80),
('Remote TV', 150000.00, 10),
('Remote máy lạnh', 250000.00, 10),
('Túi giặt là', 5000.00, 150),
('Nước suối 500ml', 10000.00, 300),
('Trà túi lọc (Hộp)', 35000.00, 40),
('Cafe hòa tan (Hộp)', 45000.00, 40),
('Premium Bathrobe', 850000.00, 20),
('Mini-bar Red Wine', 450000.00, 30),
('Satin Pillowcase', 120000.00, 50),
('Shoeshine Kit', 25000.00, 100),
('L\'Occitane Shampoo (50ml)', 75000.00, 150),
('L\'Occitane Shower Gel (50ml)', 75000.00, 150),
('Bamboo Toothbrush', 12000.00, 300),
('Artisanal Tea Tin', 180000.00, 40),
('Luxury Sleeping Mask', 55000.00, 60),
('Room Fragrance Mist', 150000.00, 25),
('Universal Charger Adaptor', 350000.00, 15),
('High-quality Stationery Set', 45000.00, 100),
('Mini Umbrella', 120000.00, 40),
('Hand Sanitizer (Luxury)', 35000.00, 200),
('Imported Chocolate Bar', 65000.00, 80),
('Premium Coffee Beans (200g)', 220000.00, 30),
('Cotton Slippers (Extra Soft)', 30000.00, 100),
('Sewing Kit', 5000.00, 200),
('Shower Cap (Sustainable)', 3000.00, 300),
('Bath Bomb (Jasmine)', 85000.00, 50);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `room_inventory`
--

CREATE TABLE `room_inventory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_id` int(11) DEFAULT NULL,
  `room_type_id` int(11) DEFAULT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `price_override` decimal(18,2) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_room_inventory_room` (`room_id`),
  KEY `idx_room_inventory_type` (`room_type_id`),
  KEY `idx_room_inventory_item` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `booking_inventory_items`
--

CREATE TABLE `booking_inventory_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_detail_id` int(11) NOT NULL,
  `item_id` int(11) DEFAULT NULL,
  `item_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_at_booking` decimal(18,2) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_booking_inv_detail` (`booking_detail_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `inventory_damage`
--

CREATE TABLE `inventory_damage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_id` int(11) DEFAULT NULL,
  `room_id` int(11) DEFAULT NULL,
  `booking_detail_id` int(11) NOT NULL,
  `item_id` int(11) DEFAULT NULL,
  `item_name_at_report` varchar(255) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(18,2) NOT NULL,
  `status` enum('Reported','Approved','Paid','Replaced','Rejected') DEFAULT 'Reported',
  `is_fixed` tinyint(1) DEFAULT 0,
  `reported_by` int(11) DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_inv_damage_detail` (`booking_detail_id`),
  KEY `idx_inv_damage_booking` (`booking_id`),
  KEY `idx_inv_damage_room` (`room_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `room_status_logs`
--

CREATE TABLE `room_status_logs` (
  `id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `old_status` varchar(50) DEFAULT NULL,
  `new_status` varchar(50) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `room_types`
--

CREATE TABLE `room_types` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `base_price` decimal(18,2) NOT NULL,
  `capacity_adults` int(11) NOT NULL,
  `capacity_children` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `area_sqm` decimal(10,2) DEFAULT 0.00,
  `is_active` tinyint(1) DEFAULT 1,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `room_types`
--

INSERT INTO `room_types` (`id`, `name`, `slug`, `base_price`, `capacity_adults`, `capacity_children`, `description`, `area_sqm`) VALUES
(1, 'Single Room', 'single-room', 500000.00, 1, 1, 'kakakaakaka', 14.00),
(2, 'Double Room', 'double-room', 800000.00, 2, 1, '', 15.00),
(3, 'Suite', 'suite', 2000000.00, 2, 2, NULL, 0.00),
(4, 'Seeded Room Type 1', 'seeded-room-type-1', 600000.00, 1, 1, 'Mô tả loại phòng 1', 0.00),
(5, 'Seeded Room Type 2', 'seeded-room-type-2', 700000.00, 3, 0, 'Mô tả loại phòng 2', 0.00),
(6, 'Seeded Room Type 3', 'seeded-room-type-3', 800000.00, 3, 2, 'Mô tả loại phòng 3', 0.00),
(7, 'Seeded Room Type 4', 'seeded-room-type-4', 900000.00, 3, 0, 'Mô tả loại phòng 4', 0.00),
(8, 'Seeded Room Type 5', 'seeded-room-type-5', 1000000.00, 3, 2, 'Mô tả loại phòng 5', 0.00),
(9, 'Seeded Room Type 6', 'seeded-room-type-6', 1100000.00, 4, 1, 'Mô tả loại phòng 6', 0.00),
(10, 'Seeded Room Type 7', 'seeded-room-type-7', 1200000.00, 2, 0, 'Mô tả loại phòng 7', 0.00),
(11, 'Seeded Room Type 8', 'seeded-room-type-8', 1300000.00, 3, 0, 'Mô tả loại phòng 8', 0.00),
(12, 'Seeded Room Type 9', 'seeded-room-type-9', 1400000.00, 4, 1, 'Mô tả loại phòng 9', 0.00),
(13, 'Seeded Room Type 10', 'seeded-room-type-10', 1500000.00, 4, 2, 'Mô tả loại phòng 10', 0.00),
(14, 'Seeded Room Type 11', 'seeded-room-type-11', 1600000.00, 3, 0, 'Mô tả loại phòng 11', 0.00),
(15, 'Seeded Room Type 12', 'seeded-room-type-12', 1700000.00, 1, 0, 'Mô tả loại phòng 12', 0.00),
(16, 'Seeded Room Type 13', 'seeded-room-type-13', 1800000.00, 2, 1, 'Mô tả loại phòng 13', 0.00),
(17, 'Seeded Room Type 14', 'seeded-room-type-14', 1900000.00, 1, 1, 'Mô tả loại phòng 14', 0.00),
(18, 'Seeded Room Type 15', 'seeded-room-type-15', 2000000.00, 2, 2, 'Mô tả loại phòng 15', 0.00),
(19, 'Seeded Room Type 16', 'seeded-room-type-16', 2100000.00, 4, 0, 'Mô tả loại phòng 16', 0.00),
(20, 'Seeded Room Type 17', 'seeded-room-type-17', 2200000.00, 4, 0, 'Mô tả loại phòng 17', 0.00),
(21, 'Seeded Room Type 18', 'seeded-room-type-18', 2300000.00, 1, 0, 'Mô tả loại phòng 18', 0.00),
(22, 'Seeded Room Type 19', 'seeded-room-type-19', 2400000.00, 1, 2, 'Mô tả loại phòng 19', 0.00),
(23, 'Seeded Room Type 20', 'seeded-room-type-20', 2500000.00, 1, 0, 'Mô tả loại phòng 20', 0.00),
(24, 'Seeded Room Type 1', 'seeded-room-type-1-2', 600000.00, 4, 1, 'Mô tả loại phòng 1', 0.00),
(25, 'Seeded Room Type 2', 'seeded-room-type-2-2', 700000.00, 3, 2, 'Mô tả loại phòng 2', 0.00),
(26, 'Seeded Room Type 3', 'seeded-room-type-3-2', 800000.00, 1, 2, 'Mô tả loại phòng 3', 0.00),
(27, 'Seeded Room Type 4', 'seeded-room-type-4-2', 900000.00, 4, 1, 'Mô tả loại phòng 4', 0.00),
(28, 'Seeded Room Type 5', 'seeded-room-type-5-2', 1000000.00, 3, 1, 'Mô tả loại phòng 5', 0.00),
(29, 'Seeded Room Type 6', 'seeded-room-type-6-2', 1100000.00, 4, 2, 'Mô tả loại phòng 6', 0.00),
(30, 'Seeded Room Type 7', 'seeded-room-type-7-2', 1200000.00, 3, 1, 'Mô tả loại phòng 7', 0.00),
(31, 'Seeded Room Type 8', 'seeded-room-type-8-2', 1300000.00, 2, 2, 'Mô tả loại phòng 8', 0.00),
(32, 'Seeded Room Type 9', 'seeded-room-type-9-2', 1400000.00, 2, 1, 'Mô tả loại phòng 9', 0.00),
(33, 'Seeded Room Type 10', 'seeded-room-type-10-2', 1500000.00, 2, 2, 'Mô tả loại phòng 10', 0.00),
(34, 'Seeded Room Type 11', 'seeded-room-type-11-2', 1600000.00, 2, 2, 'Mô tả loại phòng 11', 0.00),
(35, 'Seeded Room Type 12', 'seeded-room-type-12-2', 1700000.00, 4, 1, 'Mô tả loại phòng 12', 0.00),
(36, 'Seeded Room Type 13', 'seeded-room-type-13-2', 1800000.00, 2, 0, 'Mô tả loại phòng 13', 0.00),
(37, 'Seeded Room Type 14', 'seeded-room-type-14-2', 1900000.00, 4, 0, 'Mô tả loại phòng 14', 0.00),
(38, 'Seeded Room Type 15', 'seeded-room-type-15-2', 2000000.00, 1, 1, 'Mô tả loại phòng 15', 0.00),
(39, 'Seeded Room Type 16', 'seeded-room-type-16-2', 2100000.00, 1, 0, 'Mô tả loại phòng 16', 0.00),
(40, 'Seeded Room Type 17', 'seeded-room-type-17-2', 2200000.00, 2, 2, 'Mô tả loại phòng 17', 0.00),
(41, 'Seeded Room Type 18', 'seeded-room-type-18-2', 2300000.00, 2, 0, 'Mô tả loại phòng 18', 0.00),
(42, 'Seeded Room Type 19', 'seeded-room-type-19-2', 2400000.00, 2, 0, 'Mô tả loại phòng 19', 0.00),
(43, 'Seeded Room Type 20', 'seeded-room-type-20-2', 2500000.00, 3, 0, 'Mô tả loại phòng 20', 0.00),
(44, 'Executive Sky Suite', 'executive-sky-suite', 4500000.00, 2, 1, 'Premium suite with panoramic city views and exclusive lounge access.', 65.00),
(45, 'Garden Zen Villa', 'garden-zen-villa', 7500000.00, 2, 2, 'Spacious villa surrounded by private Japanese gardens.', 120.00),
(46, 'Royal Penthouse', 'royal-penthouse', 15000000.00, 4, 2, 'Ultimate luxury living on the top floor with private pool.', 250.00),
(47, 'Family Connecting Suite', 'family-connecting-suite', 3200000.00, 4, 2, 'Two interconnected rooms designed for absolute family comfort.', 85.00),
(48, 'Digital Nomad Studio', 'digital-nomad-studio', 1200000.00, 1, 0, 'Compact room optimized for work-from-home with high-speed desk.', 25.00),
(49, 'Ocean Breeze Studio', 'ocean-breeze-studio', 2200000.00, 2, 0, 'Wake up to the sound of waves in this minimalist beachfront studio.', 40.00),
(50, 'Heritage Loft', 'heritage-loft', 2800000.00, 2, 1, 'Classic design meets modern comfort in this industrial-style loft.', 55.00),
(51, 'Eco-Friendly Cocoon', 'eco-friendly-cocoon', 1500000.00, 2, 0, 'Sustainability-focused room with organic materials.', 30.00),
(52, 'Artisan Suite', 'artisan-suite', 3500000.00, 2, 1, 'Decorated with local art, featuring a dedicated creative space.', 50.00),
(53, 'Skyline Balcony Room', 'skyline-balcony-room', 2100000.00, 2, 0, 'Modern room with a large private balcony overlooking the skyline.', 38.00),
(54, 'Romantic Hideaway', 'romantic-hideaway', 3800000.00, 2, 0, 'Perfect for couples, featuring a sunken bathtub and candlelit service.', 45.00),
(55, 'Business Class Single', 'business-class-single', 1800000.00, 1, 0, 'Tailored for the professional traveler with ergonomic furniture.', 28.00),
(56, 'Luxury Triple Room', 'luxury-triple-room', 3000000.00, 3, 0, 'Ideal for friends or family, featuring three full-sized beds.', 55.00),
(57, 'Poolside Terrace Room', 'poolside-terrace-room', 3900000.00, 2, 2, 'Direct access to the swimming pool from your private terrace.', 50.00),
(58, 'Urban Shelter Pod', 'urban-shelter-pod', 900000.00, 1, 0, 'Smart use of space for the essential traveler.', 18.00),
(59, 'Presidents Suite', 'presidents-suite', 12000000.00, 4, 2, 'Formal dining area, separate living room, and world-class service.', 180.00),
(60, 'Sun-Drenched Corner', 'sun-drenched-corner', 2400000.00, 2, 1, 'Double aspect windows flooding the room with natural light.', 42.00),
(61, 'Quiet Sanctuary', 'quiet-sanctuary', 2600000.00, 2, 0, 'Enhanced soundproofing and sleep-optimized features.', 35.00),
(62, 'Modern Library Room', 'modern-library-room', 2700000.00, 2, 0, 'surrounded by books, featuring a cozy reading nook.', 48.00),
(63, 'Pet-Friendly Garden Room', 'pet-friendly-garden-room', 2500000.00, 2, 1, 'Features an enclosed garden area for your furry friends.', 40.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `room_type_amenities`
--

CREATE TABLE `room_type_amenities` (
  `room_type_id` int(11) NOT NULL,
  `amenity_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `room_type_inventory`
--

CREATE TABLE `room_type_inventory` (
  `room_type_id` int(11) NOT NULL,
  `inventory_item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `service_type_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `capacity` int(11) DEFAULT 1,
  `price_per_hour` decimal(18,2) NOT NULL,
  `status` enum('AVAILABLE','MAINTENANCE') DEFAULT 'AVAILABLE'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `services`
--

INSERT INTO `services` (`id`, `service_type_id`, `name`, `capacity`, `price_per_hour`, `status`) VALUES
(1, 1, 'Grand Ballroom A', 250, 4500000.00, 'AVAILABLE'),
(2, 1, 'Sky Lounge Event Hall', 120, 2800000.00, 'AVAILABLE'),
(3, 2, 'VIP Table Riverside', 12, 1800000.00, 'AVAILABLE'),
(4, 2, 'Chef Table Signature', 8, 2200000.00, 'MAINTENANCE'),
(5, 3, 'Meeting Pod M1', 10, 650000.00, 'AVAILABLE'),
(6, 3, 'Meeting Pod M2', 8, 550000.00, 'AVAILABLE'),
(7, 1, 'Crystal Ballroom', 500, 8500000.00, 'AVAILABLE'),
(8, 1, 'Zen Garden Pavilion', 80, 2500000.00, 'AVAILABLE'),
(9, 1, 'Art Gallery Hall', 150, 3200000.00, 'AVAILABLE'),
(10, 1, 'Grand Library Hall', 60, 1800000.00, 'AVAILABLE'),
(11, 1, 'Penthouse Deck', 40, 5500000.00, 'AVAILABLE'),
(12, 1, 'Basement Theatre', 100, 2200000.00, 'AVAILABLE'),
(13, 2, 'Table for Two - Terrace', 2, 1500000.00, 'AVAILABLE'),
(14, 2, 'The Wine Vault Dining', 6, 3500000.00, 'AVAILABLE'),
(15, 2, 'Sushi Counter VIP', 4, 1200000.00, 'AVAILABLE'),
(16, 2, 'Private Balcony Brasserie', 4, 1600000.00, 'AVAILABLE'),
(17, 2, 'Sky Lounge Corner', 5, 2000000.00, 'AVAILABLE'),
(18, 2, 'Chef\'s Garden Table', 8, 2500000.00, 'AVAILABLE'),
(19, 3, 'Boardroom Sigma', 20, 1200000.00, 'AVAILABLE'),
(20, 3, 'Collaboration Hub 1', 12, 850000.00, 'AVAILABLE'),
(21, 3, 'Private Cabin C1', 2, 350000.00, 'AVAILABLE'),
(22, 3, 'Executive Lounge A', 6, 950000.00, 'AVAILABLE'),
(23, 3, 'Innovation Lab', 25, 1800000.00, 'AVAILABLE'),
(24, 3, 'Media Studio 1', 5, 1500000.00, 'AVAILABLE'),
(25, 3, 'Quiet Zone Studio', 4, 600000.00, 'AVAILABLE'),
(26, 3, 'Workshop Space Z', 30, 2100000.00, 'AVAILABLE');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `service_bookings`
--

CREATE TABLE `service_bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `service_id` int(11) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `total_price` decimal(18,2) NOT NULL,
  `status` enum('PENDING','CONFIRMED','CANCELLED') DEFAULT 'PENDING',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `service_types`
--

CREATE TABLE `service_types` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `service_types`
--

INSERT INTO `service_types` (`id`, `name`, `slug`, `description`) VALUES
(1, 'Event Room', 'event-room', 'Phong su kien dat theo gio'),
(2, 'VIP Dining Table', 'vip-dining-table', 'Ban an VIP dat theo khung gio'),
(3, 'Meeting Space', 'meeting-space', 'Khong gian hop nhom nho');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `system_logs`
--

CREATE TABLE `system_logs` (
  `id` int(11) NOT NULL,
  `trace_id` varchar(100) DEFAULT NULL,
  `actor_id` int(11) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `resource_type` varchar(100) DEFAULT NULL,
  `resource_id` int(11) DEFAULT NULL,
  `before_json` longtext DEFAULT NULL,
  `after_json` longtext DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `severity` varchar(50) NOT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tags`
--

CREATE TABLE `tags` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `tags`
--

INSERT INTO `tags` (`id`, `name`, `slug`) VALUES
(1, 'Tag 1', 'tag-1'),
(2, 'Tag 2', 'tag-2'),
(3, 'Tag 3', 'tag-3'),
(4, 'Tag 4', 'tag-4'),
(5, 'Tag 5', 'tag-5'),
(6, 'Tag 6', 'tag-6'),
(7, 'Tag 7', 'tag-7'),
(8, 'Tag 8', 'tag-8'),
(9, 'Tag 9', 'tag-9'),
(10, 'Tag 10', 'tag-10'),
(11, 'Tag 11', 'tag-11'),
(12, 'Tag 12', 'tag-12'),
(13, 'Tag 13', 'tag-13'),
(14, 'Tag 14', 'tag-14'),
(15, 'Tag 15', 'tag-15'),
(16, 'Tag 16', 'tag-16'),
(17, 'Tag 17', 'tag-17'),
(18, 'Tag 18', 'tag-18'),
(19, 'Tag 19', 'tag-19'),
(20, 'Tag 20', 'tag-20');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `membership_id` int(11) DEFAULT 1,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `password_hash` text NOT NULL,
  `points` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `failed_login_attempts` int(11) DEFAULT 0,
  `locked_until` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `role_id`, `membership_id`, `full_name`, `email`, `phone`, `password_hash`, `points`, `status`, `created_at`, `deleted_at`, `updated_at`) VALUES
(1, 1, 1, 'Admin System', 'admin@hotel.com', '0123456789', '$2y$10$wErhg8K7B5KrvV71ciK8lOhYHGThq2fiYfqi9UXI6RD.IxJYh3ugW', 0, 1, '2026-03-22 13:42:27', NULL, '2026-03-25 23:55:12'),
(2, 3, 2, 'Nguyen Van A', 'customerA@gmail.com', '0988777666', 'hash_pass_abc', 0, 1, '2026-03-22 13:42:27', NULL, '2026-03-25 23:55:12'),
(3, 3, 3, 'Tran Thi B', 'customerB@gmail.com', '0911222333', 'hash_pass_bcd', 0, 1, '2026-03-22 13:42:28', NULL, '2026-03-25 23:55:12'),
(4, 2, 1, 'Le Van C (Staff)', 'staffC@hotel.com', '0933444555', 'hash_pass_staff', 0, 1, '2026-03-22 13:42:28', NULL, '2026-03-25 23:55:12'),
(5, 3, 1, 'Seeded User 1', 'user1_seed@hotel.com', '090000001', 'hash123', 1394, 1, '2026-03-22 21:42:29', NULL, '2026-03-25 23:55:12'),
(6, 3, 1, 'Seeded User 2', 'user2_seed@hotel.com', '090000002', 'hash123', 343, 1, '2026-03-22 21:42:29', NULL, '2026-03-25 23:55:12'),
(7, 3, 1, 'Seeded User 3', 'user3_seed@hotel.com', '090000003', 'hash123', 1316, 1, '2026-03-22 21:42:29', NULL, '2026-03-25 23:55:12'),
(8, 3, 1, 'Seeded User 4', 'user4_seed@hotel.com', '090000004', 'hash123', 182, 1, '2026-03-22 21:42:29', NULL, '2026-03-25 23:55:12'),
(9, 3, 1, 'Seeded User 5', 'user5_seed@hotel.com', '090000005', 'hash123', 187, 1, '2026-03-22 21:42:29', NULL, '2026-03-25 23:55:12'),
(10, 3, 1, 'Seeded User 6', 'user6_seed@hotel.com', '090000006', 'hash123', 8, 1, '2026-03-22 21:42:29', NULL, '2026-03-25 23:55:12'),
(11, 3, 1, 'Seeded User 7', 'user7_seed@hotel.com', '090000007', 'hash123', 504, 1, '2026-03-22 21:42:29', NULL, '2026-03-25 23:55:12'),
(12, 3, 1, 'Seeded User 8', 'user8_seed@hotel.com', '090000008', 'hash123', 368, 1, '2026-03-22 21:42:29', NULL, '2026-03-25 23:55:12'),
(13, 3, 1, 'Seeded User 9', 'user9_seed@hotel.com', '090000009', 'hash123', 724, 1, '2026-03-22 21:42:29', NULL, '2026-03-25 23:55:12'),
(14, 3, 1, 'Seeded User 10', 'user10_seed@hotel.com', '0900000010', 'hash123', 498, 1, '2026-03-22 21:42:29', NULL, '2026-03-25 23:55:12'),
(15, 3, 1, 'Seeded User 11', 'user11_seed@hotel.com', '0900000011', 'hash123', 923, 1, '2026-03-22 21:42:29', NULL, '2026-03-25 23:55:12'),
(16, 3, 1, 'Seeded User 12', 'user12_seed@hotel.com', '0900000012', 'hash123', 123, 1, '2026-03-22 21:42:29', NULL, '2026-03-25 23:55:12'),
(17, 3, 1, 'Seeded User 13', 'user13_seed@hotel.com', '0900000013', 'hash123', 878, 1, '2026-03-22 21:42:29', NULL, '2026-03-25 23:55:12'),
(18, 3, 1, 'Seeded User 14', 'user14_seed@hotel.com', '0900000014', 'hash123', 670, 1, '2026-03-22 21:42:29', NULL, '2026-03-25 23:55:12'),
(19, 3, 1, 'Seeded User 15', 'user15_seed@hotel.com', '0900000015', 'hash123', 1398, 1, '2026-03-22 21:42:29', NULL, '2026-03-25 23:55:12'),
(20, 3, 1, 'Seeded User 16', 'user16_seed@hotel.com', '0900000016', 'hash123', 1317, 1, '2026-03-22 21:42:29', NULL, '2026-03-25 23:55:12'),
(21, 3, 1, 'Seeded User 17', 'user17_seed@hotel.com', '0900000017', 'hash123', 1241, 1, '2026-03-22 21:42:29', NULL, '2026-03-25 23:55:12'),
(22, 3, 1, 'Seeded User 18', 'user18_seed@hotel.com', '0900000018', 'hash123', 1265, 1, '2026-03-22 21:42:29', NULL, '2026-03-25 23:55:12'),
(23, 3, 1, 'Seeded User 19', 'user19_seed@hotel.com', '0900000019', 'hash123', 89, 1, '2026-03-22 21:42:29', NULL, '2026-03-25 23:55:12'),
(24, 5, 1, 'Seeded User 20', 'user20_seed@hotel.com', '0900000020', 'hash123', 397, 0, '2026-03-22 21:42:29', NULL, '2026-04-12 21:28:27'),
(25, 2, 1, 'Phúc', 'phuc123@gmail.com', '', '$2y$10$xWUqqH4Pooc/FXa.SPN2bOVAxz53Iq47AQSruM8Tw0qnXVoLezhRK', 0, 0, '2026-03-26 11:51:39', NULL, '2026-04-12 18:43:37');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user_roles`
--

CREATE TABLE `user_roles` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `user_roles`
--

INSERT INTO `user_roles` (`user_id`, `role_id`, `expires_at`) VALUES
(1, 1, NULL),
(2, 3, NULL),
(3, 3, NULL),
(4, 2, NULL),
(5, 3, NULL),
(6, 3, NULL),
(7, 3, NULL),
(8, 3, NULL),
(9, 3, NULL),
(10, 3, NULL),
(11, 3, NULL),
(12, 3, NULL),
(13, 3, NULL),
(14, 3, NULL),
(15, 3, NULL),
(16, 3, NULL),
(17, 3, NULL),
(18, 3, NULL),
(19, 3, NULL),
(20, 3, NULL),
(21, 3, NULL),
(22, 3, NULL),
(23, 3, NULL),
(24, 5, NULL),
(25, 2, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `vouchers`
--

CREATE TABLE `vouchers` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `discount_type` varchar(50) NOT NULL,
  `discount_value` decimal(18,2) NOT NULL,
  `max_discount` decimal(18,2) DEFAULT NULL,
  `room_type_id_limit` int(11) DEFAULT NULL,
  `max_uses` int(11) DEFAULT NULL,
  `max_uses_per_user` int(11) DEFAULT NULL,
  `min_booking_value` decimal(18,2) DEFAULT 0.00,
  `valid_from` datetime DEFAULT NULL,
  `valid_to` datetime DEFAULT NULL,
  `usage_limit` int(11) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `vouchers`
--

INSERT INTO `vouchers` (`id`, `code`, `discount_type`, `discount_value`, `max_discount`, `room_type_id_limit`, `max_uses`, `max_uses_per_user`, `min_booking_value`, `valid_from`, `valid_to`, `usage_limit`, `is_active`) VALUES
(1, 'HELLO2024', 'Percentage', 10.00, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 1),
(2, 'VOUCHER_1_130', 'Fixed', 81425.00, NULL, NULL, 100, NULL, 0.00, NULL, NULL, 100, 1),
(3, 'VOUCHER_2_411', 'Percentage', 16.00, 249488.00, NULL, 100, NULL, 0.00, NULL, NULL, 100, 1),
(4, 'VOUCHER_3_742', 'Fixed', 246573.00, NULL, NULL, 100, NULL, 0.00, NULL, NULL, 100, 1),
(5, 'VOUCHER_4_640', 'Percentage', 43.00, 421805.00, NULL, 100, NULL, 0.00, NULL, NULL, 100, 1),
(6, 'VOUCHER_5_226', 'Fixed', 245972.00, NULL, NULL, 100, NULL, 0.00, NULL, NULL, 100, 1),
(7, 'VOUCHER_6_424', 'Percentage', 19.00, 304860.00, NULL, 100, NULL, 0.00, NULL, NULL, 100, 1),
(8, 'VOUCHER_7_341', 'Fixed', 401952.00, NULL, NULL, 100, NULL, 0.00, NULL, NULL, 100, 1),
(9, 'VOUCHER_8_947', 'Percentage', 19.00, 115661.00, NULL, 100, NULL, 0.00, NULL, NULL, 100, 1),
(10, 'VOUCHER_9_820', 'Fixed', 466716.00, NULL, NULL, 100, NULL, 0.00, NULL, NULL, 100, 1),
(11, 'VOUCHER_10_580', 'Percentage', 43.00, 112198.00, NULL, 100, NULL, 0.00, NULL, NULL, 100, 1),
(12, 'VOUCHER_11_325', 'Fixed', 249939.00, NULL, NULL, 100, NULL, 0.00, NULL, NULL, 100, 1),
(13, 'VOUCHER_12_826', 'Percentage', 32.00, 331753.00, NULL, 100, NULL, 0.00, NULL, NULL, 100, 1),
(14, 'VOUCHER_13_759', 'Fixed', 268507.00, NULL, NULL, 100, NULL, 0.00, NULL, NULL, 100, 1),
(15, 'VOUCHER_14_966', 'Percentage', 26.00, 278748.00, NULL, 100, NULL, 0.00, NULL, NULL, 100, 1),
(16, 'VOUCHER_15_262', 'Fixed', 369424.00, NULL, NULL, 100, NULL, 0.00, NULL, NULL, 100, 1),
(17, 'VOUCHER_16_234', 'Percentage', 9.00, 451250.00, NULL, 100, NULL, 0.00, NULL, NULL, 100, 1),
(18, 'VOUCHER_17_697', 'Fixed', 377522.00, NULL, NULL, 100, NULL, 0.00, NULL, NULL, 100, 1),
(19, 'VOUCHER_18_167', 'Percentage', 15.00, 297673.00, NULL, 100, NULL, 0.00, NULL, NULL, 100, 1),
(20, 'VOUCHER_19_825', 'Fixed', 425991.00, NULL, NULL, 100, NULL, 0.00, NULL, NULL, 100, 1),
(21, 'VOUCHER_20_173', 'Percentage', 9.00, 268864.00, NULL, 100, NULL, 0.00, NULL, NULL, 100, 1),
(22, 'VOUCHER_1_964', 'Fixed', 254955.00, NULL, NULL, 100, NULL, 0.00, NULL, NULL, 100, 1),
(23, 'VOUCHER_2_537', 'Percentage', 40.00, 248585.00, NULL, 100, NULL, 0.00, NULL, NULL, 100, 1),
(24, 'VOUCHER_3_402', 'Fixed', 452394.00, NULL, NULL, 100, NULL, 0.00, NULL, NULL, 100, 1),
(25, 'VOUCHER_4_665', 'Percentage', 16.00, 290207.00, NULL, 100, NULL, 0.00, NULL, NULL, 100, 1),
(26, 'VOUCHER_5_514', 'Fixed', 431491.00, NULL, NULL, 100, NULL, 0.00, NULL, NULL, 100, 1),
(27, 'VOUCHER_6_250', 'Percentage', 38.00, 259800.00, NULL, 100, NULL, 0.00, NULL, NULL, 100, 1),
(28, 'VOUCHER_7_227', 'Fixed', 151664.00, NULL, NULL, 100, NULL, 0.00, NULL, NULL, 100, 1),
(29, 'VOUCHER_8_830', 'Percentage', 8.00, 383453.00, NULL, 100, NULL, 0.00, NULL, NULL, 100, 1),
(30, 'VOUCHER_9_112', 'Fixed', 383156.00, NULL, NULL, 100, NULL, 0.00, NULL, NULL, 100, 1),
(31, 'VOUCHER_10_926', 'Percentage', 30.00, 374598.00, NULL, 100, NULL, 0.00, NULL, NULL, 100, 1),
(32, 'VOUCHER_11_319', 'Fixed', 336228.00, NULL, NULL, 100, NULL, 0.00, NULL, NULL, 100, 1),
(33, 'VOUCHER_12_844', 'Percentage', 48.00, 134861.00, NULL, 100, NULL, 0.00, NULL, NULL, 100, 1),
(34, 'VOUCHER_13_250', 'Fixed', 97302.00, NULL, NULL, 100, NULL, 0.00, NULL, NULL, 100, 1),
(35, 'VOUCHER_14_809', 'Percentage', 20.00, 131821.00, NULL, 100, NULL, 0.00, NULL, NULL, 100, 1),
(36, 'VOUCHER_15_999', 'Fixed', 482043.00, NULL, NULL, 100, NULL, 0.00, NULL, NULL, 100, 1),
(37, 'VOUCHER_16_201', 'Percentage', 22.00, 383461.00, NULL, 100, NULL, 0.00, NULL, NULL, 100, 1),
(38, 'VOUCHER_17_524', 'Fixed', 212845.00, NULL, NULL, 100, NULL, 0.00, NULL, NULL, 100, 1),
(39, 'VOUCHER_18_660', 'Percentage', 9.00, 468698.00, NULL, 100, NULL, 0.00, NULL, NULL, 100, 1),
(40, 'VOUCHER_19_831', 'Fixed', 62911.00, NULL, NULL, 100, NULL, 0.00, NULL, NULL, 100, 1),
(41, 'VOUCHER_20_986', 'Percentage', 35.00, 218357.00, NULL, 100, NULL, 0.00, NULL, NULL, 100, 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `voucher_usages`
--

CREATE TABLE `voucher_usages` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `voucher_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `used_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `addons`
--
ALTER TABLE `addons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Chỉ mục cho bảng `addon_categories`
--
ALTER TABLE `addon_categories`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `amenities`
--
ALTER TABLE `amenities`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `author_id` (`author_id`);

--
-- Chỉ mục cho bảng `article_categories`
--
ALTER TABLE `article_categories`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `article_tags`
--
ALTER TABLE `article_tags`
  ADD PRIMARY KEY (`article_id`,`tag_id`),
  ADD KEY `tag_id` (`tag_id`);

--
-- Chỉ mục cho bảng `attractions`
--
ALTER TABLE `attractions`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `booking_code` (`booking_code`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `voucher_id` (`voucher_id`);

--
-- Chỉ mục cho bảng `booking_details`
--
ALTER TABLE `booking_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Chỉ mục cho bảng `booking_logs`
--
ALTER TABLE `booking_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Chỉ mục cho bảng `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `review_id` (`review_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Chỉ mục cho bảng `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Chỉ mục cho bảng `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `loss_and_damages`
--
ALTER TABLE `loss_and_damages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_detail_id` (`booking_detail_id`);

--
-- Chỉ mục cho bảng `loyalty_memberships`
--
ALTER TABLE `loyalty_memberships`
  ADD PRIMARY KEY (`user_id`);

--
-- Chỉ mục cho bảng `memberships`
--
ALTER TABLE `memberships`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `is_read` (`is_read`),
  ADD KEY `created_at` (`created_at`),
  ADD KEY `idx_notifications_user_unread` (`user_id`,`is_read`),
  ADD KEY `idx_notifications_booking` (`booking_id`,`created_at`);

--
-- Chỉ mục cho bảng `order_services`
--
ALTER TABLE `order_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_detail_id` (`booking_detail_id`);

--
-- Chỉ mục cho bảng `order_service_details`
--
ALTER TABLE `order_service_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_service_id` (`order_service_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Chỉ mục cho bảng `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payment_ref` (`payment_ref`),
  ADD UNIQUE KEY `idempotency_key` (`idempotency_key`),
  ADD KEY `invoice_id` (`invoice_id`);

--
-- Chỉ mục cho bảng `payment_logs`
--
ALTER TABLE `payment_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_id` (`payment_id`);

--
-- Chỉ mục cho bảng `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `point_history`
--
ALTER TABLE `point_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `invoice_id` (`invoice_id`);

--
-- Chỉ mục cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_booking_review` (`booking_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`role_id`,`permission_id`),
  ADD KEY `permission_id` (`permission_id`);

--
-- Chỉ mục cho bảng `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_type_id` (`room_type_id`);

--
-- Chỉ mục cho bảng `roomtype_amenities`
--
ALTER TABLE `roomtype_amenities`
  ADD PRIMARY KEY (`room_type_id`,`amenity_id`),
  ADD KEY `amenity_id` (`amenity_id`);

--
-- Chỉ mục cho bảng `room_inventory`
--
ALTER TABLE `room_inventory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_id` (`room_id`);

--
-- Chỉ mục cho bảng `room_inventory_items`
--
ALTER TABLE `room_inventory_items`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `room_status_logs`
--
ALTER TABLE `room_status_logs`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `room_types`
--
ALTER TABLE `room_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Chỉ mục cho bảng `room_type_amenities`
--
ALTER TABLE `room_type_amenities`
  ADD PRIMARY KEY (`room_type_id`,`amenity_id`),
  ADD KEY `amenity_id` (`amenity_id`);

--
-- Chỉ mục cho bảng `room_type_inventory`
--
ALTER TABLE `room_type_inventory`
  ADD PRIMARY KEY (`room_type_id`,`inventory_item_id`),
  ADD KEY `inventory_item_id` (`inventory_item_id`);

--
-- Chỉ mục cho bảng `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `service_bookings`
--
ALTER TABLE `service_bookings`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `service_types`
--
ALTER TABLE `service_types`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `system_logs`
--
ALTER TABLE `system_logs`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `membership_id` (`membership_id`);

--
-- Chỉ mục cho bảng `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `role_id` (`role_id`);

--
-- Chỉ mục cho bảng `vouchers`
--
ALTER TABLE `vouchers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Chỉ mục cho bảng `voucher_usages`
--
ALTER TABLE `voucher_usages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `voucher_id` (`voucher_id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `addons`
--
ALTER TABLE `addons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `addon_categories`
--
ALTER TABLE `addon_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `amenities`
--
ALTER TABLE `amenities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `articles`
--
ALTER TABLE `articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT cho bảng `article_categories`
--
ALTER TABLE `article_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT cho bảng `attractions`
--
ALTER TABLE `attractions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT cho bảng `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT cho bảng `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `booking_details`
--
ALTER TABLE `booking_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `booking_logs`
--
ALTER TABLE `booking_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `images`
--
ALTER TABLE `images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `invoice_items`
--
ALTER TABLE `invoice_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `loss_and_damages`
--
ALTER TABLE `loss_and_damages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `memberships`
--
ALTER TABLE `memberships`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `order_services`
--
ALTER TABLE `order_services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `order_service_details`
--
ALTER TABLE `order_service_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `payment_logs`
--
ALTER TABLE `payment_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT cho bảng `point_history`
--
ALTER TABLE `point_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT cho bảng `room_inventory`
--
ALTER TABLE `room_inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `room_inventory_items`
--
ALTER TABLE `room_inventory_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `room_status_logs`
--
ALTER TABLE `room_status_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `room_types`
--
ALTER TABLE `room_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT cho bảng `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `service_bookings`
--
ALTER TABLE `service_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `service_types`
--
ALTER TABLE `service_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `system_logs`
--
ALTER TABLE `system_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT cho bảng `vouchers`
--
ALTER TABLE `vouchers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT cho bảng `voucher_usages`
--
ALTER TABLE `voucher_usages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `addons`
--
ALTER TABLE `addons`
  ADD CONSTRAINT `addons_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `addon_categories` (`id`);

--
-- Các ràng buộc cho bảng `articles`
--
ALTER TABLE `articles`
  ADD CONSTRAINT `articles_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `article_categories` (`id`),
  ADD CONSTRAINT `articles_ibfk_2` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`);

--
-- Các ràng buộc cho bảng `article_tags`
--
ALTER TABLE `article_tags`
  ADD CONSTRAINT `article_tags_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `article_tags_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Các ràng buộc cho bảng `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`voucher_id`) REFERENCES `vouchers` (`id`);

--
-- Các ràng buộc cho bảng `booking_details`
--
ALTER TABLE `booking_details`
  ADD CONSTRAINT `booking_details_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`),
  ADD CONSTRAINT `booking_details_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`);

--
-- Các ràng buộc cho bảng `booking_logs`
--
ALTER TABLE `booking_logs`
  ADD CONSTRAINT `booking_logs_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`);

--
-- Các ràng buộc cho bảng `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`review_id`) REFERENCES `reviews` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `comments_ibfk_3` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`);

--
-- Các ràng buộc cho bảng `loss_and_damages`
--
ALTER TABLE `loss_and_damages`
  ADD CONSTRAINT `loss_and_damages_ibfk_1` FOREIGN KEY (`booking_detail_id`) REFERENCES `booking_details` (`id`);

--
-- Các ràng buộc cho bảng `loyalty_memberships`
--
ALTER TABLE `loyalty_memberships`
  ADD CONSTRAINT `loyalty_memberships_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `order_services`
--
ALTER TABLE `order_services`
  ADD CONSTRAINT `order_services_ibfk_1` FOREIGN KEY (`booking_detail_id`) REFERENCES `booking_details` (`id`);

--
-- Các ràng buộc cho bảng `order_service_details`
--
ALTER TABLE `order_service_details`
  ADD CONSTRAINT `order_service_details_ibfk_1` FOREIGN KEY (`order_service_id`) REFERENCES `order_services` (`id`),
  ADD CONSTRAINT `order_service_details_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `addons` (`id`);

--
-- Các ràng buộc cho bảng `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`);

--
-- Các ràng buộc cho bảng `payment_logs`
--
ALTER TABLE `payment_logs`
  ADD CONSTRAINT `payment_logs_ibfk_1` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`);

--
-- Các ràng buộc cho bảng `point_history`
--
ALTER TABLE `point_history`
  ADD CONSTRAINT `point_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `point_history_ibfk_2` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`);

--
-- Các ràng buộc cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`);

--
-- Các ràng buộc cho bảng `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `rooms_ibfk_1` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`id`);

--
-- Các ràng buộc cho bảng `roomtype_amenities`
--
ALTER TABLE `roomtype_amenities`
  ADD CONSTRAINT `roomtype_amenities_ibfk_1` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`id`),
  ADD CONSTRAINT `roomtype_amenities_ibfk_2` FOREIGN KEY (`amenity_id`) REFERENCES `amenities` (`id`);

--
-- Các ràng buộc cho bảng `room_inventory`
--
ALTER TABLE `room_inventory`
  ADD CONSTRAINT `room_inventory_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`);

--
-- Các ràng buộc cho bảng `room_type_amenities`
--
ALTER TABLE `room_type_amenities`
  ADD CONSTRAINT `room_type_amenities_ibfk_1` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `room_type_amenities_ibfk_2` FOREIGN KEY (`amenity_id`) REFERENCES `amenities` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `room_type_inventory`
--
ALTER TABLE `room_type_inventory`
  ADD CONSTRAINT `room_type_inventory_ibfk_1` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `room_type_inventory_ibfk_2` FOREIGN KEY (`inventory_item_id`) REFERENCES `room_inventory_items` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`membership_id`) REFERENCES `memberships` (`id`);

--
-- Các ràng buộc cho bảng `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `voucher_usages`
--
ALTER TABLE `voucher_usages`
  ADD CONSTRAINT `voucher_usages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `voucher_usages_ibfk_2` FOREIGN KEY (`voucher_id`) REFERENCES `vouchers` (`id`),
  ADD CONSTRAINT `voucher_usages_ibfk_3` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- =========================================================
-- RBAC Upgrade (dynamic + deny-by-default + soft delete + version)
-- Chỉ thêm cột mới để tương thích ngược (không thay đổi dữ liệu hiện có).
-- =========================================================

ALTER TABLE `roles`
  ADD COLUMN `code` VARCHAR(50) DEFAULT NULL,
  ADD COLUMN `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  ADD COLUMN `sort_order` INT NOT NULL DEFAULT 0,
  ADD COLUMN `permission_version` INT NOT NULL DEFAULT 1,
  ADD COLUMN `deleted_at` DATETIME DEFAULT NULL,
  ADD COLUMN `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  ADD COLUMN `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

ALTER TABLE `permissions`
  ADD COLUMN `code` VARCHAR(50) DEFAULT NULL,
  ADD COLUMN `module` VARCHAR(100) DEFAULT NULL,
  ADD COLUMN `description` TEXT DEFAULT NULL,
  ADD COLUMN `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  ADD COLUMN `permission_version` INT NOT NULL DEFAULT 1,
  ADD COLUMN `deleted_at` DATETIME DEFAULT NULL,
  ADD COLUMN `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  ADD COLUMN `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

ALTER TABLE `user_roles`
  ADD COLUMN `assigned_by` INT DEFAULT NULL,
  ADD COLUMN `assigned_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  ADD COLUMN `expired_at` DATETIME DEFAULT NULL,
  ADD COLUMN `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  ADD COLUMN `deleted_at` DATETIME DEFAULT NULL;

ALTER TABLE `role_permissions`
  ADD COLUMN `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP;

-- Backfill fields mới từ schema cũ
UPDATE `roles` SET `code` = `name` WHERE `code` IS NULL;
UPDATE `permissions`
  SET `code` = `name`,
      `module` = COALESCE(`module`, `block_module`),
      `description` = COALESCE(`description`, `name`)
  WHERE `code` IS NULL OR `module` IS NULL OR `description` IS NULL;

-- RBAC global version (invalidate cache đúng lúc cho toàn hệ)
CREATE TABLE IF NOT EXISTS `rbac_permission_versions` (
  `id` INT NOT NULL PRIMARY KEY,
  `permission_version` INT NOT NULL DEFAULT 1,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `rbac_permission_versions` (`id`, `permission_version`)
VALUES (1, 1);

-- =========================================================
-- User/Profile upgrade (identity + profile + sessions + activity + reset)
-- Không thay auth cũ, chỉ bổ sung bảng/cột để nâng cấp dần.
-- =========================================================

ALTER TABLE `users`
  ADD COLUMN `email_verified_at` DATETIME DEFAULT NULL,
  ADD COLUMN `phone_verified_at` DATETIME DEFAULT NULL,
  ADD COLUMN `last_login_at` DATETIME DEFAULT NULL,
  ADD COLUMN `last_login_ip` VARCHAR(45) DEFAULT NULL,
  ADD COLUMN `avatar_url` VARCHAR(255) DEFAULT NULL,
  ADD COLUMN `profile_completion_percent` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  ADD COLUMN `failed_login_attempts` INT NOT NULL DEFAULT 0,
  ADD COLUMN `locked_until` DATETIME DEFAULT NULL;

ALTER TABLE `users`
  ADD UNIQUE KEY `uk_users_email` (`email`),
  ADD UNIQUE KEY `uk_users_phone` (`phone`);

CREATE TABLE IF NOT EXISTS `user_profiles` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `avatar_url` VARCHAR(255) DEFAULT NULL,
  `address_line` VARCHAR(255) DEFAULT NULL,
  `ward` VARCHAR(100) DEFAULT NULL,
  `district` VARCHAR(100) DEFAULT NULL,
  `city` VARCHAR(100) DEFAULT NULL,
  `country` VARCHAR(100) DEFAULT 'Việt Nam',
  `postal_code` VARCHAR(20) DEFAULT NULL,
  `admin_note` TEXT DEFAULT NULL,
  `profile_completion_percent` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `email_verified_at` DATETIME DEFAULT NULL,
  `phone_verified_at` DATETIME DEFAULT NULL,
  `last_login_at` DATETIME DEFAULT NULL,
  `last_login_ip` VARCHAR(45) DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_user_profiles_user_id` (`user_id`),
  CONSTRAINT `fk_user_profiles_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `user_session_logs` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `session_id` VARCHAR(128) NOT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `user_agent` TEXT DEFAULT NULL,
  `last_activity_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `logged_out_at` DATETIME DEFAULT NULL,
  `is_current` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_user_session_logs_user_id` (`user_id`),
  KEY `idx_user_session_logs_session_id` (`session_id`),
  CONSTRAINT `fk_user_session_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `account_activity_logs` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `action` VARCHAR(100) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `user_agent` TEXT DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_account_activity_logs_user_id` (`user_id`),
  KEY `idx_account_activity_logs_action` (`action`),
  CONSTRAINT `fk_account_activity_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `token` VARCHAR(128) NOT NULL,
  `expires_at` DATETIME NOT NULL,
  `used_at` DATETIME DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_password_reset_tokens_token` (`token`),
  KEY `idx_password_reset_tokens_user_id` (`user_id`),
  CONSTRAINT `fk_password_reset_tokens_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `user_profiles` (
  `user_id`,
  `avatar_url`,
  `profile_completion_percent`,
  `email_verified_at`,
  `phone_verified_at`,
  `last_login_at`,
  `last_login_ip`
)
SELECT
  u.`id`,
  u.`avatar_url`,
  CASE
    WHEN u.`full_name` IS NOT NULL AND u.`full_name` <> ''
      AND u.`email` IS NOT NULL AND u.`email` <> ''
      AND u.`phone` IS NOT NULL AND u.`phone` <> ''
    THEN 50
    ELSE 20
  END,
  u.`email_verified_at`,
  u.`phone_verified_at`,
  u.`last_login_at`,
  u.`last_login_ip`
FROM `users` u
LEFT JOIN `user_profiles` up ON up.`user_id` = u.`id`
WHERE up.`user_id` IS NULL;

UPDATE `users`
SET `profile_completion_percent` = CASE
  WHEN `full_name` IS NOT NULL AND `full_name` <> ''
    AND `email` IS NOT NULL AND `email` <> ''
    AND `phone` IS NOT NULL AND `phone` <> ''
  THEN 50
  ELSE 20
END
WHERE `profile_completion_percent` = 0;

-- =========================================================
-- Phase 1 hardening (RBAC/User/Profile alignment)
-- Tiep tuc quy schema ve mot nguon duy nhat la hotel.sql.
-- =========================================================

UPDATE `user_roles`
SET `expired_at` = `expires_at`
WHERE `expired_at` IS NULL
  AND `expires_at` IS NOT NULL;

ALTER TABLE `roles`
  ADD UNIQUE KEY `uk_roles_code` (`code`),
  ADD KEY `idx_roles_active_deleted` (`is_active`, `deleted_at`);

ALTER TABLE `permissions`
  ADD KEY `idx_permissions_module_active_deleted` (`module`, `is_active`, `deleted_at`);

ALTER TABLE `user_roles`
  ADD KEY `idx_user_roles_user_active_deleted` (`user_id`, `is_active`, `deleted_at`),
  ADD KEY `idx_user_roles_role_active_deleted` (`role_id`, `is_active`, `deleted_at`),
  ADD KEY `idx_user_roles_expired_at` (`expired_at`);

ALTER TABLE `audit_logs`
  ADD COLUMN `ip_address` VARCHAR(45) DEFAULT NULL AFTER `record_id`,
  ADD COLUMN `user_agent` TEXT DEFAULT NULL AFTER `ip_address`,
  ADD COLUMN `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP AFTER `user_agent`;

-- =========================================================
-- Phase 2 hardening (Room / Room Type alignment)
-- Room policy data + normalized status while keeping old flow.
-- =========================================================

ALTER TABLE `room_types`
  ADD COLUMN `max_occupancy` INT NULL DEFAULT NULL AFTER `capacity_children`,
  ADD COLUMN `bed_type` VARCHAR(100) DEFAULT NULL AFTER `max_occupancy`,
  ADD COLUMN `room_size` VARCHAR(100) DEFAULT NULL AFTER `area_sqm`,
  ADD COLUMN `amenities_default` JSON DEFAULT NULL AFTER `room_size`,
  ADD COLUMN `refund_policy` TEXT DEFAULT NULL AFTER `amenities_default`,
  ADD COLUMN `min_stay` INT NOT NULL DEFAULT 1 AFTER `refund_policy`,
  ADD COLUMN `max_stay` INT NOT NULL DEFAULT 30 AFTER `min_stay`,
  ADD COLUMN `seasonal_pricing_rule` TEXT DEFAULT NULL AFTER `max_stay`;

UPDATE `room_types`
SET `max_occupancy` = CASE
    WHEN `max_occupancy` IS NOT NULL AND `max_occupancy` > 0 THEN `max_occupancy`
    ELSE GREATEST(COALESCE(`capacity_adults`, 0) + COALESCE(`capacity_children`, 0), 1)
END,
`room_size` = CASE
    WHEN `room_size` IS NULL OR `room_size` = '' THEN CONCAT(TRIM(TRAILING '.00' FROM FORMAT(COALESCE(`area_sqm`, 0), 2)), ' sqm')
    ELSE `room_size`
END,
`min_stay` = CASE WHEN `min_stay` < 1 THEN 1 ELSE `min_stay` END,
`max_stay` = CASE WHEN `max_stay` < 1 THEN 30 ELSE `max_stay` END;

ALTER TABLE `rooms`
  ADD COLUMN `display_status` VARCHAR(50) DEFAULT NULL AFTER `status`,
  ADD COLUMN `notes` TEXT DEFAULT NULL AFTER `display_status`;

UPDATE `rooms`
SET `display_status` = CASE `status`
    WHEN 'Available' THEN 'available'
    WHEN 'Hold' THEN 'reserved'
    WHEN 'Occupied' THEN 'occupied'
    WHEN 'Cleaning' THEN 'cleaning'
    WHEN 'Maintenance' THEN 'maintenance'
    ELSE 'available'
END
WHERE `display_status` IS NULL OR `display_status` = '';

INSERT IGNORE INTO `room_type_amenities` (`room_type_id`, `amenity_id`)
SELECT `room_type_id`, `amenity_id`
FROM `roomtype_amenities`;

INSERT IGNORE INTO `roomtype_amenities` (`room_type_id`, `amenity_id`)
SELECT `room_type_id`, `amenity_id`
FROM `room_type_amenities`;

ALTER TABLE `rooms`
  ADD KEY `idx_rooms_type_display_status` (`room_type_id`, `display_status`),
  ADD KEY `idx_rooms_display_status` (`display_status`);

-- =========================================================
-- Phase 3 hardening (Booking lifecycle alignment)
-- Add normalized booking_status/payment_status without breaking old flow.
-- =========================================================

ALTER TABLE `bookings`
  ADD COLUMN `booking_status` VARCHAR(50) DEFAULT NULL AFTER `status`,
  ADD COLUMN `payment_status` VARCHAR(50) DEFAULT NULL AFTER `booking_status`;

UPDATE `bookings`
SET `booking_status` = CASE `status`
    WHEN 'draft' THEN 'draft'
    WHEN 'pending' THEN 'pending_payment'
    WHEN 'processing' THEN 'pending_payment'
    WHEN 'paid' THEN 'confirmed'
    WHEN 'confirmed' THEN 'confirmed'
    WHEN 'checked_in' THEN 'checked_in'
    WHEN 'checked_out' THEN 'checked_out'
    WHEN 'completed' THEN 'completed'
    WHEN 'cancelled' THEN 'cancelled'
    WHEN 'expired' THEN 'expired'
    WHEN 'no_show' THEN 'no_show'
    ELSE 'pending_payment'
END
WHERE `booking_status` IS NULL OR `booking_status` = '';

UPDATE `bookings`
SET `payment_status` = CASE `status`
    WHEN 'draft' THEN 'unpaid'
    WHEN 'pending_payment' THEN 'unpaid'
    WHEN 'paid' THEN 'paid'
    WHEN 'confirmed' THEN 'paid'
    WHEN 'checked_in' THEN 'paid'
    WHEN 'checked_out' THEN 'paid'
    WHEN 'completed' THEN 'paid'
    WHEN 'cancelled' THEN 'refunding'
    WHEN 'expired' THEN 'overdue'
    WHEN 'no_show' THEN 'overdue'
    ELSE 'unpaid'
END
WHERE `payment_status` IS NULL OR `payment_status` = '';

ALTER TABLE `bookings`
  ADD KEY `idx_bookings_booking_status` (`booking_status`),
  ADD KEY `idx_bookings_payment_status` (`payment_status`);

-- =========================================================
-- Phase 5-7 hardening (Availability / Service / Payment)
-- =========================================================

CREATE TABLE IF NOT EXISTS `room_availability_projection` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `status` varchar(50) NOT NULL,
  `reserved_count` int(11) NOT NULL DEFAULT 0,
  `occupied_count` int(11) NOT NULL DEFAULT 0,
  `maintenance_count` int(11) NOT NULL DEFAULT 0,
  `blocked_count` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_room_date` (`room_id`,`date`),
  KEY `idx_projection_date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `room_inventory_ledger` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_id` int(11) NOT NULL,
  `action` enum('reserve','release','occupy','clean','maintenance','damage','repair','loss') NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `issue_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_room_inventory_ledger_room` (`room_id`),
  KEY `idx_room_inventory_ledger_booking` (`booking_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `invoices`
  ADD COLUMN `room_amount` decimal(18,2) DEFAULT 0.00 AFTER `booking_id`,
  ADD COLUMN `service_amount` decimal(18,2) DEFAULT 0.00 AFTER `room_amount`,
  ADD COLUMN `addon_amount` decimal(18,2) DEFAULT 0.00 AFTER `service_amount`,
  ADD COLUMN `late_fee` decimal(18,2) DEFAULT 0.00 AFTER `addon_amount`,
  ADD COLUMN `damage_fee` decimal(18,2) DEFAULT 0.00 AFTER `late_fee`,
  ADD COLUMN `prepaid_amount` decimal(18,2) DEFAULT 0.00 AFTER `discount_amount`,
  ADD COLUMN `balance_due` decimal(18,2) DEFAULT 0.00 AFTER `final_total`;

ALTER TABLE `payments`
  ADD COLUMN `gateway` varchar(50) DEFAULT NULL AFTER `payment_method`,
  ADD COLUMN `external_txn_id` varchar(255) DEFAULT NULL AFTER `gateway`,
  ADD COLUMN `raw_payload` longtext DEFAULT NULL AFTER `payment_ref`,
  ADD COLUMN `paid_at` datetime DEFAULT NULL AFTER `payment_date`;

CREATE TABLE IF NOT EXISTS `payment_transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) NOT NULL,
  `gateway` varchar(50) NOT NULL,
  `external_txn_id` varchar(255) DEFAULT NULL,
  `amount` decimal(18,2) NOT NULL,
  `status` varchar(50) NOT NULL,
  `paid_at` datetime DEFAULT NULL,
  `raw_payload` longtext DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_gateway_txn` (`gateway`,`external_txn_id`),
  KEY `idx_payment_transactions_invoice` (`invoice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `service_bookings`
  MODIFY `status` enum('requested','reserved','active','completed','cancelled','expired') DEFAULT 'requested';

ALTER TABLE `reviews`
  ADD UNIQUE KEY `uniq_reviews_booking_user` (`booking_id`,`user_id`);

ALTER TABLE `system_logs`
  ADD KEY `idx_system_logs_level_message_created` (`level`,`message`,`created_at`);

-- --------------------------------------------------------
-- UPDATES FOR PREMIUM ROOM DETAILS (2026-04-18)
-- --------------------------------------------------------

ALTER TABLE room_types 
ADD COLUMN check_in_time VARCHAR(20) DEFAULT '14:00',
ADD COLUMN check_out_time VARCHAR(20) DEFAULT '12:00',
ADD COLUMN policy_check_in TEXT,
ADD COLUMN policy_children TEXT,
ADD COLUMN policy_cancel TEXT;

CREATE TABLE IF NOT EXISTS room_type_inventory (
    room_type_id INT,
    inventory_item_id INT,
    quantity INT DEFAULT 1,
    PRIMARY KEY (room_type_id, inventory_item_id),
    FOREIGN KEY (room_type_id) REFERENCES room_types(id) ON DELETE CASCADE,
    FOREIGN KEY (inventory_item_id) REFERENCES inventory_items(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

