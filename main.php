<?php
require_once 'src/Router.php';

// Démarrage de la session
// session_start();

// Initialisation du Router
$router = new Router();

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'YouDemy - Plateforme d\'apprentissage en ligne' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-white shadow-md fixed w-full z-10">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="#" class="text-2xl font-bold text-purple-600">YouDemy</a>
                    <div class="hidden md:flex ml-10">
                        <input type="text" placeholder="Rechercher un cours..." aria-label="Rechercher un cours"
                            class="w-96 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <button class="md:hidden" aria-label="Rechercher">
                        <i class="fas fa-search text-gray-600"></i>
                    </button>
                    <a href="#" class="hidden md:block hover:text-purple-600">Enseigner</a>
                    <a href="#" class="hidden md:block hover:text-purple-600">Mon apprentissage</a>
                    <button class="hidden md:block bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700">
                        S'inscrire
                    </button>
                    <button
                        class="hidden md:block border border-purple-600 text-purple-600 px-4 py-2 rounded-lg hover:bg-purple-50">
                        Connexion
                    </button>
                    <button class="md:hidden" aria-label="Menu" id="mobileMenuButton">
                        <i class="fas fa-bars text-gray-600"></i>
                    </button>
                </div>
            </div>
        </div>
        <!-- Menu mobile -->
        <div class="hidden md:hidden bg-white border-t" id="mobileMenu">
            <div class="px-4 py-2">
                <input type="text" placeholder="Rechercher un cours..." aria-label="Rechercher un cours"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>
            <a href="#" class="block px-4 py-2 hover:bg-gray-100">Enseigner</a>
            <a href="#" class="block px-4 py-2 hover:bg-gray-100">Mon apprentissage</a>
            <a href="index.php?view=register" class="block px-4 py-2 hover:bg-gray-100">S'inscrire</a>
            <a href="index.php?view=login" class="block px-4 py-2 hover:bg-gray-100">Connexion</a>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="pt-16">
        <div class="relative bg-purple-900 text-white py-20">
            <div class="max-w-7xl mx-auto px-4">
                <div class="md:w-1/2">
                    <h1 class="text-4xl font-bold mb-4">Apprenez de nouvelles compétences en ligne</h1>
                    <p class="text-lg mb-8">Des milliers de cours dispensés par des experts. Commencez dès aujourd'hui !
                    </p>
                    <button class="bg-white text-purple-900 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100">
                        Découvrir les cours
                    </button>
                </div>
            </div>
        </div>
    </header>

    <main>

        <section class="max-w-7xl mx-auto px-4 py-12">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-bold">Cours populaires</h2>
                <div class="hidden md:flex space-x-4">
                    <select class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option>Les plus populaires</option>
                        <option>Les mieux notés</option>
                        <option>Les plus récents</option>
                        <option>Prix : croissant</option>
                        <option>Prix : décroissant</option>
                    </select>
                    <select class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option>Tous niveaux</option>
                        <option>Débutant</option>
                        <option>Intermédiaire</option>
                        <option>Avancé</option>
                    </select>
                </div>
            </div>
            <!-- Course Grid -->
            <div class="md:col-span-3">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Course Card 1 -->
                    <article class="bg-white rounded-lg shadow-md overflow-hidden group">
                        <div class="relative">
                            <img src="https://via.placeholder.com/400x225"
                                alt="Thumbnail du cours de développement web Full-Stack" class="w-full object-cover">
                            <div
                                class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <button class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700">
                                    Aperçu du cours
                                </button>
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="flex items-center mb-2">
                                <span class="bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded">Bestseller</span>
                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded ml-2">Nouveau</span>
                            </div>
                            <h3 class="font-semibold mb-2">Introduction au développement web Full-Stack</h3>
                            <p class="text-gray-600 text-sm mb-2">Apprenez HTML, CSS, JavaScript, Node.js et MongoDB
                            </p>
                            <div class="flex items-center text-sm text-gray-500 mb-2">
                                <i class="far fa-clock mr-1"></i>
                                <span>32 heures au total</span>
                                <i class="far fa-play-circle ml-4 mr-1"></i>
                                <span>128 leçons</span>
                            </div>
                            <p class="text-gray-600 text-sm mb-2">Par John Doe</p>
                            <div class="flex items-center mb-2">
                                <span class="text-yellow-500 font-bold">4.8</span>
                                <div class="flex text-yellow-500 ml-1">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                                <span class="text-gray-600 text-sm ml-1">(2,345 avis)</span>
                            </div>
                            <div class="flex items-center">
                                <div class="flex-1">
                                    <span class="font-bold text-xl">29,99 €</span>
                                    <span class="line-through text-gray-500 ml-2">99,99 €</span>
                                </div>
                                <div class="bg-red-100 text-red-800 text-sm px-2 py-1 rounded">
                                    2 jours restants
                                </div>
                            </div>
                            <div class="mt-4 space-y-2">
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    <span>Certificat de fin de formation</span>
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    <span>Accès à vie</span>
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    <span>Projets pratiques inclus</span>
                                </div>
                            </div>
                        </div>
                        <div class="p-4 border-t">
                            <button
                                class="w-full bg-purple-600 text-white py-2 rounded-lg hover:bg-purple-700 transition-colors">
                                Ajouter au panier
                            </button>
                            <button
                                class="w-full mt-2 border border-purple-600 text-purple-600 py-2 rounded-lg hover:bg-purple-50 transition-colors">
                                Ajouter à la liste de souhaits
                            </button>
                        </div>
                    </article>

                    <!-- Course Card 2 -->
                    <article class="bg-white rounded-lg shadow-md overflow-hidden group">
                        <div class="relative">
                            <img src="https://via.placeholder.com/400x225"
                                alt="Thumbnail du cours d'Intelligence Artificielle" class="w-full object-cover">
                            <div
                                class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <button class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700">
                                    Aperçu du cours
                                </button>
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="flex items-center mb-2">
                                <span class="bg-orange-100 text-orange-800 text-xs px-2 py-1 rounded">Populaire</span>
                            </div>
                            <h3 class="font-semibold mb-2">Intelligence Artificielle : De Zéro à Expert</h3>
                            <p class="text-gray-600 text-sm mb-2">Python, TensorFlow, Deep Learning et Machine
                                Learning</p>
                            <div class="flex items-center text-sm text-gray-500 mb-2">
                                <i class="far fa-clock mr-1"></i>
                                <span>45 heures au total</span>
                                <i class="far fa-play-circle ml-4 mr-1"></i>
                                <span>164 leçons</span>
                            </div>
                            <p class="text-gray-600 text-sm mb-2">Par Sarah Connor</p>
                            <div class="flex items-center mb-2">
                                <span class="text-yellow-500 font-bold">4.9</span>
                                <div class="flex text-yellow-500 ml-1">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                                <span class="text-gray-600 text-sm ml-1">(3,789 avis)</span>
                            </div>
                            <div class="flex items-center">
                                <div class="flex-1">
                                    <span class="font-bold text-xl">49,99 €</span>
                                    <span class="line-through text-gray-500 ml-2">159,99 €</span>
                                </div>
                                <div class="bg-red-100 text-red-800 text-sm px-2 py-1 rounded">
                                    5 jours restants
                                </div>
                            </div>
                            <div class="mt-4 space-y-2">
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    <span>30 jours satisfait ou remboursé</span>
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    <span>Support instructeur</span>
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    <span>Datasets inclus</span>
                                </div>
                            </div>
                        </div>
                        <div class="p-4 border-t">
                            <button
                                class="w-full bg-purple-600 text-white py-2 rounded-lg hover:bg-purple-700 transition-colors">
                                Ajouter au panier
                            </button>
                            <button
                                class="w-full mt-2 border border-purple-600 text-purple-600 py-2 rounded-lg hover:bg-purple-50 transition-colors">
                                Ajouter à la liste de souhaits
                            </button>
                        </div>
                    </article>

                    <!-- Course Card 3 -->
                    <article class="bg-white rounded-lg shadow-md overflow-hidden group">
                        <div class="relative">
                            <img src="https://via.placeholder.com/400x225" alt="Thumbnail du cours de Design UX/UI"
                                class="w-full object-cover">
                            <div
                                class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <button class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700">
                                    Aperçu du cours
                                </button>
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="flex items-center mb-2">
                                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">Certifiant</span>
                            </div>
                            <h3 class="font-semibold mb-2">Design UX/UI Masterclass 2025</h3>
                            <p class="text-gray-600 text-sm mb-2">Figma, Adobe XD, Principes de design et Portfolio
                            </p>
                            <div class="flex items-center text-sm text-gray-500 mb-2">
                                <i class="far fa-clock mr-1"></i>
                                <span>38 heures au total</span>
                                <i class="far fa-play-circle ml-4 mr-1"></i>
                                <span>145 leçons</span>
                            </div>
                            <p class="text-gray-600 text-sm mb-2">Par Marie Martin</p>
                            <div class="flex items-center mb-2">
                                <span class="text-yellow-500 center mb-2">
                                    <span class="text-yellow-500 font-bold">4.7</span>
                                    <div class="flex text-yellow-500 ml-1">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star-half-alt"></i>
                                    </div>
                                    <span class="text-gray-600 text-sm ml-1">(1,432 avis)</span>
                            </div>
                            <div class="flex items-center">
                                <div class="flex-1">
                                    <span class="font-bold text-xl">39,99 €</span>
                                    <span class="line-through text-gray-500 ml-2">129,99 €</span>
                                </div>
                                <div class="bg-red-100 text-red-800 text-sm px-2 py-1 rounded">
                                    3 jours restants
                                </div>
                            </div>
                            <div class="mt-4 space-y-2">
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    <span>Projet final évalué</span>
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    <span>Templates gratuits inclus</span>
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    <span>Accès aux mises à jour futures</span>
                                </div>
                            </div>
                        </div>
                        <div class="p-4 border-t">
                            <button
                                class="w-full bg-purple-600 text-white py-2 rounded-lg hover:bg-purple-700 transition-colors">
                                Ajouter au panier
                            </button>
                            <button
                                class="w-full mt-2 border border-purple-600 text-purple-600 py-2 rounded-lg hover:bg-purple-50 transition-colors">
                                Ajouter à la liste de souhaits
                            </button>
                        </div>
                    </article>

                    <!-- Course Card 4 (New) -->
                    <article class="bg-white rounded-lg shadow-md overflow-hidden group">
                        <div class="relative">
                            <img src="https://via.placeholder.com/400x225" alt="Thumbnail du cours de Marketing Digital"
                                class="w-full object-cover">
                            <div
                                class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <button class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700">
                                    Aperçu du cours
                                </button>
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="flex items-center mb-2">
                                <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded">Tendance</span>
                            </div>
                            <h3 class="font-semibold mb-2">Marketing Digital : Stratégies Avancées</h3>
                            <p class="text-gray-600 text-sm mb-2">SEO, SEM, Médias Sociaux et Analyse de Données</p>
                            <div class="flex items-center text-sm text-gray-500 mb-2">
                                <i class="far fa-clock mr-1"></i>
                                <span>50 heures au total</span>
                                <i class="far fa-play-circle ml-4 mr-1"></i>
                                <span>180 leçons</span>
                            </div>
                            <p class="text-gray-600 text-sm mb-2">Par Alex Dupont</p>
                            <div class="flex items-center mb-2">
                                <span class="text-yellow-500 font-bold">4.6</span>
                                <div class="flex text-yellow-500 ml-1">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                </div>
                                <span class="text-gray-600 text-sm ml-1">(2,156 avis)</span>
                            </div>
                            <div class="flex items-center">
                                <div class="flex-1">
                                    <span class="font-bold text-xl">59,99 €</span>
                                    <span class="line-through text-gray-500 ml-2">199,99 €</span>
                                </div>
                                <div class="bg-red-100 text-red-800 text-sm px-2 py-1 rounded">
                                    7 jours restants
                                </div>
                            </div>
                            <div class="mt-4 space-y-2">
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    <span>Certification reconnue</span>
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    <span>Études de cas réelles</span>
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    <span>Accès à une communauté d'experts</span>
                                </div>
                            </div>
                        </div>
                        <div class="p-4 border-t">
                            <button
                                class="w-full bg-purple-600 text-white py-2 rounded-lg hover:bg-purple-700 transition-colors">
                                Ajouter au panier
                            </button>
                            <button
                                class="w-full mt-2 border border-purple-600 text-purple-600 py-2 rounded-lg hover:bg-purple-50 transition-colors">
                                Ajouter à la liste de souhaits
                            </button>
                        </div>
                    </article>
                </div>
            </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h4 class="font-semibold mb-4">À propos</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="hover:text-purple-400">Qui sommes-nous</a></li>
                        <li><a href="#" class="hover:text-purple-400">Carrières</a></li>
                        <li><a href="#" class="hover:text-purple-400">Blog</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Ressources</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="hover:text-purple-400">Centre d'aide</a></li>
                        <li><a href="#" class="hover:text-purple-400">Devenir formateur</a></li>
                        <li><a href="#" class="hover:text-purple-400">Affiliés</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Légal</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="hover:text-purple-400">Conditions d'utilisation</a></li>
                        <li><a href="#" class="hover:text-purple-400">Politique de confidentialité</a></li>
                        <li><a href="#" class="hover:text-purple-400">Cookies</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Suivez-nous</h4>
                    <div class="flex space-x-4">
                        <a href="#" class="hover:text-purple-400"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="hover:text-purple-400"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="hover:text-purple-400"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="hover:text-purple-400"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center">
                <p>&copy; 2025 YouDemy. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <script>
        // Toggle mobile menu
        document.getElementById('mobileMenuButton').addEventListener('click', () => {
            const mobileMenu = document.getElementById('mobileMenu');
            mobileMenu.classList.toggle('hidden');
        });

        // Sticky header shadow
        window.addEventListener('scroll', () => {
            const nav = document.querySelector('nav');
            if (window.scrollY > 0) {
                nav.classList.add('shadow-md');
            } else {
                nav.classList.remove('shadow-md');
            }
        });
    </script>
</body>

</html>