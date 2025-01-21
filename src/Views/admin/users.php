<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold mb-4">Manage Users</h1>
        <table class="min-w-full">
            <thead>
                <tr>
                    <th class="py-2 px-4 border-b">User ID</th>
                    <th class="py-2 px-4 border-b">Username</th>
                    <th class="py-2 px-4 border-b">Role</th>
                    <th class="py-2 px-4 border-b">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td class="py-2 px-4 border-b"><?= htmlspecialchars($user['id']) ?></td>
                    <td class="py-2 px-4 border-b"><?= htmlspecialchars($user['username']) ?></td>
                    <td class="py-2 px-4 border-b"><?= htmlspecialchars($user['role']) ?></td>
                    <td class="py-2 px-4 border-b">
                        <a href="/admin/users/<?= $user['id'] ?>/edit" class="text-blue-500">Edit</a>
                        <a href="/admin/users/<?= $user['id'] ?>/delete" class="text-red-500">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>