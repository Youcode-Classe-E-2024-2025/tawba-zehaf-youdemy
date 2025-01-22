<?php
namespace Youdemy\Controllers;

use Youdemy\Config\Database;
use Youdemy\Services\CourseService;
use Youdemy\Services\AuthService;
use Youdemy\Services\EnrollmentService;
use Youdemy\Models\Entity\User;
use Exception;

class StudentController {
    private CourseService $courseService;
    private AuthService $authService;
    private EnrollmentService $enrollmentService;
    public function __construct(
        CourseService $courseService,
        AuthService $authService,
        EnrollmentService $enrollmentService
    ) {
        $this->courseService = $courseService;
        $this->authService = $authService;
        $this->enrollmentService = $enrollmentService;
    }
    public function dashboard()
{
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
        header('Location: /login');
        exit();
    }

    $studentId = $_SESSION['user_id'];
    $enrolledCourses = $this->courseService->getEnrolledCourses($studentId);
    $availableCourses = $this->courseService->getAvailableCourses($studentId);

    $this->render('student/dashboard.php', [
        'enrolledCourses' => $enrolledCourses,
        'availableCourses' => $availableCourses
    ]);
}

    public function browseCourses()
    {
        $user = $this->authService->getCurrentUser();
        if (!$this->isStudent($user)) {
            $this->forbidden();
        }

        try {
            $filters = $this->getFiltersFromRequest();
            $page = max(1, (int)($_GET['page'] ?? 1));
            $limit = 12;

            $courses = $this->courseService->getPublishedCourses();
            $categories = $this->courseService->getAllCategories();

            $this->render('student/browse_courses.php', [
                'courses' => $courses['courses'],
                'pagination' => $courses['pagination'],
                'categories' => $categories,
                'filters' => $filters,
                'user' => $user
            ]);
        } catch (Exception $e) {
            $this->render('error.php', ['error' => $e->getMessage()]);
        }
    }

    public function enrollCourse($courseId)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }
    
        $studentId = $_SESSION['user_id'];
        
        try {
            $db = Database::getInstance()->getConnection();
            
            // Check if already enrolled
            $checkSql = "SELECT id FROM enrollments WHERE student_id = ? AND course_id = ?";
            $checkStmt = $db->prepare($checkSql);
            $checkStmt->execute([$studentId, $courseId]);
            
            if ($checkStmt->rowCount() === 0) {
                // Create new enrollment
                $sql = "INSERT INTO enrollments (student_id, course_id) VALUES (?, ?)";
                $stmt = $db->prepare($sql);
                $stmt->execute([$studentId, $courseId]);
                
                $_SESSION['success'] = 'Successfully enrolled in course!';
            }
            
            header('Location: /student/dashboard');
            exit();
            
        } catch (\PDOException $e) {
            $_SESSION['error'] = 'Failed to enroll in course';
            header('Location: /student/dashboard');
            exit();
        }
    }
    
    public function viewCourse($courseId)
    {
        $user = $this->authService->getCurrentUser();
        if (!$this->isStudent($user)) {
            $this->forbidden();
        }

        try {
            if (!$this->enrollmentService->isStudentEnrolled($user->getId(), $courseId)) {
                $this->forbidden();
            }

            $course = $this->courseService->getCourse($courseId);
            if (!$course) {
                $this->notFound();
            }

            $progress = $this->enrollmentService->getCourseProgress($user->getId(), $courseId);
            $this->enrollmentService->updateLastAccessed($user->getId(), $courseId);

            $this->render('student/view_course.php', [
                'course' => $course,
                'progress' => $progress,
                'user' => $user
            ]);
        } catch (Exception $e) {
            $this->render('error.php', ['error' => $e->getMessage()]);
        }
    }

    private function isStudent(?User $user): bool
    {
        return $user && $user->getRole() === 'student' && $user->isActive();
    }

    private function getFiltersFromRequest(): array
    {
        return [
            'category_id' => isset($_GET['category']) ? (int)$_GET['category'] : null,
            'min_rating' => isset($_GET['rating']) ? (float)$_GET['rating'] : null,
            'price_range' => isset($_GET['price']) ? $_GET['price'] : null,
            'search' => isset($_GET['q']) ? trim($_GET['q']) : null
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

    private function notFound()
    {
        http_response_code(404);
        $this->render('404.php', ['title' => 'Page Not Found']);
        exit;
    }
}