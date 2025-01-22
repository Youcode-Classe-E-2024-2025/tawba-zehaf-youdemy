<?php
namespace Youdemy\Controllers;

use Youdemy\Services\CourseService;
use Youdemy\Services\AuthService;
use Youdemy\Models\Entity\User;
use Youdemy\Config\Database;
use Exception;
use PDO;

class TeacherController {
    private CourseService $courseService;
    private AuthService $authService;

    public function __construct(CourseService $courseService, AuthService $authService) {
        $this->courseService = $courseService;
        $this->authService = $authService;
    }
    public function dashboard()
{
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'teacher') {
        header('Location: /login');
        exit();
    }

    $teacherId = $_SESSION['user_id'];
    $courses = $this->courseService->getTeacherCourses($teacherId);
    $stats = $this->courseService->getTeacherStats($teacherId);

    $this->render('teacher/dashboard.php', [
        'courses' => $courses,
        'stats' => $stats
    ]);
}
// public function getCategories() {
//         // Get database connection
//         $db = Database::getInstance()->getConnection();
    
//         $stmt = $db->prepare("SELECT id, name FROM categories");
//         $stmt->execute();
//         $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
//         $data = [
//             'categories' => $categories
//         ];
        
//         extract($data);
//         require_once __DIR__ . '/../Views/teacher/create.php';
        
// }

private function renderView($view, $data = []) {
    foreach ($data as $key => $value) {
        ${$key} = $value;
    }
    require_once __DIR__ . '/../Views/' . $view;
}

public function createCourse()
{
    // Get database connection
    $db = Database::getInstance()->getConnection();
    
    // Fetch categories
    $stmt = $db->prepare("SELECT id, name FROM categories");
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Handle POST request
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $category_id = $_POST['category'];
        $teacher_id = $_SESSION['user_id'];
        
        try {
            $stmt = $db->prepare("INSERT INTO courses (title, description, category_id, teacher_id) VALUES (?, ?, ?, ?)");
            $stmt->execute([$title, $description, $category_id, $teacher_id]);
            
            header('Location: /teacher/dashboard');
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = 'Failed to create course: ' . $e->getMessage();
        }
    }

    // Render view with categories data
    $this->renderView('teacher/create.php', ['categories' => $categories]);
}






    public function getAllCategories()
{
    $db = Database::getInstance()->getConnection();
    
    // Check if table exists
    $checkTable = $db->query("SHOW TABLES LIKE 'categories'");
    var_dump($checkTable->fetchAll());
    
    // Show table structure
    $structure = $db->query("DESCRIBE categories");
    var_dump($structure->fetchAll());
    
    // Get actual data
    $stmt = $db->query("SELECT * FROM categories");
    var_dump($stmt->fetchAll());
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    
    public function editCourse($courseId)
    {
        $user = $this->authService->getCurrentUser();
        if (!$this->isTeacher($user)) {
            $this->forbidden();
        }

        $course = $this->courseService->getCourse($courseId);
        if (!$course || $course->getTeacherId() !== $user->getId()) {
            $this->forbidden();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $courseData = $this->validateCourseData($_POST);
                $this->courseService->updateCourse(
                    $courseId,
                    $courseData['title'],
                    $courseData['description'],
                    $courseData['content'],
                    $courseData['category_id'],
                    $courseData['tags']
                );
                $this->redirect('/teacher/courses/' . $courseId);
            } catch (Exception $e) {
                $this->render('teacher/edit_course.php', [
                    'course' => $course,
                    'error' => $e->getMessage(),
                    'data' => $_POST,
                    'categories' => $this->courseService->getAllCategories()
                ]);
            }
        } else {
            $this->render('teacher/edit_course.php', [
                'course' => $course,
                'categories' => $this->courseService->getAllCategories()
            ]);
        }
    }

    public function publishCourse($courseId)
    {
        $user = $this->authService->getCurrentUser();
        if (!$this->isTeacher($user)) {
            $this->forbidden();
        }

        $course = $this->courseService->getCourse($courseId);
        if (!$course || $course->getTeacherId() !== $user->getId()) {
            $this->forbidden();
        }

        try {
            $this->courseService->publishCourse($courseId);
            $this->redirect('/teacher/courses/' . $courseId);
        } catch (Exception $e) {
            $this->render('error.php', ['error' => $e->getMessage()]);
        }
    }

    private function isTeacher(?User $user): bool
    {
        return $user && $user->getRole() === 'teacher' && $user->isValidated();
    }

    private function validateCourseData(array $data): array
    {
        $required = ['title', 'description', 'content', 'category_id', 'price'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new Exception("Field '$field' is required");
            }
        }

        return [
            'title' => trim($data['title']),
            'description' => trim($data['description']),
            'content' => trim($data['content']),
            'category_id' => (int)$data['category_id'],
            'price' => (float)$data['price'],
            'tags' => isset($data['tags']) ? array_map('trim', $data['tags']) : []
        ];
    }

    private function render($view, $data = [])
    {
        extract($data);
        require VIEW_PATH . $view;
    }

    private function redirect($url)
    {
        header("Location: $url");
        exit;
    }

    private function forbidden()
    {
        http_response_code(403);
        $this->render('403.php', ['title' => 'Access Denied']);
        exit;
    }
}