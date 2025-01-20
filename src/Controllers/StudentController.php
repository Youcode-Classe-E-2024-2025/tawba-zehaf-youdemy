<?php
namespace Youdemy\Controllers;

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
        $user = $this->authService->getCurrentUser();
        if (!$this->isStudent($user)) {
            $this->forbidden();
        }

        try {
            $enrolledCourses = $this->enrollmentService->getEnrolledCourses($user->getId());
            $progress = $this->enrollmentService->getStudentProgress($user->getId());
            $recommendations = $this->courseService->getRecommendedCourses($user->getId());

            $this->render('student/dashboard.php', [
                'enrolledCourses' => $enrolledCourses,
                'progress' => $progress,
                'recommendations' => $recommendations,
                'user' => $user
            ]);
        } catch (Exception $e) {
            $this->render('error.php', ['error' => $e->getMessage()]);
        }
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
        $user = $this->authService->getCurrentUser();
        if (!$this->isStudent($user)) {
            $this->forbidden();
        }

        try {
            $course = $this->courseService->getCourse($courseId);
            if (!$course || !$course->isPublished()) {
                $this->notFound();
            }

            if ($this->enrollmentService->isStudentEnrolled($user->getId(), $courseId)) {
                $this->redirect('/student/courses/' . $courseId);
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->enrollmentService->enrollStudent($user->getId(), $courseId);
                $this->redirect('/student/courses/' . $courseId);
            } else {
                $this->render('student/enroll.php', [
                    'course' => $course,
                    'user' => $user
                ]);
            }
        } catch (Exception $e) {
            $this->render('error.php', ['error' => $e->getMessage()]);
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