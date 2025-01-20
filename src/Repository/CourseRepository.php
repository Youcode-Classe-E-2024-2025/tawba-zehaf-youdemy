<?php 
namespace Youdemy\Repository;

use Youdemy\Config\Database; 
use Youdemy\Models\Entity\Course;
use PDOException;
use DateTime;

class CourseRepository {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function save(Course $course): void {
        try {
            if ($course->getId()) {
                $this->update($course);
            } else {
                $this->create($course);
            }
        } catch (PDOException $e) {
            throw new \RuntimeException('Failed to save course: ' . $e->getMessage());
        }
    }

    public function findById($id): ?Course {
        $query = "SELECT * FROM courses WHERE id = :id";
        $result = $this->db->query($query, ['id' => $id])->fetch();
        
        if (!$result) {
            return null;
        }

        return $this->hydrateCourse($result);
    }

    public function findByTeacherId($teacherId): array {
        $query = "SELECT * FROM courses WHERE teacher_id = :teacher_id";
        $results = $this->db->query($query, ['teacher_id' => $teacherId])->fetchAll();
        
        return array_map([$this, 'hydrateCourse'], $results);
    }

    private function create(Course $course): void {
        $now = new DateTime();
        $params = [
            'title' => $course->getTitle(),
            'description' => $course->getDescription(),
            'content' => $course->getContent(),
            'teacher_id' => $course->getTeacherId(),
            'category_id' => $course->getCategoryId(),
            'price' => $course->getPrice(),
            'created_at' => $now->format('Y-m-d H:i:s'),
            'updated_at' => $now->format('Y-m-d H:i:s')
        ];

        $query = "INSERT INTO courses (title, description, content, teacher_id, category_id, price, created_at, updated_at) 
                  VALUES (:title, :description, :content, :teacher_id, :category_id, :price, :created_at, :updated_at)";
        
        $this->db->query($query, $params);
        $course->setId($this->db->lastInsertId());
    }

    private function update(Course $course): void {
        $now = new DateTime();
        $params = [
            'id' => $course->getId(),
            'title' => $course->getTitle(),
            'description' => $course->getDescription(),
            'content' => $course->getContent(),
            'category_id' => $course->getCategoryId(),
            'price' => $course->getPrice(),
            'updated_at' => $now->format('Y-m-d H:i:s')
        ];

        $query = "UPDATE courses 
                  SET title = :title, 
                      description = :description, 
                      content = :content, 
                      category_id = :category_id, 
                      price = :price, 
                      updated_at = :updated_at 
                  WHERE id = :id";

        $result = $this->db->query($query, $params)->rowCount();
        
        if ($result === 0) {
            throw new \RuntimeException('Course not found or no changes made');
        }
    }

  
    public function delete($id) {
        $query = "DELETE FROM courses WHERE id = :id";
        return $this->db->query($query, ['id' => $id])->rowCount();
    }

    public function findFeatured($limit) {
        $query = "SELECT c.*, u.name as teacher_name, AVG(r.rating) as average_rating 
                  FROM courses c 
                  JOIN users u ON c.teacher_id = u.id 
                  LEFT JOIN reviews r ON c.id = r.course_id 
                  GROUP BY c.id 
                  ORDER BY average_rating DESC, c.created_at DESC 
                  LIMIT :limit";
        return $this->db->query($query, ['limit' => $limit])->fetchAll();
    }

    public function search($query, $page, $perPage) {
        $offset = ($page - 1) * $perPage;
        $searchQuery = "SELECT c.*, u.name as teacher_name, AVG(r.rating) as average_rating 
                        FROM courses c 
                        JOIN users u ON c.teacher_id = u.id 
                        LEFT JOIN reviews r ON c.id = r.course_id 
                        WHERE c.title LIKE :query OR c.description LIKE :query 
                        GROUP BY c.id 
                        ORDER BY average_rating DESC, c.created_at DESC 
                        LIMIT :limit OFFSET :offset";
        $params = [
            'query' => "%$query%",
            'limit' => $perPage,
            'offset' => $offset
        ];
        return $this->db->query($searchQuery, $params)->fetchAll();
    }

    public function addTags($courseId, $tags) {
        $query = "INSERT INTO course_tags (course_id, tag) VALUES (:course_id, :tag)";
        foreach ($tags as $tag) {
            $this->db->query($query, ['course_id' => $courseId, 'tag' => $tag]);
        }
    }

    public function updateTags($courseId, $tags) {
        $deleteQuery = "DELETE FROM course_tags WHERE course_id = :course_id";
        $this->db->query($deleteQuery, ['course_id' => $courseId]);
        $this->addTags($courseId, $tags);
    }

    public function getAllCourses(int $page = 1, int $perPage = 10, array $filters = []): array {
        try {
            $offset = ($page - 1) * $perPage;
            $params = [
                'limit' => $perPage,
                'offset' => $offset
            ];
            
            $whereConditions = [];
            if (isset($filters['isPublished'])) {
                $whereConditions[] = "c.is_published = :isPublished";
                $params['isPublished'] = $filters['isPublished'];
            }
            
            if (isset($filters['categoryId'])) {
                $whereConditions[] = "c.category_id = :categoryId";
                $params['categoryId'] = $filters['categoryId'];
            }
            
            if (isset($filters['teacherId'])) {
                $whereConditions[] = "c.teacher_id = :teacherId";
                $params['teacherId'] = $filters['teacherId'];
            }
            
            $whereClause = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";
            
            $query = "SELECT c.*, 
                            u.name as teacher_name,
                            cat.name as category_name,
                            (SELECT AVG(rating) FROM reviews r WHERE r.course_id = c.id) as average_rating,
                            (SELECT COUNT(*) FROM reviews r WHERE r.course_id = c.id) as reviews_count,
                            (SELECT COUNT(*) FROM enrollments e WHERE e.course_id = c.id) as students_count,
                            GROUP_CONCAT(t.name) as tags
                     FROM courses c
                     LEFT JOIN users u ON c.teacher_id = u.id
                     LEFT JOIN categories cat ON c.category_id = cat.id
                     LEFT JOIN course_tags ct ON c.id = ct.course_id
                     LEFT JOIN tags t ON ct.tag_id = t.id
                     $whereClause
                     GROUP BY c.id
                     ORDER BY c.created_at DESC
                     LIMIT :limit OFFSET :offset";

            $results = $this->db->query($query, $params)->fetchAll();
            
            return array_map(function($row) {
                $course = $this->hydrateCourse($row);
                if (isset($row['tags'])) {
                    $course->setTags(explode(',', $row['tags']));
                }
                return $course;
            }, $results);
        } catch (PDOException $e) {
            throw new \RuntimeException('Failed to fetch courses: ' . $e->getMessage());
        }
    }

    public function findPublished(): array {
        return $this->getAllCourses(1, 10, ['isPublished' => true]);
    }

    public function searchCourses(string $searchTerm, int $page = 1, int $perPage = 10): array {
        try {
            $offset = ($page - 1) * $perPage;
            $searchTerm = "%$searchTerm%";
            
            $query = "SELECT c.*, 
                            u.name as teacher_name,
                            (SELECT AVG(rating) FROM reviews r WHERE r.course_id = c.id) as average_rating,
                            (SELECT COUNT(*) FROM reviews r WHERE r.course_id = c.id) as reviews_count
                     FROM courses c
                     LEFT JOIN users u ON c.teacher_id = u.id
                     WHERE c.title LIKE :searchTerm 
                        OR c.description LIKE :searchTerm
                        OR EXISTS (
                            SELECT 1 FROM course_tags ct
                            JOIN tags t ON ct.tag_id = t.id
                            WHERE ct.course_id = c.id AND t.name LIKE :searchTerm
                        )
                     ORDER BY c.created_at DESC
                     LIMIT :limit OFFSET :offset";

            $results = $this->db->query($query, [
                'searchTerm' => $searchTerm,
                'limit' => $perPage,
                'offset' => $offset
            ])->fetchAll();

            return array_map([$this, 'hydrateCourse'], $results);
        } catch (PDOException $e) {
            throw new \RuntimeException('Failed to search courses: ' . $e->getMessage());
        }
    }

    public function getTotalCourses(): int {
        try {
            $query = "SELECT COUNT(*) as total FROM courses";
            $result = $this->db->query($query)->fetch();
            return (int) $result['total'];
        } catch (PDOException $e) {
            throw new \RuntimeException('Failed to count courses: ' . $e->getMessage());
        }
    }

    public function getEnrolledCourses(int $studentId): array {
        try {
            $query = "SELECT c.*, 
                            u.name as teacher_name,
                            (SELECT AVG(rating) FROM reviews r WHERE r.course_id = c.id) as average_rating,
                            (SELECT COUNT(*) FROM reviews r WHERE r.course_id = c.id) as reviews_count,
                            e.enrolled_at,
                            GROUP_CONCAT(t.name) as tags
                     FROM courses c
                     INNER JOIN enrollments e ON c.id = e.course_id
                     LEFT JOIN users u ON c.teacher_id = u.id
                     LEFT JOIN course_tags ct ON c.id = ct.course_id
                     LEFT JOIN tags t ON ct.tag_id = t.id
                     WHERE e.student_id = :studentId
                     GROUP BY c.id
                     ORDER BY e.enrolled_at DESC";

            $results = $this->db->query($query, ['studentId' => $studentId])->fetchAll();

            return array_map(function($row) {
                $course = $this->hydrateCourse($row);
                if (isset($row['tags'])) {
                    $course->setTags(explode(',', $row['tags']));
                }
                return $course;
            }, $results);
        } catch (PDOException $e) {
            throw new \RuntimeException('Failed to fetch enrolled courses: ' . $e->getMessage());
        }
    }

    public function getEnrollmentProgress(int $studentId, int $courseId): array {
        try {
            $query = "SELECT 
                        c.*,
                        e.enrolled_at,
                        e.last_accessed_at,
                        e.progress_percentage,
                        (SELECT COUNT(*) FROM course_sections WHERE course_id = c.id) as total_sections,
                        (SELECT COUNT(*) FROM section_completions sc 
                         INNER JOIN course_sections cs ON sc.section_id = cs.id 
                         WHERE cs.course_id = c.id AND sc.student_id = :studentId) as completed_sections
                     FROM courses c
                     INNER JOIN enrollments e ON c.id = e.course_id
                     WHERE e.student_id = :studentId AND c.id = :courseId";

            $result = $this->db->query($query, [
                'studentId' => $studentId,
                'courseId' => $courseId
            ])->fetch();

            if (!$result) {
                return null;
            }

            $course = $this->hydrateCourse($result);
            return [
                'course' => $course,
                'enrolled_at' => new DateTime($result['enrolled_at']),
                'last_accessed_at' => $result['last_accessed_at'] ? new DateTime($result['last_accessed_at']) : null,
                'progress_percentage' => (float) $result['progress_percentage'],
                'total_sections' => (int) $result['total_sections'],
                'completed_sections' => (int) $result['completed_sections']
            ];
        } catch (PDOException $e) {
            throw new \RuntimeException('Failed to fetch enrollment progress: ' . $e->getMessage());
        }
    }
    public function hydrateCourse(array $data): Course {
        $course = new Course($this->db);
        $course->setId($data['id']);
        $course->setTitle($data['title']);
        $course->setDescription($data['description']);
        $course->setPublished($data['published']);
        $course->setRating($data['rating']);
        $course->setContent($data['content']);
        $course->setTeacherId($data['teacher_id']);
        $course->setCategoryId($data['category_id']);
        $course->setPrice($data['price']);
        $course->setCreatedAt(new DateTime($data['created_at']));
        $course->setUpdatedAt(new DateTime($data['updated_at']));
        
    
        return $course;
    }


 
}