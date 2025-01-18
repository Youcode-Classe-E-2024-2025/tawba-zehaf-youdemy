<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
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
</head>

<body class="bg-white font-sans">
    <div
        class="flex justify-center items-center min-h-screen bg-gradient-to-r from-purple-600 via-purple-500 to-purple-400">
        <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-md">
            <h1 class="text-3xl font-semibold text-center text-purple-700 mb-6">Login to Youdemy</h1>

            <?php if(isset($data['error'])): ?>
            <div class="text-red-500 text-center mb-4"><?php echo $data['error']; ?></div>
            <?php endif; ?>

            <form action="/users/login" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                <!-- Email Field -->
                <div class=" mb-4">
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

                <p class="text-center text-purple-600">Don't have an account? <a href="/users/register"
                        class="font-semibold hover:underline">Register here</a></p>
            </form>
        </div>
    </div>
</body>

</html>