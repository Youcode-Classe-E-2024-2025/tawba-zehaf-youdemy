<?php
namespace Youdemy\Models\Entity;

use DateTime;
use PDO;
use PDOException;

class Course {
    private ?int $id = null;
    private string $title;
    private string $description;
    private string $content;
    private int $teacherId;
    private int $categoryId;
    private float $price;
    private ?DateTime $createdAt = null;
    private ?DateTime $updatedAt = null;
    private $published;
    private bool $isPublished;
    private $db;
    private array $tags = [];
    
private ?float $rating = null;
    public function __construct($db) {
        $this->db = $db;
    }

    public function getId(): ?int {
        return $this->id;
    }

    private function hydrateCourse(array $row): self {
        $course = new self($this->db);
        $course->setId($row['id'])
               ->setTitle($row['title'])
               ->setDescription($row['description'])
               ->setContent($row['content'])
               ->setTeacherId($row['teacher_id'])
               ->setCategoryId($row['category_id'])
               ->setPrice($row['price'])
               ->setCreatedAt(new DateTime($row['created_at']))
               ->setUpdatedAt(new DateTime($row['updated_at']))
               ->setIsPublished($row['is_published']);
        return $course;
    }

    public function setId(int $id): self {
        $this->id = $id;
        return $this;
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function setTitle(string $title): self {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function setDescription(string $description): self {
        $this->description = $description;
        return $this;
    }

    public function getContent(): string {
        return $this->content;
    }

    public function setContent(string $content): self {
        $this->content = $content;
        return $this;
    }

    public function getTeacherId(): int {
        return $this->teacherId;
    }

    public function setTeacherId(int $teacherId): self {
        $this->teacherId = $teacherId;
        return $this;
    }

    public function getCategoryId(): int {
        return $this->categoryId;
    }

    public function setCategoryId(int $categoryId): self {
        $this->categoryId = $categoryId;
        return $this;
    }

    public function getPrice(): float {
        return $this->price;
    }

    public function setPrice(float $price): self {
        $this->price = $price;
        return $this;
    }

    public function getCreatedAt(): ?DateTime {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): self {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?DateTime {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): self {
        $this->updatedAt = $updatedAt;
        return $this;
    }




    public function setIsPublished(bool $isPublished): void

    {

        $this->isPublished = $isPublished;

    }



    public function getIsPublished(): bool

    {

        return $this->isPublished;

    }

    public function setTags(array $tags): self {
        $this->tags = $tags;
        return $this;
    }

    public function getTags(): array {
        return $this->tags;
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




    public function getCourseById($id) {

        $stmt = $this->db->prepare('SELECT * FROM courses WHERE id = :id');

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);

    }
    
    
    
        public function getFeaturedCourses() {
    
            $stmt = $this->db->query("SELECT * FROM courses WHERE featured = 1");
    
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        }
    
       
        
        
        
            public function addTag($tag): void
        
            {
        
                $this->tags[] = $tag;
        
            }
        
        
        
            public function clearTags(): void
        
            {
        
                $this->tags = [];
        
            }
            public function isPublished(): bool {
                $stmt = $this->db->prepare("SELECT published FROM courses WHERE id = :courseId");
                $stmt->bindParam(':courseId', $this->id); // Assuming $this->id is the course ID
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                return $result['published'] ?? false; // Return false if not found
            }



    // existing properties and methods



    public function setRating($rating): void {

        $this->rating = $rating;

    }



    public function getRating() {

        return $this->rating;

    }
   
   
    
    
    
        // existing properties and methods
    
    
    
        public function setPublished(bool $published): void {
    
            $this->published = $published;
    
        }
    
    
    
        public function getPublished(): bool {
    
            return $this->published;
    
        }
    
    }

        
    
    