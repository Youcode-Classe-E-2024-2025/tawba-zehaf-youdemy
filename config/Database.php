<?php

define('DB_HOST', 'localhost');
define('DB_NAME', 'youdemy');
define('DB_USER', 'root');
define('DB_PASS', '');

class Database
{
    private static $instance = null;
    private $connection;

    private function __construct()
    {
        $host = DB_HOST;
        $dbname = DB_NAME;
        $username = DB_USER;
        $password = DB_PASS;

        try {
            $this->connection = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function query($sql, $params = [])
    {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            die("Query failed: " . $e->getMessage());
        }
    }

    public function fetch($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }

    public function fetchAll($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    public function insert($table, $data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        
        $this->query($sql, array_values($data));
        return $this->connection->lastInsertId();
    }

    public function update($table, $data, $where, $whereParams = [])
    {
        $set = implode(' = ?, ', array_keys($data)) . ' = ?';
        $sql = "UPDATE $table SET $set WHERE $where";
        
        $params = array_merge(array_values($data), $whereParams);
        $this->query($sql, $params);
    }

    public function delete($table, $where, $params = [])
    {
        $sql = "DELETE FROM $table WHERE $where";
        $this->query($sql, $params);
    }
}