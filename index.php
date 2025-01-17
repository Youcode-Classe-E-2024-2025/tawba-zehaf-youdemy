<?php
// Start session
session_start();

// Autoload classes (consider using Composer in the future)
// spl_autoload_register(function ($class) {
//     $class = str_replace('Youdemy\\', '', $class);
//     $file = __DIR__ . '/src/' . str_replace('\\', '/', $class) . '.php';
//     if (file_exists($file)) {
//         require_once $file;
//     }
// });

// Load configuration
require_once __DIR__ . '/config/Database.php';

// Initialize Router
$router = new Router();

// Define routes
$router->get('/', function() {
    require_once __DIR__ . '/src/Views/home_view.php';
});

$router->get('/login', function() {
    require_once __DIR__ . '/src/Views/login_view.php';
});

$router->post('/login', function() {
    $controller = new AuthController();
    $controller->login();
});

$router->get('/register', function() {
    require_once __DIR__ . '/src/Views/register_view.php';
});

$router->post('/register', function() {
    $controller = new AuthController();
    $controller->register();
});

$router->get('/logout', function() {
    $controller = new AuthController();
    $controller->logout();
});

// Handle 404 errors
$router->setNotFoundHandler(function() {
    http_response_code(404);
    require_once __DIR__ . '/src/Views/404_view.php';
});

// Dispatch the request
$router->dispatch();