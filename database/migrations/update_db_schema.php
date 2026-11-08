<?php
try {
    $db = new PDO("mysql:host=localhost;dbname=hotel;charset=utf8mb4", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Add submit_token to reviews
    $db->exec("ALTER TABLE reviews ADD COLUMN submit_token VARCHAR(64) DEFAULT NULL");
    echo "Added submit_token to reviews\n";
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "submit_token already exists\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}

try {
    // Recreate system_logs
    $db->exec("DROP TABLE IF EXISTS system_logs");
    $db->exec("CREATE TABLE `system_logs` (
      `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
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
    )");
    echo "Recreated system_logs\n";
} catch (Exception $e) {
    echo "Error recreating system_logs: " . $e->getMessage() . "\n";
}

