<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Catalog</title>
</head>

<body>
    <h1>Course Catalog</h1>
    <form method="GET" action="search">
        <input type="text" name="keyword" placeholder="Search courses...">
        <button type="submit">Search</button>
    </form>

    <h2>Available Courses</h2>
    <ul>
        <?php foreach ($courses as $course): ?>
        <li><?php echo htmlspecialchars($course['title']); ?></li>
        <?php endforeach; ?>
    </ul>

    <!-- Pagination Logic -->
    <div>
        <?php if ($totalPages > 1): ?>
        <nav>
            <ul class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li>
                    <a href="?page=<?php echo $i; ?>" class="<?php echo ($i == $currentPage) ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                </li>
                <?php endfor; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</body>

</html>