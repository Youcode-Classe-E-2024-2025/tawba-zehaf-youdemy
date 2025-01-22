<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - Youdemy</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-md max-w-md w-full">
            <h1 class="text-2xl font-bold text-red-600 mb-4">Error</h1>
            <p class="text-gray-600 mb-4"><?php echo $error_message ?? 'An error occurred'; ?></p>
            <a href="/teacher/dashboard"
                class="inline-block bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">
                Return to Dashboard
            </a>
        </div>
    </div>
</body>

</html>