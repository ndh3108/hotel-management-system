<?php
require_once 'Config/database.php';

try {
    $dbConfig = require 'Config/database.php';
    $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['dbname']};charset={$dbConfig['charset']}";
    $db = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], $dbConfig['options']);
    
    $content = file_get_contents('hotel.sql');
    
    // Find the marker: UPDATE `user_roles`
    $marker = "UPDATE `user_roles`\nSET `expired_at` = `expires_at`";
    $pos = strpos($content, $marker);
    if ($pos !== false) {
        $remaining = substr($content, $pos);
        // Split by semicolon (rough but effective enough for this, considering we avoid internal semicolons in strings if possible)
        // Better: use PDO block exec if it allows multiple statements, or just do it by matching query boundaries.
        // Easiest is just $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, 1); and passing the entire chunk.
        
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        
        // Remove standard comments
        $clean = preg_replace('/-- .+/', '', $remaining);
        
        // Split into statements
        $statements = array_filter(array_map('trim', explode(';', $clean)));
        foreach ($statements as $stmtSql) {
            try {
                $db->exec($stmtSql);
            } catch (Exception $e) {
                if (strpos($e->getMessage(), 'Duplicate column name') === false && 
                    strpos($e->getMessage(), 'Duplicate key name') === false) {
                    echo "Stmt Error: " . $e->getMessage() . " on query: " . substr($stmtSql, 0, 50) . "...\n";
                }
            }
        }
        echo "Executed all ALTER and UPDATE patches from hotel.sql.\n";
    } else {
        echo "Marker not found in hotel.sql\n";
    }

} catch (Exception $e) {
    echo "DB Error: " . $e->getMessage() . "\n";
}

