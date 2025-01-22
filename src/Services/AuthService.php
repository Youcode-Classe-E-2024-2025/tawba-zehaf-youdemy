<?php

namespace Youdemy\Services;

use Youdemy\Config\Database;
use Youdemy\Models\Entity\User;
use Youdemy\Repository\UserRepository;
use PDOException;

class AuthService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function login(string $email, string $password): ?User
{
    try {
        $db = Database::getInstance()->getConnection();
        
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$email]);
        
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            $userObj = new User(
                $user['username'],
                $user['email'],
                $user['password'],
                $user['role']
            );
            $userObj->setId($user['id']);
            return $userObj;
        }
        $_SESSION['error'] = 'Invalid email or password';
        return null;
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Login failed: ' . $e->getMessage();
        throw new \RuntimeException('Login failed: ' . $e->getMessage());
    }
}

public function register(string $username, string $email, string $password, string $role = 'student')
{
    $db = Database::getInstance()->getConnection();
    

    
    // Check if email already exists
    $checkEmail = $db->prepare("SELECT id FROM users WHERE email = ?");
    $checkEmail->execute([$email]);
    if ($checkEmail->rowCount() > 0) {
        $_SESSION['error'] = 'Email already registered. Please use another email.';
        return false;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    $query = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";
    $stmt = $db->prepare($query);
    return $stmt->execute([$username, $email, $hashedPassword, $role]);
}

//     public function register(string $username, string $email, string $password, string $role = 'student')
// {
//         // Debug input parameters
//         try {
//             $db = Database::getInstance()->getConnection();
//             $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
//             $sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";
//             $stmt = $db->prepare($sql);
            
//             return $stmt->execute([$username, $email, $hashedPassword, $role]);
            
//         } catch (PDOException $e) {
//             throw new \RuntimeException('Registration failed: ' . $e->getMessage());
//         }
//     }


    public function getCurrentUser(): ?User
    {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }

        try {
            return $this->userRepository->findById($_SESSION['user_id']);
        } catch (PDOException $e) {
            throw new \RuntimeException('Failed to get current user: ' . $e->getMessage());
        }
    }

    private function startSession(User $user): void
    {
        session_start();
        $_SESSION['user_id'] = $user->getId();
        $_SESSION['user_role'] = $user->getRole();
    }

    public function isAuthenticated(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public function isTeacher(): bool
    {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'teacher';
    }

    public function isAdmin(): bool
    {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }
    public function handlePostRequest(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate CSRF token
            if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                die('Invalid CSRF token');
            }

            // Sanitize and validate input
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $password = $_POST['password']; // You can also sanitize this if needed

            // Fetch user from database
            $user = $this->userRepository->findByEmail($email);

            if ($user && password_verify($password, $user->getPassword())) {
                // Password is correct, log the user in
                $_SESSION['user_id'] = $user->getId();
                header('Location: /dashboard'); // Redirect to dashboard
                exit;
            } else {
                // Invalid credentials
                $data['error'] = 'Invalid email or password.';
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate CSRF token
            if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                die('Invalid CSRF token');
            }

            // Sanitize and validate input
            $username = htmlspecialchars(trim($_POST['username']));
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];

            // Check if passwords match
            if ($password !== $confirm_password) {
                $data['error'] = 'Passwords do not match.';
            } else {
                // Hash the password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // Proceed with saving the user data
                $user = new User($username, $email, $hashedPassword, $_POST['role']);
                $user->setPassword($hashedPassword);
                $this->userRepository->save($user);
                header('Location: /login'); 
                exit;
            }
        }
    }
    public function logout(): void
    {
        session_start();
        session_destroy();
    }
}