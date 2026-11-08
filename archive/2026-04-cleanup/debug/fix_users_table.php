<?php
require_once 'Config/database.php';

try {
    $dbConfig = require 'Config/database.php';
    $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['dbname']};charset={$dbConfig['charset']}";
    $db = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], $dbConfig['options']);
    
    // Check if column exists
    $stmt = $db->query("SHOW COLUMNS FROM users LIKE 'failed_login_attempts'");
    if ($stmt->rowCount() == 0) {
        $db->exec("ALTER TABLE users 
                   ADD COLUMN failed_login_attempts INT DEFAULT 0,
                   ADD COLUMN locked_until DATETIME DEFAULT NULL");
        echo "Columns failed_login_attempts and locked_until added to users table.\n";
    } else {
        echo "Columns already exist in database.\n";
    }

} catch (Exception $e) {
    echo "DB Error: " . $e->getMessage() . "\n";
}

// Update hotel.sql file as well
$sqlFile = 'hotel.sql';
if (file_exists($sqlFile)) {
    $content = file_get_contents($sqlFile);
    if (strpos($content, '`failed_login_attempts` int(11) DEFAULT 0') === false) {
        $oldCols = "`updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()";
        $newCols = "`updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),\n  `failed_login_attempts` int(11) DEFAULT 0,\n  `locked_until` datetime DEFAULT NULL";
        
        $newContent = str_replace($oldCols, $newCols, $content);
        if ($newContent !== $content) {
            file_put_contents($sqlFile, $newContent);
            echo "Updated hotel.sql\n";
        } else {
            echo "Failed to replace in hotel.sql, please check manually if needed.\n";
        }
    } else {
        echo "hotel.sql already contains the columns.\n";
    }
}

