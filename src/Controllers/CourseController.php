<?php

namespace Youdemy\Controllers;
use Youdemy\Models\Entity\Course;
use Youdemy\Repository\CourseRepository;
<?php

namespace Youdemy\Controllers;

use Youdemy\Services\CourseService;

class CourseController {
    private $courseService;

    public function __construct(CourseService $courseService) {
        $this->courseService = $courseService;
    }

    public function index($page = 1) {
        // Logic to fetch courses with pagination
        $courses = $this->courseService->getCourses($page);
        require_once __DIR__ . '/../Views/courses.php';
    }

    public function search($keyword) {
        // Logic to search courses by keyword
        $courses = $this->courseService->searchCourses($keyword);
        require_once __DIR__ . '/../Views/courses.php';
    }
    private function notFound()
    {
        http_response_code(404);
        $this->render('404.php', ['title' => 'Page Not Found']);
        exit;
    }
}

   