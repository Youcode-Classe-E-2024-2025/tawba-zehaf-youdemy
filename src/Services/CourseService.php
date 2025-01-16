<?php
require_once 'src\Repository\TagRepository.php';
class CourseService
{
    private CourseRepository $courseRepository;
    private TagRepository $tagRepository;

    public function __construct(CourseRepository $courseRepository, TagRepository $tagRepository)
    {
        $this->courseRepository = $courseRepository;
        $this->tagRepository = $tagRepository;
    }

    public function createCourse(string $title, string $description, string $content, User $teacher, ?int $categoryId, array $tagNames): Course
    {
        if ($teacher->getRole() !== 'teacher') {
            throw new \Exception("Only teachers can create courses.");
        }

        $course = new Course($title, $description, $content, $teacher->getId(), $categoryId);
        
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
            throw new \Exception("Course not found.");
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
            throw new \Exception("Course not found.");
        }

        $course->setIsPublished(true);
        $this->courseRepository->save($course);
    }

    public function unpublishCourse(int $courseId): void
    {
        $course = $this->courseRepository->findById($courseId);
        if (!$course) {
            throw new \Exception("Course not found.");
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