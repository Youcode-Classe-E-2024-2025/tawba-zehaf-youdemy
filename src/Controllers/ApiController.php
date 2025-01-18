<?php

namespace Youdemy\Controllers;

use Youdemy\Services\CourseService;
use Youdemy\Services\AuthService;
use Youdemy\Services\EnrollmentService;

class ApiController
{
    private CourseService $courseService;
    private AuthService $authService;
    private EnrollmentService $enrollmentService;

    public function __construct(CourseService $courseService, AuthService $authService, EnrollmentService $enrollmentService)
    {
        $this->courseService = $courseService;
        $this->authService = $authService;
        $this->enrollmentService = $enrollmentService;
    }

    public function getCourses()
    {
        $courses = $this->courseService->getAllCourses();
        $this->jsonResponse($courses);
    }

    public function getCourse(int $id)
    {
        $course = $this->courseService->getCourse($id);
        if (!$course) {
            $this->jsonResponse(['error' => 'Course not found'], 404);
        }
        $this->jsonResponse($course);
    }

    public function enrollCourse(int $courseId)
    {
        $user = $this->authService->getCurrentUser();
        if (!$user) {
            $this->jsonResponse(['error' => 'Unauthorized'], 401);
        }

        try {
            $this->enrollmentService->enrollStudent($user->getId(), $courseId);
            $this->jsonResponse(['message' => 'Enrolled successfully']);
        } catch (\Exception $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    private function jsonResponse($data, int $statusCode = 200)
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }
}

