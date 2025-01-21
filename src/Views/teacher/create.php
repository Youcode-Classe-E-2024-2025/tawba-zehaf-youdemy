<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Course</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold mb-4">Create Course</h1>
        <form method="POST" action="/teacher/courses/create">
            <input type="text" name="title" placeholder="Course Title" required class="border p-2 mb-4 w-full">
            <textarea name="description" placeholder="Course Description" required
                class="border p-2 mb-4 w-full"></textarea>
            <textarea name="content" placeholder="Course Content" required class="border p-2 mb-4 w-full"></textarea>
            <input type="number" name="category_id" placeholder="Category ID" required class="border p-2 mb-4 w-full">
            <input type="number" name="teacher_id" placeholder="Teacher ID" required class="border p-2 mb-4 w-full">
            <select name="is_published" class="border p-2 mb-4 w-full">
                <option value="1">Published</option>
                <option value="0">Not Published</option>
            </select>
            <input type="text" name="tags" placeholder="Tags (comma-separated)" class="border p-2 mb-4 w-full">
            <button type="submit" class="bg-blue-500 text-white p-2">Create Course</button>
        </form>
    </div>
</body>

</html>