<?php
namespace Youdemy;

use \PDOException;
use \PDO;

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'youdemy');
define('DB_USER', 'root');
define('DB_PASS', '');

// Application paths
define('BASE_PATH', dirname(__DIR__));
define('VIEW_PATH', BASE_PATH . '/src/Views/');
define('CONTROLLER_PATH', BASE_PATH . '/src/Controllers/');
define('MODEL_PATH', BASE_PATH . '/src/Models/');

// Application configuration
define('APP_NAME', 'Youdemy');
define('APP_URL', 'http://localhost/tawba_zehaf_youdemy');
define('APP_ENV', 'development');

// Default settings
define('DEFAULT_CONTROLLER', 'Home');
define('DEFAULT_ACTION', 'index');
define('SITE_NAME', 'YouDemy');
define('SITE_URL', 'http://localhost/youdemy');

// Session configuration
ini_set('session.cookie_lifetime', 86400); // 24 hours
ini_set('session.gc_maxlifetime', 86400); // 24 hours

// Error reporting
if (APP_ENV == 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} elseif (APP_ENV == 'production') {
    error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
    ini_set('display_errors', 0);
}

// Timezone
date_default_timezone_set('Europe/Paris');

// Start session
session_start();

// Autoloader function
spl_autoload_register(function ($class) {
    $prefix = '';
    $base_dir = __DIR__ . '/../src/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

// Database connection
function getDbConnection() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    return $pdo;
}

// Helper function for redirecting
function redirect($path) {
    header("Location: " . SITE_URL . $path);
    exit();
}

// Helper function for displaying flash messages
function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}

// Load environment-specific configuration
$env = getenv('APP_ENV') ?: 'development';
if (file_exists(BASE_PATH . "/config/{$env}.php")) {
    require_once BASE_PATH . "/config/{$env}.php";
}