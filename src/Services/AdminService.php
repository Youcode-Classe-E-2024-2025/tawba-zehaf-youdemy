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
use DateTime;

class AdminService {
    private UserRepository $userRepository;
    private CourseRepository $courseRepository;
    private EnrollmentRepository $enrollmentRepository;
    private ReviewRepository $reviewRepository;
    private AuthService $authService;
    private Database $db;

    public function __construct(
        UserRepository $userRepository,
        CourseRepository $courseRepository,
        EnrollmentRepository $enrollmentRepository,
        ReviewRepository $reviewRepository,
        AuthService $authService,
        Database $db
    ) {
        $this->userRepository = $userRepository;
        $this->courseRepository = $courseRepository;
        $this->enrollmentRepository = $enrollmentRepository;
        $this->reviewRepository = $reviewRepository;
        $this->authService = $authService;
        $this->db = $db;
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

    public function getAllCourses(array $filters = [], int $page = 1, int $limit = 10): array {
        if (!$this->authService->isAdmin()) {
            throw new \RuntimeException('Unauthorized: Only admins can access all courses');
        }

        try {
            // Calculate offset for pagination
            $offset = ($page - 1) * $limit;

            // Build filter conditions
            $conditions = [];
            $params = [];

            if (isset($filters['status'])) {
                $conditions[] = 'c.is_published = :status';
                $params['status'] = $filters['status'] === 'published' ? 1 : 0;
            }

            if (isset($filters['category_id'])) {
                $conditions[] = 'c.category_id = :category_id';
                $params['category_id'] = $filters['category_id'];
            }

            if (isset($filters['teacher_id'])) {
                $conditions[] = 'c.teacher_id = :teacher_id';
                $params['teacher_id'] = $filters['teacher_id'];
            }

            if (isset($filters['min_rating'])) {
                $conditions[] = '(SELECT AVG(rating) FROM reviews r WHERE r.course_id = c.id) >= :min_rating';
                $params['min_rating'] = $filters['min_rating'];
            }

            if (isset($filters['search'])) {
                $conditions[] = '(c.title LIKE :search OR c.description LIKE :search)';
                $params['search'] = '%' . $filters['search'] . '%';
            }

            // Build the query
            $query = "SELECT 
                        c.*,
                        u.name as teacher_name,
                        cat.name as category_name,
                        (SELECT COUNT(*) FROM enrollments e WHERE e.course_id = c.id) as enrollment_count,
                        (SELECT AVG(rating) FROM reviews r WHERE r.course_id = c.id) as average_rating,
                        (SELECT COUNT(*) FROM reviews r WHERE r.course_id = c.id) as review_count,
                        GROUP_CONCAT(t.name) as tags
                    FROM courses c
                    LEFT JOIN users u ON c.teacher_id = u.id
                    LEFT JOIN categories cat ON c.category_id = cat.id
                    LEFT JOIN course_tags ct ON c.id = ct.course_id
                    LEFT JOIN tags t ON ct.tag_id = t.id";

            if (!empty($conditions)) {
                $query .= " WHERE " . implode(' AND ', $conditions);
            }

            $query .= " GROUP BY c.id
                       ORDER BY c.created_at DESC
                       LIMIT :limit OFFSET :offset";

            $params['limit'] = $limit;
            $params['offset'] = $offset;

            // Get total count for pagination
            $countQuery = "SELECT COUNT(DISTINCT c.id) as total FROM courses c";
            if (!empty($conditions)) {
                $countQuery .= " WHERE " . implode(' AND ', $conditions);
            }
            $totalCount = $this->db->query($countQuery, $params)->fetch()['total'];

            // Execute main query
            $results = $this->db->query($query, $params)->fetchAll();

            // Hydrate results
            $courses = array_map(function($row) {
                $course = $this->courseRepository->hydrateCourse($row);
                if (isset($row['tags'])) {
                    $course->setTags(explode(',', $row['tags']));
                }
                return [
                    'course' => $course,
                    'teacher_name' => $row['teacher_name'],
                    'category_name' => $row['category_name'],
                    'enrollment_count' => (int) $row['enrollment_count'],
                    'average_rating' => $row['average_rating'] ? round((float) $row['average_rating'], 1) : null,
                    'review_count' => (int) $row['review_count']
                ];
            }, $results);

            return [
                'courses' => $courses,
                'pagination' => [
                    'total' => $totalCount,
                    'page' => $page,
                    'limit' => $limit,
                    'total_pages' => ceil($totalCount / $limit)
                ]
            ];
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

    public function getGlobalStats(): array {
        if (!$this->authService->isAdmin()) {
            throw new \RuntimeException('Unauthorized: Only admins can access global stats');
        }

        try {
            // Get user statistics
            $totalUsers = $this->userRepository->count();
            $totalTeachers = $this->userRepository->countByRole('teacher');
            $totalStudents = $this->userRepository->countByRole('student');
            $newUsersThisMonth = $this->userRepository->countNewUsersSince(
                (new DateTime())->modify('first day of this month')
            );

            // Get course statistics
            $totalCourses = $this->courseRepository->getTotalCourses();
            $publishedCourses = $this->courseRepository->countPublished();
            $pendingCourses = $this->courseRepository->countPending();
            $newCoursesThisMonth = $this->courseRepository->countNewCoursesSince(
                (new DateTime())->modify('first day of this month')
            );

            // Get enrollment statistics
            $totalEnrollments = $this->enrollmentRepository->getTotalCount();
            $activeEnrollments = $this->enrollmentRepository->countActive();
            $enrollmentsThisMonth = $this->enrollmentRepository->getNewEnrollmentsByDateRange(
                (new DateTime())->modify('first day of this month'),
                new DateTime()
            );

            // Get review statistics
            $totalReviews = $this->reviewRepository->count();
            $averageRating = $this->reviewRepository->getOverallAverageRating();
            $reviewsThisMonth = $this->reviewRepository->countReviewsSince(
                (new DateTime())->modify('first day of this month')
            );

            // Calculate revenue statistics
            $totalRevenue = $this->enrollmentRepository->calculateTotalRevenue();
            $revenueThisMonth = $this->enrollmentRepository->calculateRevenueSince(
                (new DateTime())->modify('first day of this month')
            );

            // Get engagement metrics
            $completionRate = $this->enrollmentRepository->getAverageCompletionRate();
            $averageWatchTime = $this->enrollmentRepository->getAverageWatchTime();

            // Get category statistics
            $categoryCounts = $this->courseRepository->getCourseCountByCategory();
            
            // Get platform growth metrics
            $monthlyGrowthRate = $this->calculateMonthlyGrowthRate();
            $teacherRetentionRate = $this->calculateTeacherRetentionRate();
            $studentRetentionRate = $this->calculateStudentRetentionRate();

            return [
                'users' => [
                    'total' => $totalUsers,
                    'teachers' => $totalTeachers,
                    'students' => $totalStudents,
                    'new_this_month' => $newUsersThisMonth,
                    'teacher_retention' => $teacherRetentionRate,
                    'student_retention' => $studentRetentionRate
                ],
                'courses' => [
                    'total' => $totalCourses,
                    'published' => $publishedCourses,
                    'pending' => $pendingCourses,
                    'new_this_month' => $newCoursesThisMonth,
                    'by_category' => $categoryCounts
                ],
                'enrollments' => [
                    'total' => $totalEnrollments,
                    'active' => $activeEnrollments,
                    'this_month' => $enrollmentsThisMonth,
                    'completion_rate' => $completionRate,
                    'average_watch_time' => $averageWatchTime
                ],
                'reviews' => [
                    'total' => $totalReviews,
                    'average_rating' => $averageRating,
                    'this_month' => $reviewsThisMonth
                ],
                'revenue' => [
                    'total' => $totalRevenue,
                    'this_month' => $revenueThisMonth,
                    'monthly_growth' => $monthlyGrowthRate
                ],
                'timestamp' => (new DateTime())->format('Y-m-d H:i:s')
            ];
        } catch (PDOException $e) {
            throw new \RuntimeException('Failed to fetch global stats: ' . $e->getMessage());
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