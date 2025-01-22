<?php
require_once "vendor/autoload.php";
require_once "src/Router.php";
require_once __DIR__ . '/config/config.php';
require_once 'Config/Database.php';

// Get the database connection
$db = Youdemy\Config\Database::getInstance()->getConnection();

if (!$db) {
    die("Database connection failed.");
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


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
$database = new Youdemy\Config\Database();
$userRepository = new Youdemy\Repository\UserRepository($database);
$courseRepository = new Youdemy\Repository\CourseRepository($database);
$enrollmentRepository = new Youdemy\Repository\EnrollmentRepository($database);
$reviewRepository = new Youdemy\Repository\ReviewRepository($database);
$authService = new Youdemy\Services\AuthService($userRepository);
$adminService = new Youdemy\Services\AdminService($userRepository, $courseRepository, $enrollmentRepository, $reviewRepository, $authService, $database);
$adminService = new Youdemy\Services\AdminService($userRepository, $courseRepository, $enrollmentRepository, $reviewRepository, $authService, $database);
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
$router->get('/register', function() {
    (new AuthController())->register();
});
$router->get('/login', function() {
    require 'src/Views/auth/login.php';
});

$router->get('/register', function() {
    require 'src/Views/auth/register.php'; 
});

// Course routes
$router->get('/courses', function() {
    require 'src/Views/courses/courses.php'; 
});

$router->get('/courses/{id}', function($id) {
    require 'src/Views/courses/coursescatalog.php'; 
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
    // if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    //     header('Location: /login');
    //     exit();
    // }
    (new TeacherController($courseService, $authService))->dashboard();
});

$router->get('/teacher/create_btn', function() {
    require 'src/Views/teacher/create.php'; 
});



$router->get('/teacher/create', function() use ($courseService, $authService) {
    (new TeacherController($courseService, $authService))->createCourse();
});

$router->post('/teacher/create', function() use ($courseService, $authService) {
    (new TeacherController($courseService, $authService))->createCourse();
});





// $router->get('/teacher/create', [TeacherController::class, 'create']);


$router->get('/teacher/courses/:id/edit', function($id) {
    require 'src/Views/teacher/edit.php'; 
});

$router->post('/teacher/courses/:id/edit', function($id) use ($courseService, $authService) {
    (new TeacherController($courseService, $authService))->editCourse($id);
});
// Student enrollment route
$router->get('/student/enroll/:id', function($id) use ($courseService, $authService, $enrollmentService) {
    (new StudentController($courseService, $authService, $enrollmentService))->enrollCourse($id);
});

// Admin routes
// Admin routes
$router->get('/admin/dashboard', function() use ($adminService, $authService) {
    
    (new AdminController($adminService, $authService))->dashboard();
});
// Admin routes with proper patterns
$router->get('/admin/edit_user/:id', function($id) use ($adminService, $authService) {
    (new AdminController($adminService, $authService))->editUser($id);
});

$router->get('/admin/delete/:id', function($id) use ($adminService, $authService) {
    (new AdminController($adminService, $authService))->deleteUser($id);
});

$router->get('/admin/edit/:id', function($id) use ($adminService, $authService) {
    (new AdminController($adminService, $authService))->editCourse($id);
});

$router->get('/admin/courses/delete/:id', function($id) use ($adminService, $authService) {
    (new AdminController($adminService, $authService))->deleteCourse($id);
});
// $router->get('/', function() use ($courseService) {
//     $db = Database::getInstance()->getConnection();
//     $sql = "SELECT 
//             c.*, 
//             u.username as teacher_name,
//             cm.image_path as course_image,
//             cm.video_path as video_content,
//             cm.pdf_path as pdf_content
//             FROM courses c 
//             JOIN users u ON c.teacher_id = u.id 
//             LEFT JOIN course_media cm ON c.id = cm.course_id
//             ORDER BY c.created_at DESC";
//     $courses = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    
//     require 'main.php';
// });


// Admin User Management Routes
// $router->get('/admin/user/delete/{id}', function($id) use ($adminService, $authService) {
//     (new AdminController($adminService, $authService))->deleteUser($id);
// });

// $router->get('/admin/user/edit/{id}', function($id) use ($adminService, $authService) {
//     (new AdminController($adminService, $authService))->editUser($id);
// });
$router->get('/', function() use ($courseService) {
    $db = Database::getInstance()->getConnection();
    $sql = "SELECT c.*, u.username as teacher_name
            FROM courses c 
            JOIN users u ON c.teacher_id = u.id 
            ORDER BY c.created_at DESC";
    $courses = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    
    require 'main.php';
});
$router->get('/', function() use ($courseService) {
    $db = Database::getInstance()->getConnection();
    
    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $_GET['search'];
        $sql = "SELECT c.*, u.username as teacher_name
                FROM courses c 
                JOIN users u ON c.teacher_id = u.id 
                WHERE c.title LIKE ? 
                OR c.description LIKE ?
                ORDER BY c.created_at DESC";
                
        $stmt = $db->prepare($sql);
        $searchTerm = "%$search%";
        $stmt->execute([$searchTerm, $searchTerm]);
    } else {
        $sql = "SELECT c.*, u.username as teacher_name
                FROM courses c 
                JOIN users u ON c.teacher_id = u.id 
                ORDER BY c.created_at DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute();
    }
    
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    require 'main.php';
});


// $router->post('/admin/user/edit/{id}', function($id) use ($adminService, $authService) {
//     (new AdminController($adminService, $authService))->editUser($id);
// });

// // Admin Course Management Routes
// $router->get('/admin/course/delete/{id}', function($id) use ($adminService, $authService) {
//     (new AdminController($adminService, $authService))->deleteCourse($id);
// });

// $router->get('/admin/course/edit/{id}', function($id) use ($adminService, $authService) {
//     (new AdminController($adminService, $authService))->editCourse($id);
// });

// $router->post('/admin/course/edit/{id}', function($id) use ($adminService, $authService) {
//     (new AdminController($adminService, $authService))->editCourse($id);
// });


// $router->get('/admin/users', function() {
//     require 'src/Views/admin/users.php'; 
// });
// $router->get('/admin/course/delete/{id}', function($id) use ($adminService, $authService) {
//     (new AdminController($adminService, $authService))->deleteCourse($id);
// });

// $router->get('/admin/course/edit/{id}', function($id) use ($adminService, $authService) {
//     (new AdminController($adminService, $authService))->editCourse($id);
// });

// $router->post('/admin/course/edit/{id}', function($id) use ($adminService, $authService) {
//     (new AdminController($adminService, $authService))->editCourse($id);
// });
// Admin User Edit Route
// Admin routes with proper dependencies
$router->get('/admin/user/edit/([0-9]+)', function($id) use ($adminService, $authService) {
    (new AdminController($adminService, $authService))->editUser($id);
});

$router->post('/admin/user/edit/([0-9]+)', function($id) use ($adminService, $authService) {
    (new AdminController($adminService, $authService))->editUser($id);
});


$router->get('/admin/courses', function() {
    require 'src/Views/admin/courses.php'; 
});
$router->get('/courses', function() {
    require 'src/Views/courses/courses.php'; 
});

$router->get('/courses/{id}', function($id) {
    require 'src/Views/courses/course_details.php'; 
});

$router->post('/courses/enroll/{id}', function($id) {
    // Logic to enroll the student in the course
});

$router->get('/my-courses', function() {
    require 'src/Views/courses/my_courses.php'; 
});
// Set 404 handler
$router->setNotFoundHandler(function() {
    http_response_code(404);
    require_once __DIR__ . '/src/Views/404_view.php';
});

// Dispatch the request
$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);