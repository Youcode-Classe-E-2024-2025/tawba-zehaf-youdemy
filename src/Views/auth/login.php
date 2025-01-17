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
    <link rel="stylesheet" href="/css/style.css">
</head>

<body>
    <div class="container">
        <h1>Login to Youdemy</h1>
        <?php if(isset($data['error'])): ?>
        <div class="error"><?php echo $data['error']; ?></div>
        <?php endif; ?>
        <form action="/users/login" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="/users/register">Register here</a></p>
    </div>
</body>

</html>