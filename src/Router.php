<?php 
namespace Youdemy;


use Youdemy\Config\Database;
use Youdemy\Repository\UserRepository;
 class Router { private $routes=[]; private $notFoundHandler;
     public function addRoute($method,
     $path, $handler) { $this->routes[$method][$this->normalizePath($path)] = $handler;
     }

     public function setNotFoundHandler($handler)
     {
     $this->notFoundHandler = $handler;
     }

     public function get($path, $handler)
     {
     $this->addRoute('GET', $path, $handler);
     }

     public function post($path, $handler)
     {
     $this->addRoute('POST', $path, $handler);
     }

     public function dispatch($method, $uri)
     {
     $path = $this->normalizePath(parse_url($uri, PHP_URL_PATH));

     if (isset($this->routes[$method][$path])) {
     $handler = $this->routes[$method][$path];
     $this->executeHandler($handler);
     } else {
     $this->handleNotFound();
     }
     }

     private function normalizePath($path)
     {
     return '/' . trim($path, '/');
     } private function executeHandler($handler, $params = [])
     {
         if (is_callable($handler)) {
             call_user_func($handler, ...$params);
         } elseif (is_string($handler)) {
             list($controller, $method) = explode('@', $handler);
             $controllerClass = "\\Youdemy\\Controllers\\$controller";
     
             if (!class_exists($controllerClass)) {
                 throw new \RuntimeException("Controller class $controllerClass not found");
             }
     
             // Create an instance of the controller with dependencies
             $database = new Database(); // Assuming Database has a default constructor
             $userRepository = new UserRepository($database); // Assuming UserRepository requires a Database instance
             
             $controllerInstance = new $controllerClass($database, $userRepository);
     
             if (!method_exists($controllerInstance, $method)) {
                 throw new \RuntimeException("Method $method not found in controller $controllerClass");
             }
             call_user_func([$controllerInstance, $method], ...$params);
         }
     }

    //  private function executeHandler($handler)
    //  {
    //  if (is_callable($handler)) {
    //  call_user_func($handler);
    //  } elseif (is_string($handler)) {
    //  // Split the handler into controller and method
    //  list($controller, $method) = explode('@', $handler);

    //  // Remove redundant "Youdemy\Controllers" part if present
    //  $controller = str_replace('Youdemy\\Controllers\\', '', $controller); // Remove redundant Controllers namespace
    //  $controllerClass = "\\Youdemy\\Controllers\\$controller";

    //  // Debugging: Show the class being constructed
    //  echo "Resolving controller: $controllerClass\n"; // Add debug output

    //  // Ensure the controller class exists
    //  if (!class_exists($controllerClass)) {
    //  throw new \RuntimeException("Controller class $controllerClass not found");
    //  }

    //  $controllerInstance = new $controllerClass();

    //  // Ensure the method exists in the controller
    //  if (!method_exists($controllerInstance, $method)) {
    //  throw new \RuntimeException("Method $method not found in controller $controllerClass");
    //  }

    //  call_user_func([$controllerInstance, $method]);
    //  }
    //  }

     private function handleNotFound()
     {
     if ($this->notFoundHandler) {
     call_user_func($this->notFoundHandler);
     } else {
     http_response_code(404);
     require_once __DIR__ . '/Views/404_view.php';
     }
     }
     }