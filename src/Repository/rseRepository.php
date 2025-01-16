<?php
require_once 'config\Database.php';
class CourseRepository {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function findById($id) {
        $query = "SELECT * FROM courses WHERE id = :id";
        return $this->db->query($query, ['id' => $id])->fetch();
    }

    public function findByTeacherId($teacherId) {
        $query = "SELECT * FROM courses WHERE teacher_id = :teacher_id";
        return $this->db->query($query, ['teacher_id' => $teacherId])->fetchAll();
    }

    public function create($course) {
        $query = "INSERT INTO courses (title, description, content, teacher_id, category_id, price, created_at, updated_at) 
                  VALUES (:title, :description, :content, :teacher_id, :category_id, :price, :created_at, :updated_at)";
        return $this->db->query($query, $course)->lastInsertId();
    }

    public function update($course) {
        $query = "UPDATE courses 
                  SET title = :title, description = :description, content = :content, 
                      category_id = :category_id, price = :price, updated_at = :updated_at 
                  WHERE id = :id";
        return $this->db->query($query, $course)->rowCount();
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
}