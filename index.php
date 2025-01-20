<?php
require_once "vendor/autoload.php";
require_once "src/Router.php";

require_once __DIR__ . '/config/config.php';

use Youdemy\Router;
use Youdemy\Controllers\HomeController;
use Youdemy\Controllers\CourseController;
use Youdemy\Controllers\AuthController;
use Youdemy\Controllers\StudentController;
use Youdemy\Controllers\TeacherController;
use Youdemy\Controllers\AdminController;
use Youdemy\Config\Database;
$router = new Router();
$router->get('/', function() {
    require 'main.php'; 
});
$router->get('/login', function() {
    require 'src/Views/auth/login.php';
});
$router->get('/register', function() {
    require 'src/Views/auth/register.php'; 
});

// Add routes
// $router->get('/', 'HomeController@index');
$router->get('/courses', 'CourseController@index');
$router->get('/courses/{id}', 'CourseController@show');
$router->post('/register', 'AuthController@register'); 
$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout');

// Student routes
$router->get('/student/dashboard', 'StudentController@dashboard');
$router->post('/student/enroll/:id', 'StudentController@enrollCourse');
$router->get('/student/courses/:id', 'StudentController@viewCourse');

// Teacher routes
$router->get('/teacher/dashboard', 'TeacherController@dashboard');
$router->get('/teacher/courses/create', 'TeacherController@createCourse');
$router->post('/teacher/courses/create', 'TeacherController@createCourse');
$router->get('/teacher/courses/:id/edit', 'TeacherController@editCourse');
$router->post('/teacher/courses/:id/edit', 'TeacherController@editCourse');

// Admin routes
$router->get('/admin/dashboard', 'AdminController@dashboard');
$router->get('/admin/users', 'AdminController@manageUsers');
$router->get('/admin/courses', 'AdminController@manageCourses');

// Set 404 handler
$router->setNotFoundHandler(function() {
    http_response_code(404);
    require_once __DIR__ . '/src/Views/404_view.php';
});

// Dispatch the request
$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);