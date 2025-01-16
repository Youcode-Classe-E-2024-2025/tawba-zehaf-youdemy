<?php
$title = "YouDemy - Inscription";
?>

<div class="min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <h1 class="text-2xl font-bold text-center mb-6">Créer un compte</h1>
        
        <form method="POST" action="index.php?action=register">
            <div class="mb-4">
                <label for="name" class="block text-gray-700 font-medium mb-2">Nom complet</label>
                <input type="text" id="name" name="name" required
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                    placeholder="John Doe">
            </div>

            <div class="mb-4">
                <label for="email" class="block text-gray-700 font-medium mb-2">Email</label>
                <input type="email" id="email" name="email" required
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                    placeholder="votre@email.com">
            </div>
            
            <div class="mb-4">
                <label for="password" class="block text-gray-700 font-medium mb-2">Mot de passe</label>
                <input type="password" id="password" name="password" required
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                    placeholder="••••••••">
            </div>

            <div class="mb-6">
                <label for="confirm_password" class="block text-gray-700 font-medium mb-2">Confirmer le mot de passe</label>
                <input type="password" id="confirm_password" name="confirm_password" required
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                    placeholder="••••••••">
            </div>
            
            <button type="submit"
                class="w-full bg-purple-600 text-white py-2 rounded-lg hover:bg-purple-700 transition-colors">
                S'inscrire
            </button>
        </form>
        
        <p class="mt-4 text-center text-gray-600">
            Déjà un compte ? 
            <a href="index.php?view=login" class="text-purple-600 hover:text-purple-700">Se connecter</a>
        </p>
    </div>
</div>
