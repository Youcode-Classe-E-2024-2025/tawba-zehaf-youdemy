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
    <nav class="bg-gray-800 text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <div class="text-2xl font-bold">Youdemy</div>
            <ul class="flex space-x-4">
                <li><a href="#" class="hover:text-gray-400">Home</a></li>
                <li><a href="#" class="hover:text-gray-400">Courses</a></li>
                <li><a href="#" class="hover:text-gray-400">Analytics</a></li>
                <li><a href="#" class="hover:text-gray-400">Settings</a></li>
            </ul>
        </div>
    </nav>

    <!-- Dashboard -->
    <div class="container mx-auto p-4">
        <!-- Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-semibold text-gray-700">Total Students</h3>
                <p id="totalStudents" class="text-2xl font-bold text-gray-900">Loading...</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-semibold text-gray-700">Total Courses</h3>
                <p id="totalCourses" class="text-2xl font-bold text-gray-900">Loading...</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-semibold text-gray-700">Total Revenue</h3>
                <p id="totalRevenue" class="text-2xl font-bold text-gray-900">Loading...</p>
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
                const enrollmentCtx = document.getElementById('enrollmentChart').getContext('2d');
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
    </script>
</body>

</html>