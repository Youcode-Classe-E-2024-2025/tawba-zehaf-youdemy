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
        // if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        //     $username = $_POST['username'] ?? '';
        //     $email = $_POST['email'] ?? '';
        //     $password = $_POST['password'] ?? '';
        //     $role = $_POST['role'] ?? 'student';

        //     try {
        //         $user = new User($username, $email, $password, $role);
        //         // TODO: Save user to database
        //         $_SESSION['success'] = 'Inscription réussie. Veuillez vous connecter.';
        //         // header('Location: /login');
        //         exit;
        //     } catch (\InvalidArgumentException $e) {
        //         $_SESSION['error'] = $e->getMessage();
        //     }
        // }
        
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
        // if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        //     $email = $_POST['email'] ?? '';
        //     $password = $_POST['password'] ?? '';

        //     try {
        //         // TODO: Implement login logic with database
        //         $_SESSION['user_id'] = 1; // Temporary
        //         $_SESSION['success'] = 'Connexion réussie';
        //         header('Location: /course_catalog');
        //         exit;
        //     } catch (\Exception $e) {
        //         $_SESSION['error'] = 'Email ou mot de passe incorrect';
        //     }
        // }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
    
            try {
                $user = $this->authService->login($email, $password);
                
                if ($user) {
                    $_SESSION['username'] = $user->getId();
                    $_SESSION['role'] = $user->getRole();
                    $_SESSION['success'] = 'Login successful';
                    if ($user->getRole() === 'teacher') {
                        header('Location: /teacher/dashboard');
                    } else if ($user->getRole() === 'student') {
                        header('Location: /student/dashboard');
                    } else if ($user->getRole() === 'admin') {
                        header('Location: /admin/dashboard');
                        
                    } else {
                        header('Locaation: /404_view');
                    }
                    exit;
                } else {
                    $_SESSION['error'] = 'Invalid email or password';
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