<?php

namespace Youdemy\Repository;

use Youdemy\Config\Database;
use Youdemy\Models\Entity\Enrollment;
use Youdemy\Models\Entity\Course;
use Youdemy\Models\Entity\User;
use PDOException;
use DateTime;

class EnrollmentRepository
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function create(Enrollment $enrollment): int
    {
        try {
            $query = "INSERT INTO enrollments (student_id, course_id, enrolled_at) 
                      VALUES (:student_id, :course_id, :enrolled_at)";
            
            $params = [
                'student_id' => $enrollment->getStudentId(),
                'course_id' => $enrollment->getCourseId(),
                'enrolled_at' => (new DateTime())->format('Y-m-d H:i:s')
            ];
            
            $this->db->query($query, $params);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            throw new \RuntimeException('Failed to create enrollment: ' . $e->getMessage());
        }
    }

    public function findByStudentAndCourse(int $studentId, int $courseId): ?Enrollment
    {
        $query = "SELECT * FROM enrollments WHERE student_id = :student_id AND course_id = :course_id";
        $result = $this->db->query($query, [
            'student_id' => $studentId, 
            'course_id' => $courseId
        ])->fetch();
        
        return $result ? $this->hydrateEnrollment($result) : null;
    }

    public function findCoursesByStudent(int $studentId): array
    {
        $query = "SELECT c.*, e.enrolled_at 
                  FROM courses c 
                  JOIN enrollments e ON c.id = e.course_id 
                  WHERE e.student_id = :student_id";
        
        $results = $this->db->query($query, ['student_id' => $studentId])->fetchAll();
        return array_map([$this, 'hydrateCourse'], $results);
    }

    public function findStudentsByCourse(int $courseId): array
    {
        $query = "SELECT u.*, e.enrolled_at 
                  FROM users u 
                  JOIN enrollments e ON u.id = e.student_id 
                  WHERE e.course_id = :course_id";
        
        $results = $this->db->query($query, ['course_id' => $courseId])->fetchAll();
        return array_map([$this, 'hydrateUser'], $results);
    }

    public function delete(int $studentId, int $courseId): bool
    {
        try {
            $query = "DELETE FROM enrollments WHERE student_id = :student_id AND course_id = :course_id";
            $result = $this->db->query($query, [
                'student_id' => $studentId, 
                'course_id' => $courseId
            ])->rowCount();
            
            return $result > 0;
        } catch (PDOException $e) {
            throw new \RuntimeException('Failed to delete enrollment: ' . $e->getMessage());
        }
    }

    private function hydrateEnrollment(array $data): Enrollment
    {
        $enrollment = new Enrollment($data['student_id'], $data['course_id']);
        $enrollment->setId($data['id']);
        $enrollment->setStudentId($data['student_id']);
        $enrollment->setCourseId($data['course_id']);
        $enrollment->setEnrolledAt(new DateTime($data['enrolled_at']));
        return $enrollment;
    }

    private function hydrateCourse(array $data): Course
    {
        $course = new Course($this->db);
        $course->setId($data['id']);
        $course->setTitle($data['title']);
        $course->setDescription($data['description']);
        $course->setContent($data['content']);
        $course->setTeacherId($data['teacher_id']);
        $course->setCategoryId($data['category_id']);
        $course->setPrice($data['price']);
        
        if (isset($data['created_at'])) {
            $course->setCreatedAt(new DateTime($data['created_at']));
        }
        if (isset($data['updated_at'])) {
            $course->setUpdatedAt(new DateTime($data['updated_at']));
        }
        
        return $course;
    }

    private function hydrateUser(array $data): User
    {
        $user = new User($data['id'], $data['name'], $data['email']);
        // $user->setId($data['id']); // Removed because setId method does not exist
        // $user->setName($data['name']);
        $user->setEmail($data['email']);
        $user->setRole($data['role']);
        
        // if (isset($data['created_at'])) {
        //     $user->setCreatedAt(new DateTime($data['created_at']));
        // }
        
        return $user;
    }
}