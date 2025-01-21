<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($course['title']); ?></title>
</head>

<body>
    <h1><?php echo htmlspecialchars($course['title']); ?></h1>
    <p>Description: <?php echo htmlspecialchars($course['description']); ?></p>
    <p>Content: <?php echo htmlspecialchars($course['content']); ?></p>
    <p>Teacher: <?php echo htmlspecialchars($course['teacher']); ?></p>

    <form method="POST" action="/enroll/<?php echo $course['id']; ?>">
        <button type="submit">Enroll</button>
    </form>
</body>

</html>