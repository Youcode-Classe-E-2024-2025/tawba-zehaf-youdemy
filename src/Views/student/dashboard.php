<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Youdemy</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
                <div class="flex items-center space-x-4">
                    <a href="/logout" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        <!-- Enrolled Courses -->
        <section class="mb-8">
            <h2 class="text-2xl font-bold mb-4">My Enrolled Courses</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach ($enrolledCourses as $course): ?>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="font-bold text-lg mb-2"><?php echo htmlspecialchars($course['title']); ?></h3>
                    <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($course['description']); ?></p>
                    <p class="text-sm text-gray-500">Instructor:
                        <?php echo htmlspecialchars($course['teacher_name']); ?></p>
                    <a href="/student/course/<?php echo $course['id']; ?>"
                        class="mt-4 inline-block bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">
                        View Course
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Available Courses -->
        <section>
            <h2 class="text-2xl font-bold mb-4">Available Courses</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach ($availableCourses as $course): ?>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="font-bold text-lg mb-2"><?php echo htmlspecialchars($course['title']); ?></h3>
                    <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($course['description']); ?></p>
                    <p class="text-sm text-gray-500">
                        Instructor: <?php echo htmlspecialchars($course['teacher_name']); ?><br>
                        Category: <?php echo htmlspecialchars($course['category_name']); ?>
                    </p>
                    <a href="/student/enroll/<?php echo $course['id']; ?>"
                        class="mt-4 inline-block bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                        Enroll Now
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
</body>

</html>