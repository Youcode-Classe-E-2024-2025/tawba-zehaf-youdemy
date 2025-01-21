<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Generate CSRF token if it doesn't exist
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Assuming you have a User class for handling user operations
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); 
    $email = $_POST['email'];
    $role = $_POST['role'];
    $is_active =1;
    $created_at = date('Y-m-d H:i:s');
    $updated_at = date('Y-m-d H:i:s');
    try {
        $db = Youdemy\Config\Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':role', $role);
        // $stmt->bindParam(':is_active', $is_active);
        // $stmt->bindParam(':created_at',  $created_at);
        // $stmt->bindParam(':updated_at',   $updated_at);
        $stmt->execute();

        // Redirect or show success message
    } catch (PDOException $e) {
        echo "Registration failed: " . $e->getMessage();
        exit; // Stop execution to prevent further errors
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Youdemy</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="src/js/validation.js"></script> <!-- Ensure this path is correct -->
</head>

<body class="bg-white font-sans">
    <div
        class="flex justify-center items-center min-h-screen bg-gradient-to-r from-purple-600 via-purple-500 to-purple-400">
        <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-md">
            <h1 class="text-3xl font-semibold text-center text-purple-700 mb-6">Register for Youdemy</h1>

            <?php if(isset($data['error'])): ?>
            <div class="text-red-500 text-center mb-4"><?php echo htmlspecialchars($data['error']); ?></div>
            <?php endif; ?>

            <form action="/register" method="POST" onsubmit="return validateRegistrationForm()">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                <!-- Username Field -->
                <div class="mb-4">
                    <label for="username" class="block text-purple-600 font-medium">Username:</label>
                    <input type="text" id="username" name="username" required
                        class="w-full p-3 mt-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:outline-none">
                </div>

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

                <!-- Confirm Password Field -->
                <div class="mb-4">
                    <label for="confirm_password" class="block text-purple-600 font-medium">Confirm Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required
                        class="w-full p-3 mt-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:outline-none">
                </div>

                <!-- Role Field -->
                <div class="mb-4">
                    <label for="role" class="block text-purple-600 font-medium">Role:</label>
                    <select id="role" name="role" required
                        class="w-full p-3 mt-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:outline-none">
                        <option value="student">Student</option>
                        <option value="teacher">Teacher</option>
                    </select>
                </div>

                <!-- Submit Button -->
                <div class="mb-4">
                    <button type="submit"
                        class="w-full bg-purple-600 text-white p-3 rounded-lg hover:bg-purple-700 focus:outline-none">Register</button>
                </div>

                <p class="text-center text-purple-600">Already have an account? <a href="/login"
                        class="font-semibold hover:underline">Login here</a></p>
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