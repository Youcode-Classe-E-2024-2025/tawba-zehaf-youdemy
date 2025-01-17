<?php

namespace Youdemy\Models\Repository;

use PDO;
use Youdemy\Models\Entity\Tag;

class TagRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Find a tag by its ID
     */
    public function find(int $id): ?Tag
    {
        $stmt = $this->db->prepare('SELECT * FROM tags WHERE id = :id');
        $stmt->execute(['id' => $id]);
        
        $tagData = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$tagData) {
            return null;
        }
        
        return $this->createTagFromData($tagData);
    }

    /**
     * Find all tags
     * @return Tag[]
     */
    public function findAll(): array
    {
        $stmt = $this->db->query('SELECT * FROM tags ORDER BY name');
        $tagsData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return array_map([$this, 'createTagFromData'], $tagsData);
    }

    /**
     * Find tags by name (partial match)
     * @return Tag[]
     */
    public function findByName(string $name): array
    {
        $stmt = $this->db->prepare('SELECT * FROM tags WHERE name LIKE :name');
        $stmt->execute(['name' => "%$name%"]);
        $tagsData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return array_map([$this, 'createTagFromData'], $tagsData);
    }

    /**
     * Save a new tag or update an existing one
     */
    public function save(Tag $tag): Tag
    {
        if ($tag->getId()) {
            return $this->update($tag);
        }
        
        return $this->create($tag);
    }

    /**
     * Delete a tag
     */
    public function delete(Tag $tag): void
    {
        $stmt = $this->db->prepare('DELETE FROM tags WHERE id = :id');
        $stmt->execute(['id' => $tag->getId()]);
    }

    /**
     * Create a new tag
     */
    private function create(Tag $tag): Tag
    {
        $stmt = $this->db->prepare('
            INSERT INTO tags (name, created_at) 
            VALUES (:name, :created_at)
        ');
        
        $stmt->execute([
            'name' => $tag->getName(),
            'created_at' => $tag->getCreatedAt()->format('Y-m-d H:i:s')
        ]);
        
        // Set the ID on the tag object
        $tag->setId($this->db->lastInsertId());
        
        return $tag;
    }

    /**
     * Update an existing tag
     */
    private function update(Tag $tag): Tag
    {
        $stmt = $this->db->prepare('
            UPDATE tags 
            SET name = :name 
            WHERE id = :id
        ');
        
        $stmt->execute([
            'id' => $tag->getId(),
            'name' => $tag->getName()
        ]);
        
        return $tag;
    }

    /**
     * Create a Tag object from database data
     */
    private function createTagFromData(array $data): Tag
    {
        $tag = new Tag($data['name']);
        $tag->setId($data['id']);
        // Set created_at if you need it
        return $tag;
    }
}