<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Course</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold mb-4">Create New Course</h1>
        <form method="POST" action="/teacher/create" enctype="multipart/form-data"
            class="bg-white p-6 rounded-lg shadow-md">
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2" for="title">Course Title</label>
                <input type="text" name="title" id="title" required class="w-full px-3 py-2 border rounded-lg">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2" for="description">Course Description</label>
                <textarea name="description" id="description" required
                    class="w-full px-3 py-2 border rounded-lg"></textarea>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Course Content</label>

                <div class="space-y-4">
                    <!-- Video Upload -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Video Content</label>
                        <input type="file" name="video_content" accept="video/*"
                            class="w-full px-3 py-2 border rounded-lg">
                    </div>

                    <!-- PDF Upload -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600">PDF Document</label>
                        <input type="file" name="pdf_content" accept=".pdf" class="w-full px-3 py-2 border rounded-lg">
                    </div>

                    <!-- Image Upload -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Course Image</label>
                        <input type="file" name="course_image" accept="image/*"
                            class="w-full px-3 py-2 border rounded-lg">
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2" for="category">Category</label>
                <select name="category" id="category" required class="w-full px-3 py-2 border rounded-lg">
                    <option value="">Select Category</option>
                    <?php 
        var_dump($categories); // Debug output
        foreach ($categories as $category): 
        ?>
                    <option value="<?php echo $category['id']; ?>">
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>


            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2" for="tags">Tags (comma-separated)</label>
                <input type="text" name="tags" id="tags" class="w-full px-3 py-2 border rounded-lg">
            </div>

            <button type="submit" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700">
                Create Course
            </button>
        </form>
    </div>
</body>

</html>