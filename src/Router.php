<?php 
namespace Youdemy;
use Youdemy\Config\Database;
use Youdemy\Repository\UserRepository;

class Router {
    private $routes = [];
    private $notFoundHandler;

    public function get($path, $handler) {
        $this->routes['GET'][$path] = $handler;
    }

    public function post($path, $handler) {
        $this->routes['POST'][$path] = $handler;
    }

    public function setNotFoundHandler($handler) {
        $this->notFoundHandler = $handler;
    }

    public function dispatch($requestMethod, $requestUri) {
        $path = parse_url($requestUri, PHP_URL_PATH);
        
        if (isset($this->routes[$requestMethod][$path])) {
            $handler = $this->routes[$requestMethod][$path];
            $this->executeHandler($handler);
        } else {
            $this->handleNotFound();
        }
    }

    private function executeHandler($handler) {
        if (is_callable($handler)) {
            call_user_func($handler);
        } elseif (is_string($handler)) {
            list($controller, $method) = explode('@', $handler);
            $controllerClass = "Youdemy\\Controllers\\$controller";
    
            if (!class_exists($controllerClass)) {
                throw new \RuntimeException("Controller class $controllerClass not found");
            }
    
            // Assuming you have a way to get these dependencies
            $database = new Database(); // Adjust as necessary
            $userRepository = new UserRepository($database); // Adjust as necessary
            $anotherDependency = null; // Define or instantiate as necessary
    
            $controllerInstance = new $controllerClass($database, $userRepository, $anotherDependency);
            if (!method_exists($controllerInstance, $method)) {
                throw new \RuntimeException("Method $method not found in controller $controllerClass");
            }
    
            call_user_func([$controllerInstance, $method]);
        }
    }

    private function handleNotFound() {
        if ($this->notFoundHandler) {
            call_user_func($this->notFoundHandler);
        } else {
            http_response_code(404);
            echo "404 Not Found";
        }
    }
}