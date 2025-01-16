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

    public function findById(int $id): ?Tag
    {
        $stmt = $this->db->prepare("SELECT * FROM tags WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $tagData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$tagData) {
            return null;
        }

        return $this->createTagFromData($tagData);
    }

    public function findAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM tags");
        $tagsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $tags = [];
        foreach ($tagsData as $tagData) {
            $tags[] = $this->createTagFromData($tagData);
        }

        return $tags;
    }

    public function save(Tag $tag): void
    {
        if ($tag->getId()) {
            $this->update($tag);
        } else {
            $this->insert($tag);
        }
    }

    private function insert(Tag $tag): void
    {
        $stmt = $this->db->prepare("INSERT INTO tags (name) VALUES (:name)");
        $stmt->execute(['name' => $tag->getName()]);
    }

    private function update(Tag $tag): void
    {
        $stmt = $this->db->prepare("UPDATE tags SET name = :name WHERE id = :id");
        $stmt->execute([
            'id' => $tag->getId(),
            'name' => $tag->getName()
        ]);
    }

    private function createTagFromData(array $tagData): Tag
    {
        $tag = new Tag($tagData['name']);
        // Set other properties...
        return $tag;
    }
}

