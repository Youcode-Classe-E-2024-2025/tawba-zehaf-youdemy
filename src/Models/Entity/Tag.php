<?php

namespace Youdemy\Models\Entity;

class Tag
{
    private int $id;
    private string $name;
    private \DateTime $createdAt;
    private \DateTime $updatedAt;
    public function __construct(string $name)
    {
        $this->name = $name;
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
    
    
        public function setId(int $id): void
        {
            $this->id = $id;
        }
    
        public function getCreatedAt(): \DateTime
        {
            return $this->createdAt;
        }
    public function setCreatedAt(\DateTime $createdAt): void {

        $this->createdAt = $createdAt;

    }



    public function setUpdatedAt(\DateTime $updatedAt): void {

        $this->updatedAt = $updatedAt;

    }

    }