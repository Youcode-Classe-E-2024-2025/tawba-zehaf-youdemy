<?php
require_once 'src/Models/Entity/User.php';
class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? 'student';

            if ($this->userModel->createUser($username, $email, $password, $role)) {
                $_SESSION['success'] = 'Registration successful. You can now log in.';
                $this->redirect('/login');
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }

        $this->render('auth/register.php', ['title' => 'Register']);
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            $user = $this->userModel->getUserByEmail($email);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['role'];
                $this->redirect('/dashboard');
            } else {
                $error = 'Invalid email or password.';
            }
        }

        $this->render('auth/login.php', ['title' => 'Login']);
    }

    public function logout()
    {
        session_destroy();
        $this->redirect('/');
    }

    private function redirect($url)
    {
        header("Location: $url");
        exit;
    }

    private function render($view, $data = [])
    {
        extract($data);
        require VIEW_PATH . $view;
    }
}