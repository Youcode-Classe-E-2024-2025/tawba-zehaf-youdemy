<?php

namespace Youdemy\Services;

use DateTime;
use PDOException;
use RuntimeException;
use Youdemy\Config\Database;
use Youdemy\Repository\CourseRepository;
use Youdemy\Repository\UserRepository;
use Youdemy\Repository\EnrollmentRepository;

class EnrollmentService {
    private Database $db;
    private CourseRepository $courseRepository;
    private UserRepository $userRepository;
    private EnrollmentRepository $enrollmentRepository;

    public function __construct(
        Database $db,
        CourseRepository $courseRepository,
        UserRepository $userRepository,
        EnrollmentRepository $enrollmentRepository
    ) {
        $this->db = $db;
        $this->courseRepository = $courseRepository;
        $this->userRepository = $userRepository;
        $this->enrollmentRepository = $enrollmentRepository;
    }

    public function getEnrolledCourses(int $userId): array {
        try {
            $query = "SELECT 
                        c.*,
                        e.enrolled_at,
                        e.last_accessed_at,
                        e.progress_percentage,
                        u.name as teacher_name,
                        cat.name as category_name,
                        (SELECT COUNT(*) FROM course_sections WHERE course_id = c.id) as total_sections,
                        (SELECT COUNT(*) FROM section_completions sc 
                         INNER JOIN course_sections cs ON sc.section_id = cs.id 
                         WHERE cs.course_id = c.id AND sc.student_id = :userId) as completed_sections,
                        (SELECT AVG(rating) FROM reviews r WHERE r.course_id = c.id) as average_rating,
                        GROUP_CONCAT(t.name) as tags
                    FROM enrollments e
                    INNER JOIN courses c ON e.course_id = c.id
                    INNER JOIN users u ON c.teacher_id = u.id
                    LEFT JOIN categories cat ON c.category_id = cat.id
                    LEFT JOIN course_tags ct ON c.id = ct.course_id
                    LEFT JOIN tags t ON ct.tag_id = t.id
                    WHERE e.student_id = :userId
                    GROUP BY c.id
                    ORDER BY e.last_accessed_at DESC";

            $results = $this->db->query($query, ['userId' => $userId])->fetchAll();

            return array_map(function($row) {
                $course = $this->courseRepository->hydrateCourse($row);
                if (isset($row['tags'])) {
                    $course->setTags(explode(',', $row['tags']));
                }
                return [
                    'course' => $course,
                    'enrolled_at' => new DateTime($row['enrolled_at']),
                    'last_accessed_at' => $row['last_accessed_at'] ? new DateTime($row['last_accessed_at']) : null,
                    'progress_percentage' => (float) $row['progress_percentage'],
                    'total_sections' => (int) $row['total_sections'],
                    'completed_sections' => (int) $row['completed_sections'],
                    'teacher_name' => $row['teacher_name'],
                    'category_name' => $row['category_name'],
                    'average_rating' => $row['average_rating'] ? round((float) $row['average_rating'], 1) : null
                ];
            }, $results);
        } catch (PDOException $e) {
            throw new RuntimeException('Failed to fetch enrolled courses: ' . $e->getMessage());
        }
    }

    public function enrollStudent(int $studentId, int $courseId): void {
        try {
            // Check if the student is already enrolled
            if ($this->isStudentEnrolled($studentId, $courseId)) {
                throw new RuntimeException('Student is already enrolled in this course');
            }

            // Check if the course exists and is published
            $course = $this->courseRepository->findById($courseId);
            if (!$course || !$course->isPublished()) {
                throw new RuntimeException('Course is not available for enrollment');
            }

            // Begin transaction
            $this->db->beginTransaction();

            // Create enrollment
            $query = "INSERT INTO enrollments (student_id, course_id, enrolled_at, progress_percentage) 
                     VALUES (:studentId, :courseId, :enrolledAt, 0)";
            
            $this->db->query($query, [
                'studentId' => $studentId,
                'courseId' => $courseId,
                'enrolledAt' => (new DateTime())->format('Y-m-d H:i:s')
            ]);

            // Create initial section completions
            $query = "INSERT INTO section_completions (student_id, section_id, completed_at)
                     SELECT :studentId, id, NULL
                     FROM course_sections
                     WHERE course_id = :courseId";
            
            $this->db->query($query, [
                'studentId' => $studentId,
                'courseId' => $courseId
            ]);

            $this->db->commit();
        } catch (PDOException $e) {
            $this->db->rollBack();
            throw new RuntimeException('Failed to enroll student: ' . $e->getMessage());
        }
    }

    public function updateProgress(int $studentId, int $courseId, int $sectionId): void {
        try {
            // Mark section as completed
            $query = "UPDATE section_completions 
                     SET completed_at = :completedAt
                     WHERE student_id = :studentId 
                     AND section_id = :sectionId";
            
            $this->db->query($query, [
                'completedAt' => (new DateTime())->format('Y-m-d H:i:s'),
                'studentId' => $studentId,
                'sectionId' => $sectionId
            ]);

            // Update overall progress
            $this->updateOverallProgress($studentId, $courseId);
        } catch (PDOException $e) {
            throw new RuntimeException('Failed to update progress: ' . $e->getMessage());
        }
    }

    public function getCourseProgress(int $studentId, int $courseId): array {
        try {
            $query = "SELECT 
                        cs.id as section_id,
                        cs.title as section_title,
                        cs.content as section_content,
                        cs.order_index,
                        sc.completed_at
                    FROM course_sections cs
                    LEFT JOIN section_completions sc ON cs.id = sc.section_id AND sc.student_id = :studentId
                    WHERE cs.course_id = :courseId
                    ORDER BY cs.order_index";

            $sections = $this->db->query($query, [
                'studentId' => $studentId,
                'courseId' => $courseId
            ])->fetchAll();

            return array_map(function($section) {
                return [
                    'id' => (int) $section['section_id'],
                    'title' => $section['section_title'],
                    'content' => $section['section_content'],
                    'order_index' => (int) $section['order_index'],
                    'completed' => $section['completed_at'] !== null,
                    'completed_at' => $section['completed_at'] ? new DateTime($section['completed_at']) : null
                ];
            }, $sections);
        } catch (PDOException $e) {
            throw new RuntimeException('Failed to fetch course progress: ' . $e->getMessage());
        }
    }

    public function isStudentEnrolled(int $studentId, int $courseId): bool {
        try {
            $query = "SELECT COUNT(*) as count 
                     FROM enrollments 
                     WHERE student_id = :studentId AND course_id = :courseId";
            
            $result = $this->db->query($query, [
                'studentId' => $studentId,
                'courseId' => $courseId
            ])->fetch();

            return (int) $result['count'] > 0;
        } catch (PDOException $e) {
            throw new RuntimeException('Failed to check enrollment status: ' . $e->getMessage());
        }
    }

    public function updateLastAccessed(int $studentId, int $courseId): void {
        try {
            $query = "UPDATE enrollments 
                     SET last_accessed_at = :lastAccessedAt
                     WHERE student_id = :studentId AND course_id = :courseId";
            
            $this->db->query($query, [
                'lastAccessedAt' => (new DateTime())->format('Y-m-d H:i:s'),
                'studentId' => $studentId,
                'courseId' => $courseId
            ]);
        } catch (PDOException $e) {
            throw new RuntimeException('Failed to update last accessed time: ' . $e->getMessage());
        }
    }

    private function updateOverallProgress(int $studentId, int $courseId): void {
        try {
            $query = "UPDATE enrollments e
                     SET progress_percentage = (
                         SELECT (COUNT(CASE WHEN sc.completed_at IS NOT NULL THEN 1 END) * 100.0 / COUNT(*))
                         FROM section_completions sc
                         INNER JOIN course_sections cs ON sc.section_id = cs.id
                         WHERE cs.course_id = :courseId AND sc.student_id = :studentId
                     )
                     WHERE e.student_id = :studentId AND e.course_id = :courseId";
            
            $this->db->query($query, [
                'studentId' => $studentId,
                'courseId' => $courseId
            ]);
        } catch (PDOException $e) {
            throw new RuntimeException('Failed to update overall progress: ' . $e->getMessage());
        }
    }
}