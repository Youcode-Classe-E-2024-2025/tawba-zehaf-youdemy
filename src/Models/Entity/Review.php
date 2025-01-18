<?php

namespace Youdemy\Models\Entity;

class Review
{
    private int $id;
    private int $userId;
    private int $courseId;
    private int $rating;
    private string $comment;

    private \DateTime $createdAt;

    private ?\DateTime $updatedAt = null;

    private ?string $userName = null;

    public function __construct(int $userId, int $courseId, int $rating, string $comment)
    {
        $this->userId = $userId;
        $this->courseId = $courseId;
        $this->rating = $rating;
        $this->comment = $comment;
        $this->createdAt = new \DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getCourseId(): int
    {
        return $this->courseId;
    }

    public function getRating(): int
    {
        return $this->rating;
    }

    public function setRating(int $rating): void
    {
        $this->rating = $rating;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function setComment(string $comment): void
    {
        $this->comment = $comment;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }
    public function setId(int $id): void {

        $this->id = $id;

    }
    public function setCreatedAt(\DateTime $createdAt): void {

        $this->createdAt = $createdAt;

    }
    public function setCourseId(int $courseId): void {

        $this->courseId = $courseId;

    }
    public function setUserId(int $userId): void {

        $this->userId = $userId;
    }
    public function setUpdatedAt(?\DateTime $updatedAt): void {

        $this->updatedAt = $updatedAt;

    }

    public function setUserName(string $userName): void {

        $this->userName = $userName;

    }



    public function getUserName(): ?string {

        return $this->userName;

    }

}