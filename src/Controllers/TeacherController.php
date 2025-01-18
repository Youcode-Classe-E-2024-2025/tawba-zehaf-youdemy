<?php
namespace Youdemy\Controllers;

use Youdemy\Services\CourseService;
use Youdemy\Services\AuthService;
use Youdemy\Models\Entity\User;
use Exception;

class TeacherController {
    private CourseService $courseService;
    private AuthService $authService;

    public function __construct(CourseService $courseService, AuthService $authService) {
        $this->courseService = $courseService;
        $this->authService = $authService;
    }

    public function dashboard()
    {
        $user = $this->authService->getCurrentUser();
        if (!$this->isTeacher($user)) {
            $this->forbidden();
        }

        try {
            $courses = $this->courseService->getCoursesByTeacher($user->getId());
            $stats = $this->courseService->getTeacherStats($user->getId());
            
            $this->render('teacher/dashboard.php', [
                'courses' => $courses,
                'stats' => $stats,
                'user' => $user
            ]);
        } catch (Exception $e) {
            $this->render('error.php', ['error' => $e->getMessage()]);
        }
    }

    public function createCourse()
    {
        $user = $this->authService->getCurrentUser();
        if (!$this->isTeacher($user)) {
            $this->forbidden();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $courseData = $this->validateCourseData($_POST);
                $course = $this->courseService->createCourse(
                    $courseData['title'],
                    $courseData['description'],
                    $courseData['content'],
                    $user,
                    $courseData['category_id'],
                    $courseData['tags'],
                );
                $this->redirect('/teacher/courses/' . $course->getId());
            } catch (Exception $e) {
                $this->render('teacher/create_course.php', [
                    'error' => $e->getMessage(),
                    'data' => $_POST,
                    'categories' => $this->courseService->getAllCategories()
                ]);
            }
        } else {
            $this->render('teacher/create_course.php', [
                'categories' => $this->courseService->getAllCategories()
            ]);
        }
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
                    $courseData['tags'],
                    $courseData['price']
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