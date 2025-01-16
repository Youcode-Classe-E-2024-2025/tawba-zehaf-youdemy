<?php

namespace Youdemy\Models\Repository;

use PDO;
use Youdemy\Models\Entity\Enrollment;

class EnrollmentRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function findById(int $id): ?Enrollment
    {
        $stmt = $this->db->prepare("SELECT * FROM enrollments WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $enrollmentData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$enrollmentData) {
            return null;
        }

        return $this->createEnrollmentFromData($enrollmentData);
    }

    public function findByUserAndCourse(int $userId, int $courseId): ?Enrollment
    {
        $stmt = $this->db->prepare("SELECT * FROM enrollments WHERE student_id = :student_id AND course_id = :course_id");
        $stmt->execute(['student_id' => $userId, 'course_id' => $courseId]);
        $enrollmentData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$enrollmentData) {
            return null;
        }

        return $this->createEnrollmentFromData($enrollmentData);
    }

    public function findByUserId(int $userId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM enrollments WHERE student_id = :student_id");
        $stmt->execute(['student_id' => $userId]);
        $enrollmentsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $enrollments = [];
        foreach ($enrollmentsData as $enrollmentData) {
            $enrollments[] = $this->createEnrollmentFromData($enrollmentData);
        }

        return $enrollments;
    }

    public function save(Enrollment $enrollment): void
    {
        if ($enrollment->getId()) {
            $this->update($enrollment);
        } else {
            $this->insert($enrollment);
        }
    }

    private function insert(Enrollment $enrollment): void
    {
        $stmt = $this->db->prepare("INSERT INTO enrollments (course_id, student_id) VALUES (:course_id, :student_id)");
        $stmt->execute([
            'course_id' => $enrollment->getCourseId(),
            'student_id' => $enrollment->getStudentId()
        ]);
    }

    private function update(Enrollment $enrollment): void
    {
        // In this case, we don't need to update enrollments, but we'll keep this method for consistency
        // You might want to add additional fields to the Enrollment entity if updates are needed in the future
    }

    private function createEnrollmentFromData(array $enrollmentData): Enrollment
    {
        $enrollment = new Enrollment($enrollmentData['course_id'], $enrollmentData['student_id']);
        // Set other properties...
        return $enrollment;
    }
}

