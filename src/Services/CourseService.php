<?php

namespace Youdemy\Services;

use Exception;
use RuntimeException;
use Youdemy\Models\Entity\User;
use Youdemy\Models\Entity\Course;
use Youdemy\Repository\CourseRepository;
use Youdemy\Repository\TagRepository;
use Youdemy\Config\Database;

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
}