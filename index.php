<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/config.php';

use Youdemy\Router;
use Youdemy\Controllers\HomeController;
use Youdemy\Controllers\CourseController;
use Youdemy\Controllers\AuthController;
use Youdemy\Controllers\StudentController;
use Youdemy\Controllers\TeacherController;
use Youdemy\Controllers\AdminController;

$router = new Router();

// Add routes
$router->get('/', 'Youdemy\Controllers\HomeController@index');
$router->get('/courses', 'Youdemy\Controllers\CourseController@index');
$router->get('/courses/:id', 'Youdemy\Controllers\CourseController@show');
$router->get('/register', 'Youdemy\Controllers\AuthController@register');
$router->post('/register', 'Youdemy\Controllers\AuthController@register');
$router->get('/login', 'Youdemy\Controllers\AuthController@login');
$router->post('/login', 'Youdemy\Controllers\AuthController@login');
$router->get('/logout', 'Youdemy\Controllers\AuthController@logout');

// Student routes
$router->get('/student/dashboard', 'Youdemy\Controllers\StudentController@dashboard');
$router->post('/student/enroll/:id', 'Youdemy\Controllers\StudentController@enrollCourse');
$router->get('/student/courses/:id', 'Youdemy\Controllers\StudentController@viewCourse');

// Teacher routes
$router->get('/teacher/dashboard', 'Youdemy\Controllers\TeacherController@dashboard');
$router->get('/teacher/courses/create', 'Youdemy\Controllers\TeacherController@createCourse');
$router->post('/teacher/courses/create', 'Youdemy\Controllers\TeacherController@createCourse');
$router->get('/teacher/courses/:id/edit', 'Youdemy\Controllers\TeacherController@editCourse');
$router->post('/teacher/courses/:id/edit', 'Youdemy\Controllers\TeacherController@editCourse');

// Admin routes
$router->get('/admin/dashboard', 'Youdemy\Controllers\AdminController@dashboard');
$router->get('/admin/users', 'Youdemy\Controllers\AdminController@manageUsers');
$router->get('/admin/courses', 'Youdemy\Controllers\AdminController@manageCourses');

// Set 404 handler
$router->setNotFoundHandler(function() {
    http_response_code(404);
    require_once __DIR__ . '/src/Views/404_view.php';
});

// Dispatch the request
$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);