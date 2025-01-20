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
    public function getTeacherStats(int $teacherId): array {
        $stmt = $this->db->getConnection()->prepare("
            SELECT COUNT(*) as course_count, AVG(courses.rating) as average_rating
            FROM courses
            WHERE teacher_id = :teacherId
        ");
        $stmt->bindParam(':teacherId', $teacherId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
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
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function enrollInCourse($courseId, $userId) {
        $stmt = $this->db->getConnection()->prepare('INSERT INTO enrollments (course_id, user_id) VALUES (:course_id, :user_id)');
        $stmt->bindValue(':course_id', $courseId, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
    }

}