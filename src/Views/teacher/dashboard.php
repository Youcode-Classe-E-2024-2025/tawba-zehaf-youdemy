<?php var_dump($_SESSION);
var_dump($_SESSION['user_role']);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Youdemy Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-gray-100">
    <!-- Navbar -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-xl font-semibold text-purple-600">Welcome back,
                        <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                </div>
                <!-- Navbar -->
                <!-- <nav class="bg-gray-800 text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <div class="text-2xl font-bold">Youdemy</div>
            <ul class="flex space-x-4">
                <li><a href="#" class="hover:text-gray-400">Home</a></li>
                <li><a href="/teacher/courses" class="hover:text-gray-400">Courses</a></li>
                <li><a href="#" class="hover:text-gray-400">Analytics</a></li>
                <li><a href="#" class="hover:text-gray-400">Settings</a></li>
            </ul>
        </div>
    </nav> -->

                <!-- Dashboard -->
                <div class="container mx-auto p-4">
                    <!-- Cards -->
                    <div class="container mx-auto p-4">
                        <!-- Stats Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                            <div class="bg-white p-6 rounded-lg shadow-md">
                                <h3 class="text-lg font-semibold text-gray-700">Total Students</h3>
                                <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total_students'] ?? 0; ?>
                                </p>
                            </div>
                            <div class="bg-white p-6 rounded-lg shadow-md">
                                <h3 class="text-lg font-semibold text-gray-700">Total Courses</h3>
                                <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total_courses'] ?? 0; ?>
                                </p>
                            </div>
                            <div class="bg-white p-6 rounded-lg shadow-md">
                                <h3 class="text-lg font-semibold text-gray-700">Total Enrollments</h3>
                                <p class="text-2xl font-bold text-gray-900">
                                    <?php echo $stats['total_enrollments'] ?? 0; ?></p>
                            </div>
                        </div>

                        <!-- Courses List -->
                        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
                            <h2 class="text-xl font-bold mb-4">Your Courses</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <?php foreach ($courses as $course): ?>
                                <div class="border rounded-lg p-4">
                                    <h3 class="font-semibold"><?php echo htmlspecialchars($course['title']); ?></h3>
                                    <p class="text-gray-600 text-sm mb-2">
                                        <?php echo htmlspecialchars($course['description']); ?>
                                    </p>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-500">
                                            <?php echo $course['student_count']; ?> students
                                        </span>
                                        <a href="/teacher/courses/edit/<?php echo $course['id']; ?>"
                                            class="text-purple-600 hover:text-purple-800">
                                            Edit
                                        </a>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Charts -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-lg font-semibold text-gray-700 mb-4">Enrollments Over Time</h3>
                            <canvas id="enrollmentChart"></canvas>
                        </div>
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-lg font-semibold text-gray-700 mb-4">Courses by Category</h3>
                            <canvas id="categoryChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="mb-6">
                    <a href="/teacher/create"
                        class="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 inline-flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Create New Course
                    </a>
                </div>

                <!-- JavaScript -->
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    fetch('db.php')
                        .then(response => response.json())
                        .then(data => {
                            // Update card data
                            document.getElementById('totalStudents').textContent = data.totalStudents;
                            document.getElementById('totalCourses').textContent = data.totalCourses;
                            document.getElementById('totalRevenue').textContent = `$${data.totalRevenue}`;

                            // Enrollment Chart
                            const enrollmentCtx = document.getElementById('enrollmentChart').getContext(
                                '2d');
                            new Chart(enrollmentCtx, {
                                type: 'line',
                                data: {
                                    labels: data.enrollmentData.map(item => item.date),
                                    datasets: [{
                                        label: 'Enrollments',
                                        data: data.enrollmentData.map(item => item.count),
                                        borderColor: '#3b82f6',
                                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                        fill: true
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    plugins: {
                                        legend: {
                                            display: false
                                        }
                                    }
                                }
                            });

                            // Category Chart
                            const categoryCtx = document.getElementById('categoryChart').getContext('2d');
                            new Chart(categoryCtx, {
                                type: 'bar',
                                data: {
                                    labels: data.courseCategories.map(item => item.name),
                                    datasets: [{
                                        label: 'Courses by Category',
                                        data: data.courseCategories.map(item => item.count),
                                        backgroundColor: '#ef4444',
                                        borderColor: '#dc2626',
                                        borderWidth: 1
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    plugins: {
                                        legend: {
                                            display: false
                                        }
                                    }
                                }
                            });
                        })
                        .catch(error => console.error('Error fetching data:', error));
                });
                const DATA_COUNT = 7;
                const NUMBER_CFG = {
                    count: DATA_COUNT,
                    min: -100,
                    max: 100
                };

                const labels = Utils.months({
                    count: 7
                });
                const data = {
                    labels: labels,
                    datasets: [{
                            label: 'Fully Rounded',
                            data: Utils.numbers(NUMBER_CFG),
                            borderColor: Utils.CHART_COLORS.red,
                            backgroundColor: Utils.transparentize(Utils.CHART_COLORS.red, 0.5),
                            borderWidth: 2,
                            borderRadius: Number.MAX_VALUE,
                            borderSkipped: false,
                        },
                        {
                            label: 'Small Radius',
                            data: Utils.numbers(NUMBER_CFG),
                            borderColor: Utils.CHART_COLORS.blue,
                            backgroundColor: Utils.transparentize(Utils.CHART_COLORS.blue, 0.5),
                            borderWidth: 2,
                            borderRadius: 5,
                            borderSkipped: false,
                        }
                    ]
                };
                </script>
</body>

</html>