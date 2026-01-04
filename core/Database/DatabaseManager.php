<?php
class DatabaseManager {
    private static $instance;
    private $pdo;
    private $config;
    
    private function __construct() {
        $this->config = [
            'host' => $_ENV['DB_HOST'] ?? 'localhost',
            'database' => $_ENV['DB_NAME'] ?? 'maithili_gallery',
            'username' => $_ENV['DB_USER'] ?? 'root',
            'password' => $_ENV['DB_PASS'] ?? '',
            'charset' => 'utf8mb4',
            'options' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        ];
        
        $this->connect();
    }
    
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function connect() {
        try {
            $dsn = "mysql:host={$this->config['host']};dbname={$this->config['database']};charset={$this->config['charset']}";
            $this->pdo = new PDO($dsn, $this->config['username'], $this->config['password'], $this->config['options']);
        } catch (PDOException $e) {
            ErrorHandler::logError([
                'type' => 'DATABASE_CONNECTION',
                'message' => $e->getMessage(),
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            throw new Exception('Database connection failed');
        }
    }
    
    public function getPDO() {
        return $this->pdo;
    }
    
    public function query($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            ErrorHandler::logError([
                'type' => 'DATABASE_QUERY',
                'message' => $e->getMessage(),
                'sql' => $sql,
                'params' => $params,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            throw new Exception('Database query failed');
        }
    }
    
    public function fetchOne($sql, $params = []) {
        return $this->query($sql, $params)->fetch();
    }
    
    public function fetchAll($sql, $params = []) {
        return $this->query($sql, $params)->fetchAll();
    }
    
    public function execute($sql, $params = []) {
        return $this->query($sql, $params)->rowCount();
    }
    
    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }
    
    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }
    
    public function commit() {
        return $this->pdo->commit();
    }
    
    public function rollback() {
        return $this->pdo->rollback();
    }
}
?>