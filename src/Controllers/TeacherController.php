<?php

class TeacherController {
    private $courseService;
    private $authService;

    public function __construct() {
        $this->courseService = new CourseService();
        $this->authService = new AuthService();
    }

    public function dashboard()
    {
        $teacher = $this->authService->getCurrentUser();
        if (!$teacher || $teacher['role'] !== 'teacher') {
            $this->forbidden();
        }

        $courses = $this->courseService->getCoursesByTeacher($teacher['id']);
        $this->render('teacher/dashboard.php', ['courses' => $courses]);
    }

    public function createCourse()
    {
        $teacher = $this->authService->getCurrentUser();
        if (!$teacher || $teacher['role'] !== 'teacher') {
            $this->forbidden();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            $content = $_POST['content'] ?? '';
            $categoryId = (int)($_POST['category_id'] ?? 0);
            $price = (float)($_POST['price'] ?? 0);
            $tags = $_POST['tags'] ?? [];

            try {
                $course = $this->courseService->createCourse($title, $description, $content, $teacher, $categoryId, $tags, $price);
                $this->redirect('/teacher/courses/' . $course['id']);
            } catch (Exception $e) {
                $this->render('teacher/create_course.php', ['error' => $e->getMessage()]);
            }
        } else {
            $this->render('teacher/create_course.php');
        }
    }

    public function editCourse($courseId)
    {
        $teacher = $this->authService->getCurrentUser();
        if (!$teacher || $teacher['role'] !== 'teacher') {
            $this->forbidden();
        }

        $course = $this->courseService->getCourse($courseId);
        if (!$course || $course['teacher_id'] !== $teacher['id']) {
            $this->forbidden();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            $content = $_POST['content'] ?? '';
            $categoryId = (int)($_POST['category_id'] ?? 0);
            $price = (float)($_POST['price'] ?? 0);
            $tags = $_POST['tags'] ?? [];

            try {
                $this->courseService->updateCourse($courseId, $title, $description, $content, $categoryId, $tags, $price);
                $this->redirect('/teacher/courses/' . $courseId);
            } catch (Exception $e) {
                $this->render('teacher/edit_course.php', ['course' => $course, 'error' => $e->getMessage()]);
            }
        } else {
            $this->render('teacher/edit_course.php', ['course' => $course]);
        }
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