<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Courses</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold mb-4">Manage Courses</h1>
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
                    <td class="py-2 px-4 border-b">
                        <a href="/admin/courses/<?= $course['id'] ?>/edit" class="text-blue-500">Edit</a>
                        <a href="/admin/courses/<?= $course['id'] ?>/delete" class="text-red-500">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>