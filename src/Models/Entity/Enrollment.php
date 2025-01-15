<?php

namespace Youdemy\Models\Entity;

class Enrollment
{
    private int $id;
    private int $courseId;
    private int $studentId;
    private \DateTime $enrollmentDate;

    public function __construct(int $courseId, int $studentId)
    {
        $this->courseId = $courseId;
        $this->studentId = $studentId;
        $this->enrollmentDate = new \DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCourseId(): int
    {
        return $this->courseId;
    }

    public function getStudentId(): int
    {
        return $this->studentId;
    }

    public function getEnrollmentDate(): \DateTime
    {
        return $this->enrollmentDate;
    }
}