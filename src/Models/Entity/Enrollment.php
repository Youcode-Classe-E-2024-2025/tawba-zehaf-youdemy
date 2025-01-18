<?php

namespace Youdemy\Models\Entity;

use \DateTime;

// class Enrollment
// {
//     private int $id;
//     private int $courseId;
//     private int $studentId;
//     private \DateTime $enrolledAt;
//     private \DateTime $enrollmentDate;

//     public function __construct(int $courseId, int $studentId)
//     {
//         $this->courseId = $courseId;
//         $this->studentId = $studentId;
//         $this->enrollmentDate = new \DateTime();
//     }

//     public function getId(): int
//     {
//         return $this->id;
//     }

//     public function getCourseId(): int
//     {
//         return $this->courseId;
//     }

//     public function getStudentId(): int
//     {
//         return $this->studentId;
//     }

//     public function getEnrollmentDate(): \DateTime
//     {
//         return $this->enrollmentDate;
//     }
//     public function setId(int $id): void

//     {

//         $this->id = $id;

//     }
//     public function setStudentId(int $studentId): void

//     {

//         $this->studentId = $studentId;

//     }
//     public function setCourseId(int $courseId): void

//     {

//         $this->courseId = $courseId;}

// }


class Enrollment

{

    private int $id;

    private int $studentId;

    private int $courseId;

    private DateTime $enrolledAt;



    public function __construct(int $studentId, int $courseId)

    {

        $this->studentId = $studentId;

        $this->courseId = $courseId;

        $this->enrolledAt = new DateTime();

    }



    public function getId(): int

    {

        return $this->id;

    }



    public function setId(int $id): void

    {

        $this->id = $id;

    }



    public function getStudentId(): int

    {

        return $this->studentId;

    }



    public function setStudentId(int $studentId): void

    {

        $this->studentId = $studentId;

    }



    public function getCourseId(): int

    {

        return $this->courseId;

    }



    public function setCourseId(int $courseId): void

    {

        $this->courseId = $courseId;

    }



    public function getEnrolledAt(): DateTime

    {

        return $this->enrolledAt;

    }



    public function setEnrolledAt(DateTime $enrolledAt): void

    {

        $this->enrolledAt = $enrolledAt;

    }

}