<?php
use Youdemy\Services\CourseService;

$courseId = $_GET['id']; // Assuming course ID is passed in the URL
$course = $courseService->getCourseById($courseId); // Implement getCourseById in CourseService
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($course->title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold mb-4"><?= htmlspecialchars($course->title) ?></h1>
        <p><strong>Description:</strong> <?= htmlspecialchars($course->description) ?></p>
        <p><strong>Content:</strong> <?= htmlspecialchars($course->content) ?></p>
        <p><strong>Teacher:</strong> <?= htmlspecialchars($course->teacher_name) ?></p>
        <!-- Assuming teacher name is available -->
        <a href="/courses/enroll/<?= $course->id ?>"
            class="mt-4 inline-block bg-purple-500 text-white px-4 py-2 rounded-lg">Enroll in this Course</a>
    </div>
</body>

</html>