<?php
namespace Youdemy\Controllers;
use Youdemy\Models\Entity\User;
use Youdemy\Services\AuthService;
use Youdemy\Repository\UserRepository;
use Youdemy\Config\Database;
use RuntimeException;

class AuthController
{
    private AuthService $authService;
    private UserRepository $userRepo;

    public function __construct() 
    {
        $db = Database::getInstance();
        $userRepository = new UserRepository($db);
        $this->userRepo = $userRepository;
        $this->authService = new AuthService($userRepository);
    }
    public function register()
    {
        require_once __DIR__ . '/../Views/auth/register.php';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $role = $_POST['role'] ?? 'student';
            
            try {
                if ($this->authService->register($username, $email, $password, $role)) {
                    // Redirect to login page on success
                    header('Location: /login');
                    exit();
            } 
        }catch (RuntimeException $e) {
                $_SESSION['error'] = $e->getMessage();
            }
        }
        
        require_once __DIR__ . '/../Views/auth/register.php';
        
    
    }
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
    
            try {
                $user = $this->authService->login($email, $password);
                
                if ($user) {
                    $_SESSION['user_id'] = $user->getId();
                    $_SESSION['username'] = $user->getUsername();
                    $_SESSION['user_role'] = $user->getRole();
                    
                    // Role-based redirection
                    switch($user->getRole()) {
                        case 'admin':
                            header('Location: /admin/dashboard');
                            break;
                        case 'teacher':
                            header('Location: /teacher/dashboard');
                            break;
                        case 'student':
                            header('Location: /student/dashboard');
                            break;
                    }
                    exit;
                }
            } catch (\Exception $e) {
                $_SESSION['error'] = 'Login failed: ' . $e->getMessage();
            }
        }
        
        require_once __DIR__ . '/../Views/auth/login.php';
    }
    

    public function logout()
    {
        session_start();
        session_destroy();
        header('Location: /');
        exit;
    }
}