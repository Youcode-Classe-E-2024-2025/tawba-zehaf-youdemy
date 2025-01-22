<?php


use Youdemy\Services\AdminService;

// Assuming you have a way to get the current user and check for admin privileges
// $adminService = new AdminService($userRepository, $courseRepository, $enrollmentRepository, $reviewRepository, $authService, $database);

// Fetch users and courses
$users = $adminService->getAllUsers();
$courses = $adminService->getAllCourses();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold mb-4">Admin Dashboard</h1>

        <h2 class="text-xl font-semibold mb-2">Manage Users</h2>
        <table class="min-w-full mb-6">
            <thead>
                <tr>
                    <th class="py-2 px-4 border-b">User ID</th>
                    <th class="py-2 px-4 border-b">Username</th>
                    <th class="py-2 px-4 border-b">Email</th>
                    <th class="py-2 px-4 border-b">Role</th>
                    <th class="py-2 px-4 border-b">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td class="py-2 px-4 border-b"><?= htmlspecialchars($user['id']) ?></td>
                    <td class="py-2 px-4 border-b"><?= htmlspecialchars($user['username']) ?></td>
                    <td class="py-2 px-4 border-b"><?= htmlspecialchars($user['email']) ?></td>
                    <td class="py-2 px-4 border-b"><?= htmlspecialchars($user['role']) ?>
                    <td class="px-6 py-4">
                        <a href="/admin/user/edit/<?php echo $user['id']; ?>"
                            class="text-blue-600 hover:text-blue-800 mr-4">Edit</a>
                        <a href="/admin/user/delete/<?php echo $user['id']; ?>" class="text-red-600 hover:text-red-800"
                            onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                    </td>


                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2 class="text-xl font-semibold mb-2">Manage Courses</h2>
        <table class="min-w-full">
            <thead>
                <tr>
                    <th class="py-2 px-4 border-b">Course ID</th>
                    <th class="py-2 px-4 border-b">Title</th>
                    <th class="py-2 px-4 border-b">Description</th>
                    <th class="py-2 px-4 border-b">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($courses as $course): ?>
                <tr>
                    <td class="py-2 px-4 border-b"><?= htmlspecialchars($course['id']) ?></td>
                    <td class="py-2 px-4 border-b"><?= htmlspecialchars($course['title']) ?></td>
                    <td class="py-2 px-4 border-b"><?= htmlspecialchars($course['description']) ?></td>
                    <td class="px-6 py-4">
                        <a href="/admin/course/edit/<?php echo $course['id']; ?>"
                            class="text-blue-600 hover:text-blue-800 mr-4">Edit</a>
                        <a href="/admin/course/delete/<?php echo $course['id']; ?>"
                            class="text-red-600 hover:text-red-800" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>

                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>