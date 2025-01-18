<!-- class AuthService {
private $db;
private $userRepository;

public function __construct() {
$this->db = new Database();
$this->userRepository = new UserRepository($this->db);
}

public function login($email, $password) {
$user = $this->userRepository->findByEmail($email);

if (!$user || !password_verify($password, $user->getPassword())) {
throw new Exception("Invalid email or password");
}

$_SESSION['user_id'] = $user->getId();

$_SESSION['user_role'] = $user->getRole();

return $user;
}

public function register($name, $email, $password, $role = 'student') {
if (empty($name) || empty($email) || empty($password)) {
throw new Exception("All fields are required");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
throw new Exception("Invalid email format");
}

if (strlen($password) < 8) { throw new Exception("Password must be at least 8 characters long"); } $existingUser=$this->
    userRepository->findByEmail($email);
    if ($existingUser) {
    throw new Exception("Email already in use");
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $user = [
    'name' => $name,
    'email' => $email,
    'password' => $hashedPassword,
    'role' => $role,
    'created_at' => date('Y-m-d H:i:s'),
    'updated_at' => date('Y-m-d H:i:s')
    ];

    $userId = $this->userRepository->create($user);

    return $this->userRepository->findById($userId);
    }

    public function logout() {
    session_unset();
    session_destroy();
    }

    public function getCurrentUser() {
    if (!isset($_SESSION['user_id'])) {
    return null;
    }

    return $this->userRepository->findById($_SESSION['user_id']);
    }

    public function isLoggedIn() {
    return isset($_SESSION['user_id']);
    }

    public function requireLogin() {
    if (!$this->isLoggedIn()) {
    header('Location: /login');
    exit;
    }
    }

    public function requireRole($role) {
    $this->requireLogin();

    if ($_SESSION['user_role'] !== $role) {
    header('Location: /403');
    exit;
    }
    }

    } -->
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