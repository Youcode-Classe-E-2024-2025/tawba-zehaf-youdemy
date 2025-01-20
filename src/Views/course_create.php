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

        <button type="submit">Create Course</button>
    </form>
</body>

</html>