<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard - Youdemy</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <!-- Top Navigation Bar -->
    <nav class="bg-white shadow-lg fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-xl font-semibold text-purple-600">Welcome,
                        <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                </div>
                <div class="flex items-center">
                    <a href="/logout" class="text-gray-600 hover:text-purple-600">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="pt-20 px-6">
        <div class="max-w-7xl mx-auto">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition duration-300">
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Total Students</h3>
                    <p class="text-3xl font-bold text-purple-600"><?php echo $stats['total_students'] ?? 0; ?></p>
                </div>
                <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition duration-300">
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Total Courses</h3>
                    <p class="text-3xl font-bold text-purple-600"><?php echo $stats['total_courses'] ?? 0; ?></p>
                </div>
                <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition duration-300">
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Total Enrollments</h3>
                    <p class="text-3xl font-bold text-purple-600"><?php echo $stats['total_enrollments'] ?? 0; ?></p>
                </div>
            </div>

            <!-- Create Course Button -->
            <div class="mb-8">
                <a href="/teacher/create"
                    class="inline-flex items-center px-6 py-3 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 transition duration-300">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Create New Course
                </a>
            </div>

            <!-- Courses Table -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6 border-b">
                    <h2 class="text-xl font-semibold text-gray-800">Manage Courses</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Course ID
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($courses as $course): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($course['id']) ?></td>
                                <td class="px-6 py-4"><?= htmlspecialchars($course['title']) ?></td>
                                <td class="px-6 py-4"><?= htmlspecialchars($course['description']) ?></td>
                                <td class="px-6 py-4">
                                    <a href="/teacher/courses/edit/<?= $course['id'] ?>"
                                        class="text-blue-600 hover:text-blue-800 mr-4">Edit</a>
                                    <a href="/teacher/courses/delete/<?= $course['id'] ?>"
                                        class="text-red-600 hover:text-red-800"
                                        onclick="return confirm('Are you sure?')">Delete</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>