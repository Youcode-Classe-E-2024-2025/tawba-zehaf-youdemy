<?php

namespace Youdemy\Repository;

use Youdemy\Config\Database;
use Youdemy\Models\Entity\User;
use PDOException;
use DateTime;

class UserRepository {
    private Database $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function save(User $user): void {
        try {
            if ($user->getId()) {
                $this->update($user);
            } else {
                $this->create($user);
            }
        } catch (PDOException $e) {
            throw new \RuntimeException('Failed to save user: ' . $e->getMessage());
        }
    }

    public function findById(int $id): ?User {
        $query = "SELECT * FROM users WHERE id = :id";
        $result = $this->db->query($query, ['id' => $id])->fetch();

        if (!$result) {
            return null;
        }

        return $this->hydrateUser($result);
    }

    public function findByEmail(string $email): ?User {
        $query = "SELECT * FROM users WHERE email = :email";
        $result = $this->db->query($query, ['email' => $email])->fetch();

        if (!$result) {
            return null;
        }

        return $this->hydrateUser($result);
    }

    public function findTeachers(): array {
        $query = "SELECT * FROM users WHERE role = 'teacher' ORDER BY name";
        $results = $this->db->query($query)->fetchAll();
        return array_map([$this, 'hydrateUser'], $results);
    }

    public function findStudents(): array {
        $query = "SELECT * FROM users WHERE role = 'student' ORDER BY name";
        $results = $this->db->query($query)->fetchAll();
        return array_map([$this, 'hydrateUser'], $results);
    }

    private function create(User $user): void {
        $now = new DateTime();
        $params = [
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
            'role' => $user->getRole(),
            'created_at' => $now->format('Y-m-d H:i:s'),
            'updated_at' => $now->format('Y-m-d H:i:s')
        ];

        $query = "INSERT INTO users (name, email, password, role, created_at, updated_at) 
                 VALUES (:name, :email, :password, :role, :created_at, :updated_at)";
        
        $this->db->query($query, $params);
        $user->setId($this->db->lastInsertId());
        $user->setCreatedAt($now);
        $user->setUpdatedAt($now);
    }

    private function update(User $user): void {
        $now = new DateTime();
        $params = [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'role' => $user->getRole(),
            'updated_at' => $now->format('Y-m-d H:i:s')
        ];

        // Only include password in update if it's been changed
        if ($user->getPassword()) {
            $params['password'] = $user->getPassword();
            $passwordSet = ", password = :password";
        } else {
            $passwordSet = "";
        }

        $query = "UPDATE users 
                 SET name = :name,
                     email = :email,
                     role = :role,
                     updated_at = :updated_at" . 
                     $passwordSet . 
                 " WHERE id = :id";

        $result = $this->db->query($query, $params)->rowCount();
        
        if ($result === 0) {
            throw new \RuntimeException('User not found or no changes made');
        }
        
        $user->setUpdatedAt($now);
    }

    private function hydrateUser(array $data): User {
        $user = new User($data['name'], $data['email'], $data['role']);
        $user->setId($data['id']);
        $user->setName($data['name']);
        $user->setEmail($data['email']);
        $user->setRole($data['role']);
        
        if (isset($data['password'])) {
            $user->setPassword($data['password']);
        }
        
        if (isset($data['created_at'])) {
            $user->setCreatedAt(new DateTime($data['created_at']));
        }
        
        if (isset($data['updated_at'])) {
            $user->setUpdatedAt(new DateTime($data['updated_at']));
        }
        
        return $user;}
    


    // public function create($user) {
    //     $query = "INSERT INTO users (name, email, password, role, created_at, updated_at) VALUES (:name, :email, :password, :role, :created_at, :updated_at)";
    //     $this->db->query($query, $user);
    //     return $this->db->lastInsertId();
    // }

}