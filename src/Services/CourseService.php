<?php

namespace Youdemy\Services;

use Exception;
use RuntimeException;
use Youdemy\Models\Entity\User;
use Youdemy\Models\Entity\Course;
use Youdemy\Repository\CourseRepository;
use Youdemy\Repository\TagRepository;
use Youdemy\Config\Database;
use PDO;
use PDOException;

class CourseService
{
    private CourseRepository $courseRepository;
    private TagRepository $tagRepository;
    private Database $db;

    public function __construct(CourseRepository $courseRepository, TagRepository $tagRepository, Database $db)
    {
        $this->courseRepository = $courseRepository;
        $this->tagRepository = $tagRepository;
        $this->db = $db;
    }

    public function createCourse(string $title, string $description, string $content, User $teacher, ?int $categoryId, array $tagNames): Course
    {
        if ($teacher->getRole() !== 'teacher') {
            throw new RuntimeException("Only teachers can create courses");
        }

        $course = new Course($this->db);
        $course->setTitle($title);
        $course->setDescription($description);
        $course->setContent($content);
        $course->setTeacherId($teacher->getId());
        $course->setCategoryId($categoryId);
        $course->setIsPublished(false); // New courses are unpublished by default
        
        foreach ($tagNames as $tagName) {
            $tag = $this->tagRepository->findOrCreateByName($tagName);
            $course->addTag($tag);
        }

        $this->courseRepository->save($course);
        return $course;
    }

    public function updateCourse(int $courseId, string $title, string $description, string $content, ?int $categoryId, array $tagNames): Course
    {
        $course = $this->courseRepository->findById($courseId);
        if (!$course) {
            throw new RuntimeException("Course not found");
        }

        $course->setTitle($title);
        $course->setDescription($description);
        $course->setContent($content);
        $course->setCategoryId($categoryId);

        // Update tags
        $course->clearTags();
        foreach ($tagNames as $tagName) {
            $tag = $this->tagRepository->findOrCreateByName($tagName);
            $course->addTag($tag);
        }

        $this->courseRepository->save($course);
        return $course;
    }

    public function publishCourse(int $courseId): void
    {
        $course = $this->courseRepository->findById($courseId);
        if (!$course) {
            throw new RuntimeException("Course not found");
        }

        $course->setIsPublished(true);
        $this->courseRepository->save($course);
    }

    public function unpublishCourse(int $courseId): void
    {
        $course = $this->courseRepository->findById($courseId);
        if (!$course) {
            throw new RuntimeException("Course not found");
        }

        $course->setIsPublished(false);
        $this->courseRepository->save($course);
    }

    public function getCourse(int $courseId): ?Course
    {
        return $this->courseRepository->findById($courseId);
    }

    public function getPublishedCourses(): array
    {
        return $this->courseRepository->findPublished();
    }

    public function getCoursesByTeacher(int $teacherId): array
    {
        return $this->courseRepository->findByTeacherId($teacherId);
    }
    public function getRecommendedCourses(int $userId): array {
        $stmt = $this->db->getConnection()->prepare("SELECT courses.* FROM courses
            JOIN user_courses ON courses.id = user_courses.course_id
            WHERE user_courses.user_id = :userId
            ORDER BY courses.rating DESC");
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getAllCategories(): array {
        $stmt = $this->db->getConnection()->prepare("SELECT * FROM categories");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getTeacherStats($teacherId)
    {
        try {
            $db = Database::getInstance()->getConnection();
            
            $sql = "SELECT 
                    COUNT(DISTINCT c.id) as total_courses,
                    COUNT(DISTINCT e.student_id) as total_students,
                    COUNT(DISTINCT e.id) as total_enrollments
                    FROM courses c
                    LEFT JOIN enrollments e ON c.id = e.course_id
                    WHERE c.teacher_id = ?";
                    
            $stmt = $db->prepare($sql);
            $stmt->execute([$teacherId]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            throw new RuntimeException('Failed to fetch teacher statistics: ' . $e->getMessage());
        }
    }
    public function getEnrolledCourses(int $studentId): array
    {
        $db = Database::getInstance()->getConnection();
        
        $sql = "SELECT 
                c.id, 
                c.title, 
                c.description,
                c.created_at,
                u.username as teacher_name
                FROM courses c
                INNER JOIN enrollments e ON c.id = e.course_id
                INNER JOIN users u ON c.teacher_id = u.id
                WHERE e.student_id = ?
                ORDER BY c.created_at DESC";
                
        $stmt = $db->prepare($sql);
        $stmt->execute([$studentId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
public function getAvailableCourses(int $studentId): array
{
    $db = Database::getInstance()->getConnection();
    
    $sql = "SELECT 
            c.id, 
            c.title, 
            c.description,
            c.created_at,
            u.username as teacher_name,
            cat.name as category_name
            FROM courses c
            INNER JOIN users u ON c.teacher_id = u.id
            LEFT JOIN categories cat ON c.category_id = cat.id
            WHERE c.id NOT IN (
                SELECT course_id 
                FROM enrollments 
                WHERE student_id = ?
            )
            ORDER BY c.created_at DESC";
            
    $stmt = $db->prepare($sql);
    $stmt->execute([$studentId]);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    
    public function getCourses($page, $coursesPerPage = 10) {
        $offset = ($page - 1) * $coursesPerPage; // Calculate the offset for pagination
        $stmt = $this->db->getConnection()->prepare('SELECT * FROM courses LIMIT :limit OFFSET :offset');
        $stmt->bindValue(':limit', $coursesPerPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(); // Return the list of courses
    }

    public function searchCourses($keyword) {
        $stmt = $this->db->getConnection()->prepare('SELECT * FROM courses WHERE title LIKE :keyword');
        $stmt->bindValue(':keyword', '%' . $keyword . '%', PDO::PARAM_STR); // Use wildcard for searching
        $stmt->execute();
        return $stmt->fetchAll(); // Return the list of matching courses
    }

    public function getTotalCourses() {
        $stmt = $this->db->query('SELECT COUNT(*) FROM courses');
        return $stmt->fetchColumn(); // Return the total number of courses
    }
    public function getCourseById($id) {
        $stmt = $this->db->getConnection()->prepare('SELECT * FROM courses WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        // return $stmt->fetch(PDO::FETCH_ASSOC);
        return $this->courseRepository->findById($id);
    }
    public function getAllCourses()
{
    // Establish database connection
    $db = new Database(); // Assuming Database class handles the connection
    $connection = $db->getConnection(); // Get the connection method

    // Prepare and execute the SQL query
    $query = "SELECT * FROM courses"; // Replace 'courses' with your actual table name
    $stmt = $connection->prepare($query);
    $stmt->execute();

    // Fetch all courses
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch as associative array

    return $courses; // Return the array of courses
}
    public function enrollInCourse($courseId, $userId) {
        $stmt = $this->db->getConnection()->prepare('INSERT INTO enrollments (course_id, user_id) VALUES (:course_id, :user_id)');
        $stmt->bindValue(':course_id', $courseId, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
    }
    public function addCourse($title, $description, $contentPath, $tags, $category) {
        $stmt = $this->db->getConnection()->prepare('INSERT INTO courses (title, description, content, tags, category) VALUES (:title, :description, :content, :tags, :category)');
        $stmt->bindValue(':title', $title, PDO::PARAM_STR);
        $stmt->bindValue(':description', $description, PDO::PARAM_STR);
        $stmt->bindValue(':content', $contentPath, PDO::PARAM_STR); // Save the file path
        $stmt->bindValue(':tags', $tags, PDO::PARAM_STR);
        $stmt->bindValue(':category', $category, PDO::PARAM_STR);
        $stmt->execute();
    }
    public function getTeacherCourses($teacherId)
{
    try {
        $db = Database::getInstance()->getConnection();
        
        $sql = "SELECT 
                c.id,
                c.title,
                c.description,
                c.created_at,
                COUNT(e.id) as student_count
                FROM courses c
                LEFT JOIN enrollments e ON c.id = e.course_id
                WHERE c.teacher_id = ?
                GROUP BY c.id
                ORDER BY c.created_at DESC";
                
        $stmt = $db->prepare($sql);
        $stmt->execute([$teacherId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        throw new RuntimeException('Failed to fetch teacher courses: ' . $e->getMessage());
    }
}

}