<?php

class CourseController {
    private $courseModel;

    public function __construct() {
        $this->courseModel = new Course();
    }

    public function index()
    {
        $courses = $this->courseModel->getAllCourses();
        $this->render('courses/index.php', ['title' => 'All Courses', 'courses' => $courses]);
    }

    public function show($id)
    {
        $course = $this->courseModel->getCourseById($id);
        
        if (!$course) {
            $this->notFound();
        }
        
        $this->render('courses/show.php', ['title' => $course['title'], 'course' => $course]);
    }

    private function render($view, $data = [])
    {
        extract($data);
        require VIEW_PATH . $view;
    }

    private function notFound()
    {
        http_response_code(404);
        $this->render('404.php', ['title' => 'Page Not Found']);
        exit;
    }
}

