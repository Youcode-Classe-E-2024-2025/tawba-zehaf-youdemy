<?php
require_once '../Services/CourseService.php';
require_once '../Services/AuthService.php';
class StudentController {
    private $courseService;
    private $authService;
    private $enrollmentService;

    public function __construct() {
        $this->courseService = new CourseService();
        $this->authService = new AuthService();
        $this->enrollmentService = new EnrollmentService();
    }

    public function dashboard()
    {
        $student = $this->authService->getCurrentUser();
        if (!$student || $student['role'] !== 'student') {
            $this->forbidden();
        }

        $enrolledCourses = $this->enrollmentService->getEnrolledCourses($student['id']);
        $this->render('student/dashboard.php', ['enrolledCourses' => $enrolledCourses]);
    }

    public function enrollCourse($courseId)
    {
        $student = $this->authService->getCurrentUser();
        if (!$student || $student['role'] !== 'student') {
            $this->forbidden();
        }

        $course = $this->courseService->getCourse($courseId);
        if (!$course) {
            $this->notFound();
        }

        try {
            $this->enrollmentService->enrollStudent($student['id'], $courseId);
            $this->redirect('/student/courses/' . $courseId);
        } catch (Exception $e) {
            $this->render('courses/enroll.php', ['course' => $course, 'error' => $e->getMessage()]);
        }
    }

    public function viewCourse($courseId)
    {
        $student = $this->authService->getCurrentUser();
        if (!$student || $student['role'] !== 'student') {
            $this->forbidden();
        }

        $course = $this->courseService->getCourse($courseId);
        if (!$course || !$this->enrollmentService->isStudentEnrolled($student['id'], $courseId)) {
            $this->forbidden();
        }

        $this->render('student/view_course.php', ['course' => $course]);
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