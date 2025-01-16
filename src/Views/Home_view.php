<?php
$title = "YouDemy - Accueil";
?>

<section class="max-w-7xl mx-auto px-4 py-12">
    <div class="flex justify-between items-center mb-8">
        <h2 class="text-2xl font-bold">Cours populaires</h2>
        <div class="hidden md:flex space-x-4">
            <select class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                <option>Tous les niveaux</option>
                <option>Débutant</option>
                <option>Intermédiaire</option>
                <option>Avancé</option>
            </select>
            <select class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                <option>Toutes les catégories</option>
                <option>Développement Web</option>
                <option>Design</option>
                <option>Marketing</option>
            </select>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Course Card -->
        <article class="bg-white rounded-lg shadow-md overflow-hidden group">
            <div class="relative">
                <img src="https://via.placeholder.com/400x225" alt="Thumbnail du cours" class="w-full object-cover">
                <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                    <button class="bg-white text-purple-600 px-6 py-2 rounded-lg font-semibold hover:bg-purple-50">
                        Voir le cours
                    </button>
                </div>
            </div>
            <div class="p-4">
                <h3 class="text-lg font-semibold mb-2">Développement Web Full-Stack</h3>
                <p class="text-gray-600 text-sm mb-4">Apprenez à créer des applications web complètes</p>
                <div class="flex items-center justify-between">
                    <span class="text-purple-600 font-bold">49.99 €</span>
                    <div class="flex items-center">
                        <span class="text-yellow-400 mr-1"><i class="fas fa-star"></i></span>
                        <span class="text-gray-600">4.8</span>
                    </div>
                </div>
            </div>
        </article>
    </div>
</section>
