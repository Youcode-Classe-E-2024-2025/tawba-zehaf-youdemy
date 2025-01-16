<?php
$title = "YouDemy - Connexion";
?>

<div class="min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <h1 class="text-2xl font-bold text-center mb-6">Connexion</h1>
        
        <form method="POST" action="index.php?action=login">
            <div class="mb-4">
                <label for="email" class="block text-gray-700 font-medium mb-2">Email</label>
                <input type="email" id="email" name="email" required
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                    placeholder="votre@email.com">
            </div>
            
            <div class="mb-6">
                <label for="password" class="block text-gray-700 font-medium mb-2">Mot de passe</label>
                <input type="password" id="password" name="password" required
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                    placeholder="••••••••">
            </div>
            
            <button type="submit"
                class="w-full bg-purple-600 text-white py-2 rounded-lg hover:bg-purple-700 transition-colors">
                Se connecter
            </button>
        </form>
        
        <p class="mt-4 text-center text-gray-600">
            Pas encore de compte ? 
            <a href="index.php?view=register" class="text-purple-600 hover:text-purple-700">S'inscrire</a>
        </p>
    </div>
</div>
