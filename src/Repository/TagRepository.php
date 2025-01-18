<?php

namespace Youdemy\Repository;

use Youdemy\Config\Database;
use Youdemy\Models\Entity\Tag;
use PDOException;
use DateTime;


  

class TagRepository {
    private Database $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function save(Tag $tag): void {
        try {
            if ($tag->getId()) {
                $this->update($tag);
            } else {
                $this->create($tag);
            }
        } catch (PDOException $e) {
            throw new \RuntimeException('Failed to save tag: ' . $e->getMessage());
        }
    }

    public function findById(int $id): ?Tag {
        $query = "SELECT * FROM tags WHERE id = :id";
        $result = $this->db->query($query, ['id' => $id])->fetch();

        if (!$result) {
            return null;
        }

        return $this->hydrateTag($result);
    }

    public function findAll(): array {
        $query = "SELECT * FROM tags ORDER BY name";
        $results = $this->db->query($query)->fetchAll();
        return array_map([$this, 'hydrateTag'], $results);
    }

    public function findByName(string $name): array {
        $query = "SELECT * FROM tags WHERE name LIKE :name";
        $results = $this->db->query($query, ['name' => "%$name%"])->fetchAll();
        return array_map([$this, 'hydrateTag'], $results);
    }

    public function delete(int $id): void {
        try {
            $query = "DELETE FROM tags WHERE id = :id";
            $result = $this->db->query($query, ['id' => $id])->rowCount();
            
            if ($result === 0) {
                throw new \RuntimeException('Tag not found');
            }
        } catch (PDOException $e) {
            throw new \RuntimeException('Failed to delete tag: ' . $e->getMessage());
        }
    }

    public function findByCourse(int $courseId): array {
        $query = "SELECT t.* 
                 FROM tags t
                 JOIN course_tags ct ON t.id = ct.tag_id
                 WHERE ct.course_id = :course_id
                 ORDER BY t.name";
        
        $results = $this->db->query($query, ['course_id' => $courseId])->fetchAll();
        return array_map([$this, 'hydrateTag'], $results);
    }

    private function create(Tag $tag): void {
        $now = new DateTime();
        $params = [
            'name' => $tag->getName(),
            'created_at' => $now->format('Y-m-d H:i:s'),
            'updated_at' => $now->format('Y-m-d H:i:s')
        ];

        $query = "INSERT INTO tags (name, created_at, updated_at) 
                 VALUES (:name, :created_at, :updated_at)";
        
        $this->db->query($query, $params);
        $tag->setId($this->db->lastInsertId());
        $tag->setCreatedAt($now);
        $tag->setUpdatedAt($now);
    }

    private function update(Tag $tag): void {
        $now = new DateTime();
        $params = [
            'id' => $tag->getId(),
            'name' => $tag->getName(),
            'updated_at' => $now->format('Y-m-d H:i:s')
        ];

        $query = "UPDATE tags 
                 SET name = :name,
                     updated_at = :updated_at
                 WHERE id = :id";

        $result = $this->db->query($query, $params)->rowCount();
        
        if ($result === 0) {
            throw new \RuntimeException('Tag not found or no changes made');
        }
        
        $tag->setUpdatedAt($now);
    }

    private function hydrateTag(array $data): Tag {
        $tag = new Tag($data['name']);
        $tag->setId($data['id']);
        $tag->setName($data['name']);
        $tag->setCreatedAt(new DateTime($data['created_at']));
        
        if (isset($data['updated_at'])) {
            $tag->setUpdatedAt(new DateTime($data['updated_at']));
        }
        
        return $tag;
    }
    public function findOrCreateByName(string $name)

    {

        $tag = $this->findByName($name);

        if (!$tag) {

            $tag = new Tag($name);

            $tag->setName($name);

            $this->save($tag);

        }

        return $tag;

    }
}