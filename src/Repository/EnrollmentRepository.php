<?php

class EnrollmentRepository {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function create($enrollment) {
        $query = "INSERT INTO enrollments (student_id, course_id, enrolled_at) 
                  VALUES (:student_id, :course_id, :enrolled_at)";
        return $this->db->query($query, $enrollment)->lastInsertId();
    }

    public function findByStudentAndCourse($studentId, $courseId) {
        $query = "SELECT * FROM enrollments WHERE student_id = :student_id AND course_id = :course_id";
        return $this->db->query($query, ['student_id' => $studentId, 'course_id' => $courseId])->fetch();
    }

    public function findCoursesByStudent($studentId) {
        $query = "SELECT c.*, e.enrolled_at 
                  FROM courses c 
                  JOIN enrollments e ON c.id = e.course_id 
                  WHERE e.student_id = :student_id";
        return $this->db->query($query, ['student_id' => $studentId])->fetchAll();
    }

    public function findStudentsByCourse($courseId) {
        $query = "SELECT u.*, e.enrolled_at 
                  FROM users u 
                  JOIN enrollments e ON u.id = e.student_id 
                  WHERE e.course_id = :course_id";
        return $this->db->query($query, ['course_id' => $courseId])->fetchAll();
    }

    public function delete($studentId, $courseId) {
        $query = "DELETE FROM enrollments WHERE student_id = :student_id AND course_id = :course_id";
        return $this->db->query($query, ['student_id' => $studentId, 'course_id' => $courseId])->rowCount();
    }
}