<?php

class AuthService {
    private $db;
    private $userRepository;

    public function __construct() {
        $this->db = new Database();
        $this->userRepository = new UserRepository($this->db);
    }

    public function login($email, $password) {
        $user = $this->userRepository->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            throw new Exception("Invalid email or password");
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];

        return $user;
    }

    public function register($name, $email, $password, $role = 'student') {
        if (empty($name) || empty($email) || empty($password)) {
            throw new Exception("All fields are required");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }

        if (strlen($password) < 8) {
            throw new Exception("Password must be at least 8 characters long");
        }

        $existingUser = $this->userRepository->findByEmail($email);
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
}