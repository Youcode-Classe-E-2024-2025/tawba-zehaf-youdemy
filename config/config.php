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
define('MODEL_PATH', BASE_PATH . '/src/Models/');

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

// Autoloader function
function autoloader($class) {
    $paths = [
        CONTROLLER_PATH,
        MODEL_PATH,
        BASE_PATH . '/src/'
    ];

    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
}

// Register autoloader
spl_autoload_register('autoloader');

// Initialize the Router
$router = new Router();

// Add routes
$router->addRoute('GET', '/', function() {
    $controller = new HomeController();
    return $controller->index();
});

$router->addRoute('GET', '/courses', function() {
    $controller = new CourseController();
    return $controller->index();
});

$router->addRoute('GET', '/courses/:id', function($params) {
    $controller = new CourseController();
    return $controller->show($params['id']);
});

$router->addRoute('GET', '/register', function() {
    $controller = new AuthController();
    return $controller->register();
});

$router->addRoute('POST', '/register', function() {
    $controller = new AuthController();
    return $controller->register();
});

$router->addRoute('GET', '/login', function() {
    $controller = new AuthController();
    return $controller->login();
});

$router->addRoute('POST', '/login', function() {
    $controller = new AuthController();
    return $controller->login();
});

$router->addRoute('GET', '/logout', function() {
    $controller = new AuthController();
    return $controller->logout();
});

// Set 404 handler
$router->setNotFoundHandler(function() {
    http_response_code(404);
    $controller = new HomeController();
    $controller->render('404.php', ['title' => 'Page Not Found']);
});

// Database connection function
function getDbConnection() {
    return Database::getInstance()->getConnection();
}