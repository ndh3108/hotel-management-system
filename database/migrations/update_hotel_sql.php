<?php
$sql = file_get_contents('hotel.sql');

// 1. Update reviews table
$reviews_old = "`rating_room` int(11) DEFAULT 5 CHECK (`rating_room` between 1 and 5),";
$reviews_new = "`submit_token` varchar(64) DEFAULT NULL,
  `rating_room` int(11) DEFAULT 5 CHECK (`rating_room` between 1 and 5),";
if (strpos($sql, '`submit_token` varchar(64)') === false) {
    $sql = str_replace($reviews_old, $reviews_new, $sql);
}

// 2. Update system_logs table
$system_logs_old = "CREATE TABLE `system_logs` (
  `id` int(11) NOT NULL,
  `level` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `context` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
)";
$system_logs_new = "CREATE TABLE `system_logs` (
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
)";
if (strpos($sql, '`trace_id`') === false && strpos($sql, $system_logs_old) !== false) {
    $sql = str_replace($system_logs_old, $system_logs_new, $sql);
}

// Write back
file_put_contents('hotel.sql', $sql);
echo "hotel.sql updated\n";

