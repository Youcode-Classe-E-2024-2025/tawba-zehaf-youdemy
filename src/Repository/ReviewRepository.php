<?php

namespace Youdemy\Repository;

use Youdemy\Config\Database;
use Youdemy\Models\Entity\Review;
use PDOException;
use DateTime;

class ReviewRepository {
    private Database $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function save(Review $review): void {
        try {
            if ($review->getId()) {
                $this->update($review);
            } else {
                $this->create($review);
            }
        } catch (PDOException $e) {
            throw new \RuntimeException('Failed to save review: ' . $e->getMessage());
        }
    }

    public function findById(int $id): ?Review {
        $query = "SELECT * FROM reviews WHERE id = :id";
        $result = $this->db->query($query, ['id' => $id])->fetch();

        if (!$result) {
            return null;
        }

        return $this->hydrateReview($result);
    }

    public function findByCourseId(int $courseId): array {
        $query = "SELECT r.*, u.name as user_name 
                 FROM reviews r
                 JOIN users u ON r.user_id = u.id
                 WHERE r.course_id = :course_id
                 ORDER BY r.created_at DESC";
        
        $results = $this->db->query($query, ['course_id' => $courseId])->fetchAll();
        return array_map([$this, 'hydrateReview'], $results);
    }

    public function getAverageRating(int $courseId): float {
        $query = "SELECT AVG(rating) as average_rating 
                 FROM reviews 
                 WHERE course_id = :course_id";
        
        $result = $this->db->query($query, ['course_id' => $courseId])->fetch();
        return (float) ($result['average_rating'] ?? 0);
    }

    private function create(Review $review): void {
        $now = new DateTime();
        $params = [
            'user_id' => $review->getUserId(),
            'course_id' => $review->getCourseId(),
            'rating' => $review->getRating(),
            'comment' => $review->getComment(),
            'created_at' => $now->format('Y-m-d H:i:s')
        ];

        $query = "INSERT INTO reviews (user_id, course_id, rating, comment, created_at) 
                 VALUES (:user_id, :course_id, :rating, :comment, :created_at)";
        
        $this->db->query($query, $params);
        $review->setId($this->db->lastInsertId());
        $review->setCreatedAt($now);
    }

    private function update(Review $review): void {
        $params = [
            'id' => $review->getId(),
            'rating' => $review->getRating(),
            'comment' => $review->getComment(),
            'updated_at' => (new DateTime())->format('Y-m-d H:i:s')
        ];

        $query = "UPDATE reviews 
                 SET rating = :rating, 
                     comment = :comment, 
                     updated_at = :updated_at 
                 WHERE id = :id";

        $result = $this->db->query($query, $params)->rowCount();
        
        if ($result === 0) {
            throw new \RuntimeException('Review not found or no changes made');
        }
    }

    private function hydrateReview(array $data): Review {
        $review = new Review(
            $data['user_id'],
            $data['course_id'],
            $data['rating'],
            $data['comment']
        );
        $review->setId($data['id']);
        $review->setUserId($data['user_id']);
        $review->setCourseId($data['course_id']);
        $review->setRating($data['rating']);
        $review->setComment($data['comment']);
        $review->setCreatedAt(new DateTime($data['created_at']));
        
        if (isset($data['updated_at'])) {
            $review->setUpdatedAt(new DateTime($data['updated_at']));
        }
        
        if (isset($data['user_name'])) {
            $review->setUserName($data['user_name']);
        }
        
        return $review;
    }


    public function deleteByCourseId(int $courseId): void {

        $query = "DELETE FROM reviews WHERE course_id = :course_id";

        $this->db->query($query, ['course_id' => $courseId]);

    }

    
    
    
    
        public function getTotalCount(): int {
    
            $query = "SELECT COUNT(*) as total FROM reviews";
    
            $result = $this->db->query($query)->fetch();
    
            return (int) $result['total'];
    
        }

        
        
            public function count(): int {
        
                $query = "SELECT COUNT(*) as total FROM reviews";
        
                $result = $this->db->query($query)->fetch();
        
                return (int) $result['total'];
        
            }
         

    public function getNewReviewsByDateRange(DateTime $startDate, DateTime $endDate): array {

        $query = "SELECT * FROM reviews WHERE created_at BETWEEN :start_date AND :end_date";

        $params = [

            'start_date' => $startDate->format('Y-m-d H:i:s'),

            'end_date' => $endDate->format('Y-m-d H:i:s')

        ];

        return $this->db->query($query, $params)->fetchAll();

    }
    public function countReviewsSince(DateTime $startDate): int {

        $query = "SELECT COUNT(*) as review_count FROM reviews WHERE created_at >= :start_date";

        $params = ['start_date' => $startDate->format('Y-m-d H:i:s')];

        return (int) $this->db->query($query, $params)->fetch()['review_count'];

    }
   
    

    
        public function getOverallAverageRating(): float {
    
            $query = "SELECT AVG(rating) as average_rating FROM reviews";
    
            $result = $this->db->query($query)->fetch();
    
            return (float) $result['average_rating'];
    
        }
    
    }
    

        
    

    