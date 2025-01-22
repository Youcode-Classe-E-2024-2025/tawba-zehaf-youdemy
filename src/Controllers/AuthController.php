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
        // require_once __DIR__ . '/../Views/auth/register.php';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $role = $_POST['role'] ?? 'student';

            try {
                if ($this->authService->register($username, $email, $password, $role)) {
                    $_SESSION['success'] = 'Registration successful! Please login.';
                    // Redirect to login page on success
                    header('Location: /login');
                    exit();
                }
            } catch (RuntimeException $e) {
                $_SESSION['error'] = $e->getMessage();
            }
        }

        require_once __DIR__ . '/../Views/auth/register.php';
    }
    public function login()
    {
        echo "Form submitted data: ";
        echo "<pre>";
        print_r($_POST);
        echo "</pre>";
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            echo "POST method detected";

            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';

            try {
                $user = $this->authService->login($email, $password);

                if ($user) {
                    $_SESSION['success'] = 'Login successful!';

                    $_SESSION['user_id'] = $user->getId();
                    $_SESSION['username'] = $user->getUsername();
                    $_SESSION['user_role'] = $user->getRole();
                        // Force immediate redirect with exit
                        echo "User role: " . $user->getRole(); // Debug line
                
                        // Clear any output buffers
                        ob_clean();
                        
        
                        switch($user->getRole()) {
                            case 'admin':
                                header('Location: /admin/dashboard');
                                exit();
                            case 'teacher':
                                header('Location: /teacher/dashboard');
                                exit();
                            case 'student':
                                header('Location: /student/dashboard');
                                exit();
                        }
            }
            $_SESSION['error'] = 'Invalid credentials';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Login error: ' . $e->getMessage();
        }
    }

                    // Role-based redirection
        //             switch ($user->getRole()) {
        //                 case 'admin':
        //                     header('Location: /admin/dashboard');
        //                     break;
        //                 case 'teacher':
        //                     header('Location: /teacher/dashboard');
        //                     break;
        //                 case 'student':
        //                     header('Location: /student/dashboard');
        //                     break;
        //             }
        //             exit();
        //         } else {
        //             $_SESSION['error'] = 'Invalid email or password';
        //         }
        //     } catch (\Exception $e) {
        //         $_SESSION['error'] = 'Login failed: ' . $e->getMessage();
        //     }
        // }

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