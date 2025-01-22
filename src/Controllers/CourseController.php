<?php

namespace Youdemy\Controllers;
use Youdemy\Models\Entity\Course;
use Youdemy\Repository\CourseRepository;
use Youdemy\Services\CourseService;
class CourseController {
    private $courseService;

    public function __construct(CourseService $courseService) {
        $this->courseService = $courseService;
    }

 
        // Logic to fetch courses with pagination
        public function index($page = 1) {
            $coursesPerPage = 10; // Set how many courses to display per page
            $totalCourses = $this->courseService->getTotalCourses(); // Method to get total courses
            $totalPages = ceil($totalCourses / $coursesPerPage); // Calculate total pages
            $courses = $this->courseService->getCourses($page, $coursesPerPage); // Fetch courses for the current page
            require_once __DIR__ . '/../Views/courses.php';
        }

    public function search($keyword) {
        // Logic to search courses by keyword
        $courses = $this->courseService->searchCourses($keyword);
        require_once __DIR__ . '/../Views/courses.php';
    }

    public function show($id) {
        $course = $this->courseService->getCourseById($id);
        if (!$course) {
            $this->notFound();
        }
        require_once __DIR__ . '/../Views/course_details.php'; // Create this view for course details
    }
    
    public function enroll($courseId) {
        // Check if the user is authenticated
        if (!$this->isAuthenticated()) {
            // Redirect to login or show an error
            header('Location: /login'); // Redirect to login page
            exit;
        }
        
        // Enroll the user in the course
        $this->courseService->enrollInCourse($courseId, $_SESSION['user_id']); // Method to enroll the user
        
        // Redirect to "My Courses" or show a success message
        header('Location: /my-courses'); // Redirect to the user's courses page
        exit;
    }
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate and sanitize input data
            $title = $_POST['title'];
            $description = $_POST['description'];
            $tags = $_POST['tags'];
            $category = $_POST['category'];
    
            // Handle file upload
            if (isset($_FILES['content']) && $_FILES['content']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['content']['tmp_name'];
                $fileName = $_FILES['content']['name'];
                $fileSize = $_FILES['content']['size'];
                $fileType = $_FILES['content']['type'];
    
                // Specify the upload directory
                $uploadFileDir = './uploads/';
                $destPath = $uploadFileDir . $fileName;
    
                // Move the file to the upload directory
                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    // Save course details along with the file path
                    $this->courseService->addCourse($title, $description, $destPath, $tags, $category);
                    header('Location: /courses'); // Redirect to the course list
                    exit;
                } else {
                    // Handle file upload error
                    echo 'There was an error moving the uploaded file.';
                }
            }
        }
        require_once __DIR__ . '/../Views/courses/course_create.php'; // Show the form again if not POST
    }
    private function notFound()
    {
        http_response_code(404);
        $this->render('404.php', ['title' => 'Page Not Found']);
        exit;
    }


    private function isAuthenticated()
    {
        // Logic to check if the user is authenticated
        return isset($_SESSION['user_id']);
    }

    private function render($view, $data = [])
    {
        extract($data);
        require_once __DIR__ . '/../Views/' . $view;
    }
}

   