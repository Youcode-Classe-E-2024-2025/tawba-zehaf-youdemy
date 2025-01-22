<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Generate CSRF token if it doesn't exist
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Youdemy</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="src/js/validation.js"></script> <!-- Ensure this path is correct -->
</head>

<body class="bg-white font-sans">
    <div
        class="flex justify-center items-center min-h-screen bg-gradient-to-r from-purple-600 via-purple-500 to-purple-400">
        <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-md">
            <h1 class="text-3xl font-semibold text-center text-purple-700 mb-6">Login to Youdemy</h1>
            <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline"><?php echo htmlspecialchars($_SESSION['error']); ?></span>
            </div>
            <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline"><?php echo htmlspecialchars($_SESSION['success']); ?></span>
            </div>
            <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            <!-- <?php if (isset($data['error'])): ?>
                <div class="text-red-500 text-center mb-4"><?php echo htmlspecialchars($data['error']); ?></div>
            <?php endif; ?> -->

            <form action="/login" method="POST" onsubmit="return validateLoginForm()">

                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                <!-- Email Field -->
                <div class="mb-4">
                    <label for="email" class="block text-purple-600 font-medium">Email:</label>
                    <input type="email" id="email" name="email" required
                        class="w-full p-3 mt-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:outline-none">
                </div>

                <!-- Password Field -->
                <div class="mb-4">
                    <label for="password" class="block text-purple-600 font-medium">Password:</label>
                    <input type="password" id="password" name="password" required
                        class="w-full p-3 mt-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:outline-none">
                </div>

                <!-- Login Button -->
                <div class="mb-4">
                    <button type="submit"
                        class="w-full bg-purple-600 text-white p-3 rounded-lg hover:bg-purple-700 focus:outline-none">Login</button>
                </div>

                <p class="text-center text-purple-600">Don't have an account? <a href="/register"
                        class="font-semibold hover:underline">Register here</a></p>
            </form>
        </div>
    </div>

    <!-- Modal Structure -->
    <div id="errorModal" class="fixed inset-0 flex items-center justify-center z-50 hidden bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-6 m-4 max-w-md w-full">
            <span class="absolute top-0 right-0 p-2 text-gray-600 cursor-pointer" onclick="closeModal()">&times;</span>
            <p id="errorMessage" class="text-red-600"></p>
            <div class="mt-4">
                <button class="bg-blue-500 text-white px-4 py-2 rounded" onclick="closeModal()">Close</button>
            </div>
        </div>
    </div>

</body>

</html>