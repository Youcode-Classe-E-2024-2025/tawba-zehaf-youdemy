<?php

class Course {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getFeaturedCourses()
    {
        $sql = "SELECT c.*, u.username as instructor, 
                       (SELECT AVG(rating) FROM reviews WHERE course_id = c.id) as rating,
                       (SELECT COUNT(*) FROM reviews WHERE course_id = c.id) as reviews_count
                FROM courses c
                JOIN users u ON c.teacher_id = u.id
                WHERE c.is_featured = 1
                LIMIT 4";
        return $this->db->query($sql)->fetchAll();
    }

    public function getAllCourses()
    {
        $sql = "SELECT c.*, u.username as instructor, 
                       (SELECT AVG(rating) FROM reviews WHERE course_id = c.id) as rating,
                       (SELECT COUNT(*) FROM reviews WHERE course_id = c.id) as reviews_count
                FROM courses c
                JOIN users u ON c.teacher_id = u.id
                ORDER BY c.created_at DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function getCourseById($id)
    {
        $sql = "SELECT c.*, u.username as instructor, 
                       (SELECT AVG(rating) FROM reviews WHERE course_id = c.id) as rating,
                       (SELECT COUNT(*) FROM reviews WHERE course_id = c.id) as reviews_count
                FROM courses c
                JOIN users u ON c.teacher_id = u.id
                WHERE c.id = :id";
        return $this->db->query($sql, ['id' => $id])->fetch();
    }
}