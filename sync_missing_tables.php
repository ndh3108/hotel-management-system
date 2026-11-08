<?php
require_once 'Config/database.php';

try {
    $dbConfig = require 'Config/database.php';
    $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['dbname']};charset={$dbConfig['charset']}";
    $db = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], $dbConfig['options']);
    
    $content = file_get_contents('hotel.sql');
    
    // Extract CREATE TABLE IF NOT EXISTS statements
    preg_match_all('/CREATE TABLE IF NOT EXISTS `.*?` \(.*?\).*?;/s', $content, $matches);
    
    if (!empty($matches[0])) {
        foreach ($matches[0] as $sql) {
            $db->exec($sql);
            preg_match('/`([^`]+)`/', $sql, $tblMatch);
            echo "Created/ensured table: " . ($tblMatch[1] ?? 'unknown') . "\n";
        }
    } else {
        echo "No CREATE TABLE IF NOT EXISTS found.\n";
    }
} catch (Exception $e) {
    echo "DB Error: " . $e->getMessage() . "\n";
}

