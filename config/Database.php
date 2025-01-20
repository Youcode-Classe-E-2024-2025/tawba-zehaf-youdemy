<?php

namespace Youdemy\Config;

use PDO;
use PDOException;

class Database {

    private static $instance = null;

    private $connection;
    private $pdo;

    private function __construct() {

        $this->connection = new PDO("mysql:host=localhost;dbname=yourdbname", "username", "password");

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
    
    
    
    
        public function getConnection() {
    
            return $this->connection;
    
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