<?php

namespace Youdemy\Models\Entity;

class Category
{
    private int $id;
    private string $name;
    private ?string $description;
    private \DateTime $createdAt;

    public function __construct(string $name, ?string $description = null)
    {
        $this->name = $name;
        $this->description = $description;
        $this->createdAt = new \DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }
}