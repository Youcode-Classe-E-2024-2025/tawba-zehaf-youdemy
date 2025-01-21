<?php
// Define the function to fetch the course by ID
function getCourseById($courseId) {
    // Replace with actual code to fetch course details from the database
    // Example:
    // $query = "SELECT * FROM courses WHERE id = ?";
    // $stmt = $pdo->prepare($query);
    // $stmt->execute([$courseId]);
    // return $stmt->fetch();
    return [
        'title' => 'Sample Course',
        'description' => 'This is a sample course description.',
        'content' => 'Course content goes here.',
        'category_id' => 1,
        'teacher_id' => 1,
        'is_published' => 1,
        'tags' => 'sample,course'
    ];
}

// Assuming you have a method to fetch the course by ID
$course = getCourseById($courseId); // Fetch course details here
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Course</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold mb-4">Edit Course</h1>
        <form method="POST" action="/teacher/courses/<?= $courseId ?>/edit">
            <input type="text" name="title" value="<?= htmlspecialchars($course['title']) ?>" required
                class="border p-2 mb-4 w-full">
            <textarea name="description" required
                class="border p-2 mb-4 w-full"><?= htmlspecialchars($course['description']) ?></textarea>
            <textarea name="content" required
                class="border p-2 mb-4 w-full"><?= htmlspecialchars($course['content']) ?></textarea>
            <input type="number" name="category_id" value="<?= htmlspecialchars($course['category_id']) ?>" required
                class="border p-2 mb-4 w-full">
            <input type="number" name="teacher_id" value="<?= htmlspecialchars($course['teacher_id']) ?>" required
                class="border p-2 mb-4 w-full">
            <select name="is_published" class="border p-2 mb-4 w-full">
                <option value="1" <?= $course['is_published'] ? 'selected' : '' ?>>Published</option>
                <option value="0" <?= !$course['is_published'] ? 'selected' : '' ?>>Not Published</option>
            </select>
            <input type="text" name="tags" value="<?= htmlspecialchars($course['tags']) ?>"
                placeholder="Tags (comma-separated)" class="border p-2 mb-4 w-full">
            <button type="submit" class="bg-blue-500 text-white p-2">Update Course</button>
        </form>
    </div>
</body>

</html>