<?php

namespace Youdemy\Models\Repository;

use PDO;
use Youdemy\Models\Entity\Category;

class CategoryRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function findById(int $id): ?Category
    {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $categoryData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$categoryData) {
            return null;
        }

        return $this->createCategoryFromData($categoryData);
    }

    public function findAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM categories");
        $categoriesData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $categories = [];
        foreach ($categoriesData as $categoryData) {
            $categories[] = $this->createCategoryFromData($categoryData);
        }

        return $categories;
    }

    public function save(Category $category): void
    {
        if ($category->getId()) {
            $this->update($category);
        } else {
            $this->insert($category);
        }
    }

    private function insert(Category $category): void
    {
        $stmt = $this->db->prepare("INSERT INTO categories (name, description) VALUES (:name, :description)");
        $stmt->execute([
            'name' => $category->getName(),
            'description' => $category->getDescription()
        ]);
    }

    private function update(Category $category): void
    {
        $stmt = $this->db->prepare("UPDATE categories SET name = :name, description = :description WHERE id = :id");
        $stmt->execute([
            'id' => $category->getId(),
            'name' => $category->getName(),
            'description' => $category->getDescription()
        ]);
    }

    private function createCategoryFromData(array $categoryData): Category
    {
        $category = new Category($categoryData['name'], $categoryData['description']);
        // Set other properties...
        return $category;
    }
}

