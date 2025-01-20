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
use Youdemy\Services\CourseService;
use Youdemy\Services\AuthService;
use Youdemy\Services\EnrollmentService;
use Youdemy\Services\AdminService;
use Youdemy\Repository\UserRepository;
use Youdemy\Repository\EnrollmentRepository;
use Youdemy\Repository\CourseRepository;
use Youdemy\Repository\TagRepository;
use Youdemy\Repository\ReviewRepository;

$router = new Router();
$database = new Database();
$userRepository = new UserRepository($database);
$courseRepository = new CourseRepository($database);
$tagRepository = new TagRepository($database);
$enrollmentRepository = new EnrollmentRepository($database);
$courseService = new CourseService($courseRepository, $tagRepository, $database);
$enrollmentService = new EnrollmentService($database, $courseRepository, $userRepository, $enrollmentRepository);
$authService = new AuthService($userRepository);
$adminService = new AdminService($userRepository, $courseRepository, $enrollmentRepository, $reviewRepository, $authService, $database);

$router->get('/', function() {
    require 'main.php'; 
});

$router->get('/login', function() {
    require 'src/Views/auth/login.php';
});

$router->get('/register', function() {
    require 'src/Views/auth/register.php'; 
});

// Course routes
$router->get('/courses', function() {
    require 'src/Views/courses/index.php'; 
});

$router->get('/courses/{id}', function($id) {
    require 'src/Views/courses/show.php'; 
});

// Auth routes
$router->post('/register', function() {
    (new AuthController())->register(); 
});

$router->post('/login', function() {
    (new AuthController())->login();
});

$router->get('/logout', function() {
    (new AuthController())->logout();
});

// Student routes
$router->get('/student/dashboard', function() use ($courseService, $authService, $enrollmentService) {
    (new StudentController($courseService, $authService, $enrollmentService))->dashboard();
});

$router->post('/student/enroll/:id', function($id) use ($courseService, $authService, $enrollmentService) {
    (new StudentController($courseService, $authService, $enrollmentService))->enrollCourse($id);
});

$router->get('/student/courses/:id', function($id) use ($courseService, $authService, $enrollmentService) {
    (new StudentController($courseService, $authService, $enrollmentService))->viewCourse($id);
});

// Teacher routes
$router->get('/teacher/dashboard', function() use ($courseService, $authService) {
    (new TeacherController($courseService, $authService))->dashboard();
});

$router->get('/teacher/courses/create', function() {
    require 'src/Views/teacher/create.php'; 
});

$router->post('/teacher/courses/create', function() use ($courseService, $authService) {
    (new TeacherController($courseService, $authService))->createCourse();
});

$router->get('/teacher/courses/:id/edit', function($id) {
    require 'src/Views/teacher/edit.php'; 
});

$router->post('/teacher/courses/:id/edit', function($id) use ($courseService, $authService) {
    (new TeacherController($courseService, $authService))->editCourse($id);
});

// Admin routes
$router->get('/admin/dashboard', function() use ($adminService, $authService) {
    (new AdminController($adminService, $authService))->dashboard();
});

$router->get('/admin/users', function() {
    require 'src/Views/admin/users.php'; 
});

$router->get('/admin/courses', function() {
    require 'src/Views/admin/courses.php'; 
});

// Set 404 handler
$router->setNotFoundHandler(function() {
    http_response_code(404);
    require_once __DIR__ . '/src/Views/404_view.php';
});

// Dispatch the request
$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);