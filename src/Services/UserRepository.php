<?php

namespace Youdemy\Models\Repository;

use PDO;
use Youdemy\Models\Entity\User;

class UserRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function findById(int $id): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$userData) {
            return null;
        }

        return $this->createUserFromData($userData);
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$userData) {
            return null;
        }

        return $this->createUserFromData($userData);
    }

    public function save(User $user): void
    {
        if ($user->getId()) {
            $this->update($user);
        } else {
            $this->insert($user);
        }
    }

    private function insert(User $user): void
    {
        $stmt = $this->db->prepare("INSERT INTO users (username, email, password, role, is_active) VALUES (:username, :email, :password, :role, :is_active)");
        $stmt->execute([
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
            'role' => $user->getRole(),
            'is_active' => $user->isActive()
        ]);
    }

    private function update(User $user): void
    {
        $stmt = $this->db->prepare("UPDATE users SET username = :username, email = :email, role = :role, is_active = :is_active, updated_at = CURRENT_TIMESTAMP WHERE id = :id");
        $stmt->execute([
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'role' => $user->getRole(),
            'is_active' => $user->isActive()
        ]);
    }

    private function createUserFromData(array $userData): User
    {
        $user = new User($userData['username'], $userData['email'], $userData['password'], $userData['role']);
        $user->setIsActive($userData['is_active']);
        // Set other properties...
        return $user;
    }
}

