<?php
use Youdemy\Config\Database;
require_once 'Config/Database.php'; // Adjust path as necessary

// Database connection
$database = new Database();
$db = $database->getConnection(); // Assuming you have a method to get the connection

// Pagination settings
$limit = 10; // Number of courses per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch courses
$query = "SELECT * FROM courses LIMIT :limit OFFSET :offset";
$stmt = $db->prepare($query);
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count total courses for pagination
$countQuery = "SELECT COUNT(*) FROM courses";
$countStmt = $db->prepare($countQuery);
$countStmt->execute();
$totalCourses = $countStmt->fetchColumn();
$totalPages = ceil($totalCourses / $limit);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold mb-4">Courses</h1>
        <div class="bg-white shadow-md rounded-lg p-6">
            <table class="min-w-full">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b">Course Name</th>
                        <th class="py-2 px-4 border-b">Description</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($courses as $course): ?>
                    <tr>
                        <td class="py-2 px-4 border-b"><?= htmlspecialchars($course['name']) ?></td>
                        <td class="py-2 px-4 border-b"><?= htmlspecialchars($course['description']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <nav class="flex justify-between">
                <a href="?page=<?= max(1, $page - 1) ?>" class="text-blue-500">Previous</a>
                <span>Page <?= $page ?> of <?= $totalPages ?></span>
                <a href="?page=<?= min($totalPages, $page + 1) ?>" class="text-blue-500">Next</a>
            </nav>
        </div>
    </div>
</body>

</html>