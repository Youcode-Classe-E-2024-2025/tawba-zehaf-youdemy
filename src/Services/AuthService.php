<?php

namespace Youdemy\Services;

use Youdemy\Config\Database;
use Youdemy\Models\Entity\User;
use Youdemy\Repository\UserRepository;
use PDOException;

class AuthService {
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository) {
        $this->userRepository = $userRepository;
    }

    public function login(string $email, string $password): ?User {
        try {
            $user = $this->userRepository->findByEmail($email);
            
            if (!$user || !password_verify($password, $user->getPassword())) {
                return null;
            }

            $this->startSession($user);
            return $user;
        } catch (PDOException $e) {
            throw new \RuntimeException('Authentication failed: ' . $e->getMessage());
        }
    }

    public function register(string $name, string $email, string $password, string $role = 'student'): User {
        try {
            // Check if user already exists
            if ($this->userRepository->findByEmail($email)) {
                throw new \RuntimeException('Email already registered');
            }

            // Create new user
            $user = new User($name, $email, $role);
            $user->setPassword(password_hash($password, PASSWORD_DEFAULT));
            
            $this->userRepository->save($user);
            $this->startSession($user);
            
            return $user;
        } catch (PDOException $e) {
            throw new \RuntimeException('Registration failed: ' . $e->getMessage());
        }
    }

    public function logout(): void {
        session_start();
        session_destroy();
    }

    public function getCurrentUser(): ?User {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }

        try {
            return $this->userRepository->findById($_SESSION['user_id']);
        } catch (PDOException $e) {
            throw new \RuntimeException('Failed to get current user: ' . $e->getMessage());
        }
    }

    private function startSession(User $user): void {
        session_start();
        $_SESSION['user_id'] = $user->getId();
        $_SESSION['user_role'] = $user->getRole();
    }

    public function isAuthenticated(): bool {
        return isset($_SESSION['user_id']);
    }

    public function isTeacher(): bool {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'teacher';
    }

    public function isAdmin(): bool {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }
}