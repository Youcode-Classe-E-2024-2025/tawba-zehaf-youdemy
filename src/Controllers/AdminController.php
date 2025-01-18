<?php

namespace Youdemy\Controllers;

use Youdemy\Services\AdminService;
use Youdemy\Services\AuthService;

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
        $this->checkAdminAccess();
        $stats = $this->adminService->getGlobalStats();
        $this->render('admin/dashboard.php', ['stats' => $stats]);
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