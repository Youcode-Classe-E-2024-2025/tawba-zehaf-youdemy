<?php

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'youdemy');
define('DB_USER', 'root');
define('DB_PASS', 'secret');

// Application paths
define('BASE_PATH', dirname(__DIR__));
define('VIEW_PATH', BASE_PATH . '/src/Views/');
define('CONTROLLER_PATH', BASE_PATH . '/src/Controllers/');

// Default settings
define('DEFAULT_VIEW', 'home');
define('NOT_FOUND_VIEW', '404');

// Other settings
define('SITE_NAME', 'YouDemy');
define('SITE_URL', 'http://localhost/youdemy');

// Error reporting (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

// Load the Router class
require_once BASE_PATH . '/src/Router.php';

// Load the Database class
require_once BASE_PATH . 'Database.php';

// Initialize the Router
$router = new Router(VIEW_PATH, DEFAULT_VIEW, NOT_FOUND_VIEW, CONTROLLER_PATH);

// Database connection function
function getDbConnection() {
    return Database::getInstance()->getConnection();
}