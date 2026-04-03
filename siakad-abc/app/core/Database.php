<?php
/**
 * Database Connection Class
 * Singleton pattern untuk koneksi database
 */

class Database {
    private static $instance = null;
    private $connection;
    private $config;
    
    private function __construct() {
        $this->config = require __DIR__ . '/../config/database.php';
        
        try {
            $dsn = sprintf(
                "mysql:host=%s;port=%s;dbname=%s;charset=%s",
                $this->config['host'],
                $this->config['port'],
                $this->config['database'],
                $this->config['charset']
            );
            
            $this->connection = new PDO($dsn, $this->config['username'], $this->config['password'], $this->config['options']);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            die("Database connection failed. Please check your configuration.");
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    // Prevent cloning
    private function __clone() {}
    
    // Prevent unserialization
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}
