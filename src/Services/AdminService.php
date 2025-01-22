<?php

namespace Youdemy\Services;

use Youdemy\Config\Database;
use Youdemy\Models\Entity\User;
use Youdemy\Models\Entity\Course;
use Youdemy\Repository\UserRepository;
use Youdemy\Repository\CourseRepository;
use Youdemy\Repository\EnrollmentRepository;
use Youdemy\Repository\ReviewRepository;
use PDOException;
use PDO;
use DateTime;

class AdminService {
    private UserRepository $userRepository;
    private CourseRepository $courseRepository;
    private EnrollmentRepository $enrollmentRepository;
    private ReviewRepository $reviewRepository;
    private AuthService $authService;
    private database $db;

    public function __construct(
        UserRepository $userRepository,
        CourseRepository $courseRepository,
        EnrollmentRepository $enrollmentRepository,
        ReviewRepository $reviewRepository,
        AuthService $authService,
        Database $database
    ) {
        $this->userRepository = $userRepository;
        $this->courseRepository = $courseRepository;
        $this->enrollmentRepository = $enrollmentRepository;
        $this->reviewRepository = $reviewRepository;
        $this->authService = $authService;
        $this->db = $database;
    }

    // User Management
    public function getAllUsers(int $page = 1, int $perPage = 10, ?string $role = null): array {
        try {
            return $this->userRepository->getAllUsers($page, $perPage, $role);
        } catch (PDOException $e) {
            throw new \RuntimeException('Failed to fetch users: ' . $e->getMessage());
        }
    }

    public function updateUserRole(int $userId, string $newRole): void {
        if (!$this->authService->isAdmin()) {
            throw new \RuntimeException('Unauthorized: Only admins can update user roles');
        }

        try {
            $user = $this->userRepository->findById($userId);
            if (!$user) {
                throw new \RuntimeException('User not found');
            }

            $user->setRole($newRole);
            $this->userRepository->save($user);
        } catch (PDOException $e) {
            throw new \RuntimeException('Failed to update user role: ' . $e->getMessage());
        }
    }

    public function deactivateUser(int $userId): void {
        if (!$this->authService->isAdmin()) {
            throw new \RuntimeException('Unauthorized: Only admins can deactivate users');
        }

        try {
            $user = $this->userRepository->findById($userId);
            if (!$user) {
                throw new \RuntimeException('User not found');
            }

            $user->setIsActive(false);
            $this->userRepository->save($user);
        } catch (PDOException $e) {
            throw new \RuntimeException('Failed to deactivate user: ' . $e->getMessage());
        }
    }

    public function toggleUserStatus(int $userId): void {
        if (!$this->authService->isAdmin()) {
            throw new \RuntimeException('Unauthorized: Only admins can toggle user status');
        }

        try {
            $user = $this->userRepository->findById($userId);
            if (!$user) {
                throw new \RuntimeException('User not found');
            }

            // Don't allow deactivating the last admin
            if ($user->getRole() === 'admin' && !$user->isActive()) {
                $activeAdmins = $this->userRepository->countActiveAdmins();
                if ($activeAdmins <= 1) {
                    throw new \RuntimeException('Cannot deactivate the last active admin');
                }
            }

            // Toggle the status
            $user->setIsActive(!$user->isActive());
            $user->setUpdatedAt(new DateTime());
            
            // Add a note about who made the change
            $adminUser = $this->authService->getCurrentUser();
            $actionType = $user->isActive() ? 'activated' : 'deactivated';
            $user->addStatusChangeLog(sprintf(
                'User %s by admin %s on %s',
                $actionType,
                $adminUser->getName(),
                (new DateTime())->format('Y-m-d H:i:s')
            ));

            $this->userRepository->save($user);

            // Log the action
            $this->logAdminAction(
                'user_status_change',
                sprintf('User ID %d %s by admin ID %d', $userId, $actionType, $adminUser->getId()),
                $userId
            );
        } catch (PDOException $e) {
            throw new \RuntimeException('Failed to toggle user status: ' . $e->getMessage());
        }
    }

    public function validateTeacher(int $userId): void {
        if (!$this->authService->isAdmin()) {
            throw new \RuntimeException('Unauthorized: Only admins can validate teachers');
        }

        try {
            $user = $this->userRepository->findById($userId);
            if (!$user) {
                throw new \RuntimeException('User not found');
            }

            // Check if user is already a validated teacher
            if ($user->getRole() === 'teacher' && $user->isValidated()) {
                throw new \RuntimeException('Teacher is already validated');
            }

            // Update user role to teacher if not already
            $user->setRole('teacher');
            
            // Set validation status and timestamp
            $user->setIsValidated(true);
            $user->setValidatedAt(new DateTime());
            $user->setUpdatedAt(new DateTime());

            // Add validation note
            $adminUser = $this->authService->getCurrentUser();
            $user->addValidationLog(sprintf(
                'Teacher validated by admin %s on %s',
                $adminUser->getName(),
                (new DateTime())->format('Y-m-d H:i:s')
            ));

            // Save the changes
            $this->userRepository->save($user);

            // Send notification email to teacher
            $this->sendTeacherValidationEmail($user);

            // Log the admin action
            $this->logAdminAction(
                'teacher_validation',
                sprintf('Teacher ID %d validated by admin ID %d', $userId, $adminUser->getId()),
                $userId
            );
        } catch (PDOException $e) {
            throw new \RuntimeException('Failed to validate teacher: ' . $e->getMessage());
        }
    }

    private function sendTeacherValidationEmail(User $user): void {
        try {
            $to = $user->getEmail();
            $subject = 'Your Teacher Account has been Validated';
            $message = sprintf(
                "Dear %s,\n\n" .
                "Congratulations! Your teacher account on Youdemy has been validated. " .
                "You can now start creating and publishing courses.\n\n" .
                "Get started by visiting your teacher dashboard at:\n" .
                "%s\n\n" .
                "Best regards,\n" .
                "The Youdemy Team",
                $user->getName(),
                'https://youdemy.com/teacher/dashboard'
            );
            
            $headers = [
                'From' => 'noreply@youdemy.com',
                'Content-Type' => 'text/plain; charset=utf-8'
            ];

            mail($to, $subject, $message, $headers);
        } catch (\Exception $e) {
            // Log email sending failure but don't break the main operation
            error_log('Failed to send teacher validation email: ' . $e->getMessage());
        }
    }

    private function logAdminAction(string $action, string $description, int $targetId): void {
        try {
            $adminUser = $this->authService->getCurrentUser();
            $query = "INSERT INTO admin_logs (admin_id, action, description, target_id, created_at) 
                     VALUES (:admin_id, :action, :description, :target_id, :created_at)";
            
            $this->db->query($query, [
                'admin_id' => $adminUser->getId(),
                'action' => $action,
                'description' => $description,
                'target_id' => $targetId,
                'created_at' => (new DateTime())->format('Y-m-d H:i:s')
            ]);
        } catch (PDOException $e) {
            // Log failure shouldn't break the main operation
            error_log('Failed to log admin action: ' . $e->getMessage());
        }
    }

    // Course Management
    public function approveCourse(int $courseId): void {
        if (!$this->authService->isAdmin()) {
            throw new \RuntimeException('Unauthorized: Only admins can approve courses');
        }

        try {
            $course = $this->courseRepository->findById($courseId);
            if (!$course) {
                throw new \RuntimeException('Course not found');
            }

            $course->setIsPublished(true);
            $this->courseRepository->save($course);
        } catch (PDOException $e) {
            throw new \RuntimeException('Failed to approve course: ' . $e->getMessage());
        }
    }

    public function getPendingCourses(): array {
        try {
            return $this->courseRepository->findByStatus(false);
        } catch (PDOException $e) {
            throw new \RuntimeException('Failed to fetch pending courses: ' . $e->getMessage());
        }
    }

    public function removeCourse(int $courseId): void {
        if (!$this->authService->isAdmin()) {
            throw new \RuntimeException('Unauthorized: Only admins can remove courses');
        }

        try {
            // First remove all enrollments and reviews
            $this->enrollmentRepository->deleteByCourseId($courseId);
            $this->reviewRepository->deleteByCourseId($courseId);
            
            // Then remove the course
            $this->courseRepository->delete($courseId);
        } catch (PDOException $e) {
            throw new \RuntimeException('Failed to remove course: ' . $e->getMessage());
        }
    }
    public function getAllCourses(): array
    {
        try {
            $db = Database::getInstance()->getConnection();
            
            $sql = "SELECT 
                    c.id,
                    c.title,
                    c.description,
                    c.created_at,
                    u.username as teacher_name,
                    cat.name as category_name
                    FROM courses c
                    LEFT JOIN users u ON c.teacher_id = u.id
                    LEFT JOIN categories cat ON c.category_id = cat.id
                    ORDER BY c.created_at DESC";
                    
            $stmt = $db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            throw new \RuntimeException('Failed to fetch courses: ' . $e->getMessage());
        }
    }
    
    // Statistics and Reports
    public function getDashboardStats(): array {
        try {
            return [
                'total_users' => $this->userRepository->getTotalCount(),
                'total_courses' => $this->courseRepository->getTotalCourses(),
                'total_enrollments' => $this->enrollmentRepository->getTotalCount(),
                'total_reviews' => $this->reviewRepository->count(),
                'average_course_rating' => $this->reviewRepository->getOverallAverageRating(),
            ];
        } catch (PDOException $e) {
            throw new \RuntimeException('Failed to fetch dashboard stats: ' . $e->getMessage());
        }
    }

    public function getRevenueReport(DateTime $startDate, DateTime $endDate): array {
        try {
            return $this->courseRepository->getRevenueByDateRange($startDate, $endDate);
        } catch (PDOException $e) {
            throw new \RuntimeException('Failed to generate revenue report: ' . $e->getMessage());
        }
    }

    public function getUserActivityReport(DateTime $startDate, DateTime $endDate): array {
        try {
            return [
                'new_users' => $this->userRepository->getNewUsersByDateRange($startDate, $endDate),
                'new_enrollments' => $this->enrollmentRepository->getNewEnrollmentsByDateRange($startDate, $endDate),
                'new_reviews' => $this->reviewRepository->getNewReviewsByDateRange($startDate, $endDate)
            ];
        } catch (PDOException $e) {
            throw new \RuntimeException('Failed to generate user activity report: ' . $e->getMessage());
        }
    }

    // System Maintenance
    public function cleanupInactiveUsers(int $daysInactive): void {
        if (!$this->authService->isAdmin()) {
            throw new \RuntimeException('Unauthorized: Only admins can perform system maintenance');
        }

        try {
            $cutoffDate = new DateTime("-{$daysInactive} days");
            $this->userRepository->deleteInactiveUsers($cutoffDate);
        } catch (PDOException $e) {
            throw new \RuntimeException('Failed to cleanup inactive users: ' . $e->getMessage());
        }
    }

    public function backupDatabase(): string {
        if (!$this->authService->isAdmin()) {
            throw new \RuntimeException('Unauthorized: Only admins can perform database backups');
        }

        try {
            $backupDir = __DIR__ . '/../../../backups';
            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            $filename = $backupDir . '/backup_' . date('Y-m-d_H-i-s') . '.sql';
            // Implementation would depend on your database system
            // Here you would typically use mysqldump or similar
            
            return $filename;
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to backup database: ' . $e->getMessage());
        }
    }


    private function calculateMonthlyGrowthRate(): float {
        $currentMonth = $this->enrollmentRepository->calculateRevenueSince(
            (new DateTime())->modify('first day of this month')
        );
        $lastMonth = $this->enrollmentRepository->calculateRevenueSince(
            (new DateTime())->modify('first day of last month'),
            (new DateTime())->modify('last day of last month')
        );
        
        if ($lastMonth == 0) {
            return 0.0;
        }
        
        return (($currentMonth - $lastMonth) / $lastMonth) * 100;
    }

    private function calculateTeacherRetentionRate(): float {
        $activeLastMonth = $this->userRepository->countActiveTeachersSince(
            (new DateTime())->modify('-2 months'),
            (new DateTime())->modify('-1 month')
        );
        $stillActiveThisMonth = $this->userRepository->countRetainedTeachersSince(
            (new DateTime())->modify('-1 month')
        );
        
        if ($activeLastMonth == 0) {
            return 0.0;
        }
        
        return ($stillActiveThisMonth / $activeLastMonth) * 100;
    }
    public function getGlobalStats(): array 
{
    try {
        $db = $this->db->getConnection();
        
        // Basic statistics queries
        $totalUsers = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
        $totalTeachers = $db->query("SELECT COUNT(*) FROM users WHERE role = 'teacher'")->fetchColumn();
        $totalStudents = $db->query("SELECT COUNT(*) FROM users WHERE role = 'student'")->fetchColumn();
        $totalCourses = $db->query("SELECT COUNT(*) FROM courses")->fetchColumn();
        
        // Get enrollments count
        $totalEnrollments = $db->query("SELECT COUNT(*) FROM enrollments")->fetchColumn();
        
        // Get this month's statistics
        $firstDayOfMonth = date('Y-m-01');
        $newUsersThisMonth = $db->query("SELECT COUNT(*) FROM users WHERE created_at >= '$firstDayOfMonth'")->fetchColumn();
        $newCoursesThisMonth = $db->query("SELECT COUNT(*) FROM courses WHERE created_at >= '$firstDayOfMonth'")->fetchColumn();
        
        return [
            'users' => [
                'total' => $totalUsers,
                'teachers' => $totalTeachers,
                'students' => $totalStudents,
                'new_this_month' => $newUsersThisMonth
            ],
            'courses' => [
                'total' => $totalCourses,
                'new_this_month' => $newCoursesThisMonth
            ],
            'enrollments' => [
                'total' => $totalEnrollments
            ],
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
    } catch (PDOException $e) {
        throw new \RuntimeException('Failed to fetch global stats: ' . $e->getMessage());
    }
}


    private function calculateStudentRetentionRate(): float {
        $activeLastMonth = $this->userRepository->countActiveStudentsSince(
            (new DateTime())->modify('-2 months')
        );
        $stillActiveThisMonth = $this->userRepository->countActiveStudentsSince(
            (new DateTime())->modify('-1 month')
        );
        
        if ($activeLastMonth == 0) {
            return 0.0;
        }
        
        return ($stillActiveThisMonth / $activeLastMonth) * 100;
    }
}