<?php

namespace Youdemy\Models\Repository;

use PDO;
use Youdemy\Models\Entity\Review;

class ReviewRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function findById(int $id): ?Review
    {
        $stmt = $this->db->prepare("SELECT * FROM reviews WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $reviewData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$reviewData) {
            return null;
        }

        return new Review(
            $reviewData['user_id'],
            $reviewData['course_id'],
            $reviewData['rating'],
            $reviewData['comment']
        );
    }

    public function findByCourseId(int $courseId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM reviews WHERE course_id = :course_id");
        $stmt->execute(['course_id' => $courseId]);
        $reviewsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $reviews = [];
        foreach ($reviewsData as $reviewData) {
            $review = new Review(
                $reviewData['user_id'],
                $reviewData['course_id'],
                $reviewData['rating'],
                $reviewData['comment']
            );
            $reviews[] = $review;
        }

        return $reviews;
    }

    public function save(Review $review): void
    {
        if ($review->getId()) {
            $this->update($review);
        } else {
            $this->insert($review);
        }
    }

    private function insert(Review $review): void
    {
        $stmt = $this->db->prepare("INSERT INTO reviews (user_id, course_id, rating, comment, created_at) VALUES (:user_id, :course_id, :rating, :comment, :created_at)");
        $stmt->execute([
            'user_id' => $review->getUserId(),
            'course_id' => $review->getCourseId(),
            'rating' => $review->getRating(),
            'comment' => $review->getComment(),
            'created_at' => $review->getCreatedAt()->format('Y-m-d H:i:s')
        ]);
    }

    private function update(Review $review): void
    {
        $stmt = $this->db->prepare("UPDATE reviews SET rating = :rating, comment = :comment WHERE id = :id");
        $stmt->execute([
            'id' => $review->getId(),
            'rating' => $review->getRating(),
            'comment' => $review->getComment()
        ]);
    }
}

