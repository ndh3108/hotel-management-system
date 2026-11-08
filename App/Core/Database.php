<?php

namespace App\Core;

use PDO;
use PDOException;

/**
 * Class Database - Kết nối PDO singleton chuẩn MVC
 * Load cấu hình từ Config/database.php và cung cấp getConnection() cho Model
 */
class Database
{
    private static ?Database $instance = null;
    private ?PDO $pdo = null;
    private array $config;

    private function __construct()
    {
        $configPath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'database.php';

        if (!is_file($configPath)) {
            throw new PDOException('File cấu hình database không tồn tại: ' . $configPath);
        }

        $this->config = require $configPath;
        $this->connect();
    }

    /**
     * Lấy instance duy nhất của Database
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Tạo kết nối PDO
     */
    private function connect(): void
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $this->config['host'],
            $this->config['port'] ?? 3306,
            $this->config['dbname'],
            $this->config['charset'] ?? 'utf8mb4'
        );

        try {
            $this->pdo = new PDO(
                $dsn,
                $this->config['username'],
                $this->config['password'],
                $this->config['options'] ?? []
            );
        } catch (PDOException $e) {
            throw new PDOException(
                'Kết nối database thất bại: ' . $e->getMessage(),
                (int) $e->getCode()
            );
        }
    }

    /**
     * Trả về đối tượng PDO để Model sử dụng (query, prepare, ...)
     */
    public function getConnection(): PDO
    {
        return $this->pdo;
    }

    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    public function rollBack(): bool
    {
        return $this->pdo->rollBack();
    }

    /**
     * Ngăn clone
     */
    private function __clone() {}

    /**
     * Ngăn unserialize
     */
    public function __wakeup()
    {
        throw new PDOException('Cannot unserialize singleton');
    }
}
