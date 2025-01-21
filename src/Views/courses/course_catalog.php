<?php
use Youdemy\Services\CourseService;
use Youdemy\Repository\CourseRepository;
use Youdemy\Repository\TagRepository;
use Youdemy\Config\Database;

// Instantiate the database connection
$database = Database::getInstance()->getConnection(); // Get the PDO connection

// Instantiate repositories
$courseRepository = new CourseRepository(Database::getInstance());
$tagRepository = new TagRepository(Database::getInstance());

// Create an instance of CourseService
$courseService = new CourseService($courseRepository, $tagRepository, Database::getInstance());

// Fetch all courses
$courses = $courseService->getAllCourses(); // Fetch all courses

// Capture search query
$search = isset($_GET['search']) ? $_GET['search'] : '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Course Catalog</title>
</head>

<body>
    <h1>Course Catalog</h1>
    <form method="GET" action="/courses">
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search for a course...">
        <button type="submit">Search</button>
    </form>
    <table>
        <?php foreach ($courses as $course): ?>
        <tr>
            <td><?= htmlspecialchars($course['id']) ?></td>
            <td><?= htmlspecialchars($course['title']) ?></td>
            <td><a href="/courses/<?= $course['id'] ?>">View Details</a></td>
            <td><a href="/courses/enroll/<?= $course['id'] ?>">Enroll</a></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>

</html>