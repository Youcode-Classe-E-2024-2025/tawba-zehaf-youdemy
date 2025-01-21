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

// Private constructor to prevent direct instantiation
public function __construct() {
$charset = 'utf8mb4';
$dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$charset}";

$options = [
PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
PDO::ATTR_EMULATE_PREPARES => false,
];

try {
$this->connection = new PDO($dsn, $this->username, $this->password, $options);
} catch (PDOException $e) {
echo "Connection failed: " . $e->getMessage();
exit; // Exit if connection fails
}
}

// Get the singleton instance of the Database
public static function getInstance() {
if (self::$instance == null) {
self::$instance = new Database();
}
return self::$instance;
}

// Get the PDO connection
public function getConnection() {
return $this->connection;
}

// Prepare a SQL statement
public function prepare($sql) {
return $this->connection->prepare($sql);
}

public function query($sql, $params = []) {
$stmt = $this->prepare($sql); // Use the new prepare method
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