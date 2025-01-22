<?php

namespace Youdemy\Controllers;
use Pdo;
use Youdemy\Services\AdminService;
use Youdemy\Services\AuthService;
use Youdemy\Config\Database;

class AdminController 
{
    private AdminService $adminService;
    private AuthService $authService;

    public function __construct(AdminService $adminService, AuthService $authService)
    {
        $this->adminService = $adminService;
        $this->authService = $authService;
    }

    public function dashboard()
    {
        $stats = $this->adminService->getGlobalStats();
        
        $this->render('admin/dashboard.php', [
            'stats' => $stats,
            'adminService' => $this->adminService
        ]);
    }
    

    public function users()
    {
        $this->checkAdminAccess();
        $users = $this->adminService->getAllUsers();
        $this->render('admin/users.php', ['users' => $users]);
    }

    public function courses()
    {
        $this->checkAdminAccess();
        $courses = $this->adminService->getAllCourses();
        $this->render('admin/courses.php', ['courses' => $courses]);
    }

    public function validateTeacher(int $userId)
    {
        $this->checkAdminAccess();
        $this->adminService->validateTeacher($userId);
        $this->redirect('/admin/users');
    }

    public function toggleUserStatus(int $userId)
    {
        $this->checkAdminAccess();
        $this->adminService->toggleUserStatus($userId);
        $this->redirect('/admin/users');
    }

    private function render(string $view, array $data = [])
    {
        extract($data);
        include __DIR__ . '/../Views/' . $view;
    }

    private function redirect(string $url)
    {
        header('Location: ' . $url);
        exit;
    }
    public function deleteUser($userId) 
    {
        $db = Database::getInstance()->getConnection();
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$userId]);
        header('Location: /admin/dashboard');
    }
    
    public function editUser($userId)
    {
        $db = Database::getInstance()->getConnection();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sql = "UPDATE users SET username = ?, email = ?, role = ? WHERE id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                $_POST['username'],
                $_POST['email'], 
                $_POST['role'],
                $userId
            ]);
            header('Location: /admin/dashboard');
            exit();
        }
        
        // Get user data for edit form
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $this->render('admin/edit_user.php', ['user' => $user]);
    }
    
    public function deleteCourse($courseId) 
{
    $db = Database::getInstance()->getConnection();
    $sql = "DELETE FROM courses WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$courseId]);
    header('Location: /admin/dashboard');
    exit();
}

public function editCourse($courseId) 
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $db = Database::getInstance()->getConnection();
        $sql = "UPDATE courses SET title = ?, description = ?, category_id = ? WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            $_POST['title'],
            $_POST['description'],
            $_POST['category_id'],
            $courseId
        ]);
        header('Location: /admin/dashboard');
        exit();
    }
}

    private function checkAdminAccess()
    {
        $currentUser = $this->authService->getCurrentUser();
        if (!$currentUser || $currentUser->getRole() !== 'admin') {
            $this->forbidden();
        }
    }

    private function forbidden()
    {
        header('HTTP/1.0 403 Forbidden');
        echo 'You are forbidden from accessing this page.';
        exit;
    }
}