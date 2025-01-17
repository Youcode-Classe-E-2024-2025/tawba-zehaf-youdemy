<?php
namespace Youdemy\Controllers;

use Youdemy\Models\Entity\User;

class AuthController
{
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? 'student';

            try {
                $user = new User($username, $email, $password, $role);
                // TODO: Save user to database
                $_SESSION['success'] = 'Inscription réussie. Veuillez vous connecter.';
                header('Location: /login');
                exit;
            } catch (\InvalidArgumentException $e) {
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
                // TODO: Implement login logic with database
                $_SESSION['user_id'] = 1; // Temporary
                $_SESSION['success'] = 'Connexion réussie';
                header('Location: /dashboard');
                exit;
            } catch (\Exception $e) {
                $_SESSION['error'] = 'Email ou mot de passe incorrect';
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