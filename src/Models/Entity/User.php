<?php

namespace Youdemy\Models\Entity;

class User
{
    private int $id;
    private string $username;
    private string $email;
    private string $password;
    private string $role;
    private bool $isActive;
    private \DateTime $createdAt;
    private \DateTime $updatedAt;

    public function __construct(string $username, string $email, string $password, string $role = 'student')
    {
        $this->username = $username;
        $this->email = $email;
        $this->password = password_hash($password, PASSWORD_DEFAULT);
        $this->role = $role;
        $this->isActive = true;
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    // Getters and setters
    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): void
    {
        $this->role = $role;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }
}