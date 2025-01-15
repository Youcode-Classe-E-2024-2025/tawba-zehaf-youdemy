<script src="https://cdn.tailwindcss.com"></script>
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Inscription
            </h2>
        </div>
        <?php
        if (!empty($errors)) {
            echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative' role='alert'>";
            foreach ($errors as $error) {
                echo "<p>{$error}</p>";
            }
            echo "</div>";
        }
        ?>
        <form class="mt-8 space-y-6" action="/register" method="POST" id="registerForm">
            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="name" class="sr-only">Nom</label>
                    <input id="name" name="name" type="text" required
                        class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                        placeholder="Nom">
                </div>
                <div>
                    <label for="email-address" class="sr-only">Adresse e-mail</label>
                    <input id="email-address" name="email" type="email" autocomplete="email" required
                        class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                        placeholder="Adresse e-mail">
                </div>
                <div>
                    <label for="password" class="sr-only">Mot de passe</label>
                    <input id="password" name="password" type="password" autocomplete="new-password" required
                        class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                        placeholder="Mot de passe">
                </div>
            </div>

            <div>
                <button type="submit"
                    class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    S'inscrire
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('registerForm').addEventListener('submit', function(event) {
    var name = document.getElementById('name').value;
    var email = document.getElementById('email-address').value;
    var password = document.getElementById('password').value;
    var errors = [];

    // Name validation
    if (!name.match(/^[a-zA-Z ]{2,100}$/)) {
        errors.push("Name must be between 2 and 100 characters and contain only letters and spaces.");
    }

    // Email validation
    if (!email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
        errors.push("Please enter a valid email address.");
    }

    // Password validation
    if (!password.match(/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/)) {
        errors.push(
            "Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, and one number."
        );
    }

    if (errors.length > 0) {
        event.preventDefault();
        alert(errors.join("\n"));
    }
});
</script>