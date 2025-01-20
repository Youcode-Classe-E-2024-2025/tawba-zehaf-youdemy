<?php

namespace Youdemy\Config;

use PDO;
use PDOException;
class Database {

    private $host = 'localhost';
    private $dbname = 'youdemy';
    private $username = 'root'; 
    private $password = '';

    private static $instance = null;

    private $connection;
    private $pdo;
    public function __construct() {
        // Database connection parameters
        $host = 'localhost';
        $db = 'your_database_name';
        $user = 'your_username';
        $pass = 'your_password';
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->connection = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public function getConnection() {
        return $this->connection;
    }
    public function __construct() {
        $this->connection = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->username, $this->password);
    }



    public static function getInstance() {

        if (self::$instance == null) {

            self::$instance = new Database();

        }

        return self::$instance;

    }



    public function query($sql, $params = []) {

        $stmt = $this->connection->prepare($sql);

        $stmt->execute($params);

        return $stmt;

    }
    
    

        public function beginTransaction(): void {
            $this->connection->beginTransaction();
        }
        public function commit(): void {

            $this->connection->commit();
    
        }
        public function rollBack(): void {

            $this->connection->rollBack();
    
        }
        public function lastInsertId() {
            return $this->connection->lastInsertId();
        }
    }

   