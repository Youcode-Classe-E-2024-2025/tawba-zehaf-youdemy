<?php

class CourseService {
    private $db;
    private $courseRepository;

    public function __construct() {
        $this->db = new Database();
        $this->courseRepository = new CourseRepository($this->db);
    }

    public function getCourse($id) {
        return $this->courseRepository->findById($id);
    }

    public function getCoursesByTeacher($teacherId) {
        return $this->courseRepository->findByTeacherId($teacherId);
    }

    public function createCourse($title, $description, $content, $teacher, $categoryId, $tags, $price) {
        // Validate input
        if (empty($title) || empty($description) || empty($content) || empty($teacher) || $categoryId <= 0 || $price < 0) {
            throw new Exception("Invalid course data");
        }

        $course = [
            'title' => $title,
            'description' => $description,
            'content' => $content,
            'teacher_id' => $teacher['id'],
            'category_id' => $categoryId,
            'price' => $price,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $courseId = $this->courseRepository->create($course);

        if (!empty($tags)) {
            $this->courseRepository->addTags($courseId, $tags);
        }

        return $this->getCourse($courseId);
    }

    public function updateCourse($id, $title, $description, $content, $categoryId, $tags, $price) {
        // Validate input
        if (empty($title) || empty($description) || empty($content) || $categoryId <= 0 || $price < 0) {
            throw new Exception("Invalid course data");
        }

        $course = [
            'id' => $id,
            'title' => $title,
            'description' => $description,
            'content' => $content,
            'category_id' => $categoryId,
            'price' => $price,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->courseRepository->update($course);

        if (!empty($tags)) {
            $this->courseRepository->updateTags($id, $tags);
        }

        return $this->getCourse($id);
    }

    public function getFeaturedCourses($limit = 6) {
        return $this->courseRepository->findFeatured($limit);
    }

    public function searchCourses($query, $page = 1, $perPage = 10) {
        return $this->courseRepository->search($query, $page, $perPage);
    }
}