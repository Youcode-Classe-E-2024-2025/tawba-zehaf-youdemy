<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Course</title>
</head>

<body>
    <h1>Create a New Course</h1>
    <form method="POST" action="/courses/create" enctype="multipart/form-data">
        <label for="title">Title:</label>
        <input type="text" name="title" required><br>

        <label for="description">Description:</label>
        <textarea name="description" required></textarea><br>

        <label for="content">Upload Content (Document/Video/Image):</label>
        <input type="file" name="content" accept=".pdf,.doc,.docx,.mp4,.jpg,.jpeg,.png" required><br>

        <label for="tags">Tags:</label>
        <input type="text" name="tags"><br>

        <label for="category">Category:</label>
        <input type="text" name="category"><br>
    <input type="text" name="title" placeholder="Course Title" required>
    <input type="text" name="description" placeholder="Course Description" required>
    <textarea name="content" placeholder="Course Content" required></textarea>
    <input type="number" name="category_id" placeholder="Category ID" required>
    <input type="number" name="teacher_id" placeholder="Teacher ID" required>
    <select name="is_published">
        <option value="1">Published</option>
        <option value="0">Not Published</option>
    </select>
    <input type="text" name="tags" placeholder="Tags (comma-separated)">
    <button type="submit">Create Course</button>
</form>
        <button type="submit">Create Course</button>
    </form>
</body>

</html>