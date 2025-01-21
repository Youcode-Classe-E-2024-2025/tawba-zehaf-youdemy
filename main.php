<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/config.php';

use Youdemy\Router;

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
                    <a href="/" class="text-2xl font-bold text-purple-600">YouDemy</a>
                    <!-- <div class="hidden md:flex ml-10">
                        <input type="text" placeholder="Rechercher un cours..." aria-label="Rechercher un cours"
                            class="w-96 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div> -->
                    <div class="hidden md:flex ml-10">
                        <form method="GET" action="/courses">
                            <!-- Adjust action to your courses route -->
                            <input type="text" name="search" placeholder="Rechercher un cours..."
                                aria-label="Rechercher un cours"
                                class="w-96 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <button type="submit"
                                class="ml-2 bg-purple-500 text-white px-4 py-2 rounded-lg">Search</button>
                        </form>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <button class="md:hidden" aria-label="Rechercher">
                        <i class="fas fa-search text-gray-600"></i>
                    </button>
                    <a href="/teacher/dashboard" class="hidden md:block hover:text-purple-600">Enseigner</a>
                    <a href="/student/dashboard" class="hidden md:block hover:text-purple-600">Mon apprentissage</a>
                    <a href="/register"
                        class="hidden md:block bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700">
                        S'inscrire
                    </a>
                    <a href="/login"
                        class="hidden md:block border border-purple-600 text-purple-600 px-4 py-2 rounded-lg hover:bg-purple-50">
                        Connexion
                    </a>
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
            <a href="/teacher/dashboard" class="block px-4 py-2 hover:bg-gray-100">Enseigner</a>
            <a href="/student/dashboard" class="block px-4 py-2 hover:bg-gray-100">Mon apprentissage</a>
            <a href="/register" class="block px-4 py-2 hover:bg-gray-100">S'inscrire</a>
            <a href="/login" class="block px-4 py-2 hover:bg-gray-100">Connexion</a>
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
                    <a href="/courses"
                        class="inline-block bg-white text-purple-900 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100">
                        Découvrir les cours
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main>
        <section class="max-w-7xl mx-auto px-4 py-12">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-bold">Cours populaires</h2>
                <div class="hidden md:flex space-x-4">
                    <select id="sortBy"
                        class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="popularity">Les plus populaires</option>
                        <option value="rating">Les mieux notés</option>
                        <option value="newest">Les plus récents</option>
                        <option value="price-asc">Prix : croissant</option>
                        <option value="price-desc">Prix : décroissant</option>
                    </select>
                    <select id="level"
                        class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="all">Tous niveaux</option>
                        <option value="beginner">Débutant</option>
                        <option value="intermediate">Intermédiaire</option>
                        <option value="advanced">Avancé</option>
                    </select>
                </div>
            </div>
            <!-- Course Grid -->
            <div class="md:col-span-3">
                <div id="courseGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Course Card 1 -->
                    <article class="course-card bg-white rounded-lg shadow-md overflow-hidden group"
                        data-level="beginner" data-price="29.99" data-rating="4.8">
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
                            <h3 class="font-semibold mb-2">Introduction au développement web Full-Stack</h3>
                            <p class="text-gray-600 text-sm mb-2">Apprenez HTML, CSS, JavaScript, Node.js et MongoDB</p>
                            <div class="flex items-center mb-2">
                                <span class="text-yellow-500 font-bold">4.8</span>
                                <div class="flex text-yellow-500 ml-1">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="flex-1">
                                    <span class="font-bold text-xl">29,99 €</span>
                                </div>
                            </div>
                        </div>
                    </article>

                    <!-- Course Card 2 -->
                    <article class="course-card bg-white rounded-lg shadow-md overflow-hidden group"
                        data-level="intermediate" data-price="49.99" data-rating="4.9">
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
                            <h3 class="font-semibold mb-2">Intelligence Artificielle : De Zéro à Expert</h3>
                            <p class="text-gray-600 text-sm mb-2">Python, TensorFlow, Deep Learning et Machine Learning
                            </p>
                            <div class="flex items-center mb-2">
                                <span class="text-yellow-500 font-bold">4.9</span>
                                <div class="flex text-yellow-500 ml-1">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="flex-1">
                                    <span class="font-bold text-xl">49,99 €</span>
                                </div>
                            </div>
                        </div>
                    </article>

                    <!-- Course Card 3 -->
                    <article class="course-card bg-white rounded-lg shadow-md overflow-hidden group"
                        data-level="beginner" data-price="39.99" data-rating="4.7">
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
                            <h3 class="font-semibold mb-2">Design UX/UI Masterclass 2025</h3>
                            <p class="text-gray-600 text-sm mb-2">Figma, Adobe XD, Principes de design et Portfolio</p>
                            <div class="flex items-center mb-2">
                                <span class="text-yellow-500 font-bold">4.7</span>
                                <div class="flex text-yellow-500 ml-1">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="flex-1">
                                    <span class="font-bold text-xl">39,99 €</span>
                                </div>
                            </div>
                        </div>
                    </article>

                    <!-- Course Card 4 -->
                    <article class="course-card bg-white rounded-lg shadow-md overflow-hidden group"
                        data-level="advanced" data-price="59.99" data-rating="4.6">
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
                            <h3 class="font-semibold mb-2">Marketing Digital : Stratégies Avancées</h3>
                            <p class="text-gray-600 text-sm mb-2">SEO, SEM, Médias Sociaux et Analyse de Données</p>
                            <div class="flex items-center mb-2">
                                <span class="text-yellow-500 font-bold">4.6</span>
                                <div class="flex text-yellow-500 ml-1">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="flex-1">
                                    <span class="font-bold text-xl">59,99 €</span>
                                </div>
                            </div>
                        </div>
                    </article>

                    <!-- Course Card 5 -->
                    <article class="course-card bg-white rounded-lg shadow-md overflow-hidden group"
                        data-level="beginner" data-price="19.99" data-rating="4.5">
                        <div class="relative">
                            <img src="https://via.placeholder.com/400x225"
                                alt="Thumbnail du cours de Développement Mobile" class="w-full object-cover">
                            <div
                                class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <button class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700">
                                    Aperçu du cours
                                </button>
                            </div>
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold mb-2">Développement Mobile avec Flutter</h3>
                            <p class="text-gray-600 text-sm mb-2">Créez des applications Android et iOS avec Flutter</p>
                            <div class="flex items-center mb-2">
                                <span class="text-yellow-500 font-bold">4.5</span>
                                <div class="flex text-yellow-500 ml-1">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="flex-1">
                                    <span class="font-bold text-xl">19,99 €</span>
                                </div>
                            </div>
                        </div>
                    </article>

                    <!-- Course Card 6 -->
                    <article class="course-card bg-white rounded-lg shadow-md overflow-hidden group"
                        data-level="intermediate" data-price="45.99" data-rating="4.8">
                        <div class="relative">
                            <img src="https://via.placeholder.com/400x225" alt="Thumbnail du cours de Python Avancé"
                                class="w-full object-cover">
                            <div
                                class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <button class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700">
                                    Aperçu du cours
                                </button>
                            </div>
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold mb-2">Python Avancé : Développement et Data Science</h3>
                            <p class="text-gray-600 text-sm mb-2">Bibliothèques Python, Pandas, NumPy et Machine
                                Learning</p>
                            <div class="flex items-center mb-2">
                                <span class="text-yellow-500 font-bold">4.8</span>
                                <div class="flex text-yellow-500 ml-1">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="flex-1">
                                    <span class="font-bold text-xl">45,99 €</span>
                                </div>
                            </div>
                        </div>
                    </article>
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
    // Get the filter options and course grid
    const sortBySelect = document.getElementById('sortBy');
    const levelSelect = document.getElementById('level');
    const courseGrid = document.getElementById('courseGrid');

    // Function to filter and sort courses
    function filterCourses() {
        const sortBy = sortBySelect.value;
        const level = levelSelect.value;

        // Get all course cards
        const courses = Array.from(courseGrid.getElementsByClassName('course-card'));

        // Filter courses by level
        const filteredCourses = courses.filter(course => {
            if (level === 'all') return true;
            return course.dataset.level === level;
        });

        // Sort courses based on the selected sorting option
        filteredCourses.sort((a, b) => {
            if (sortBy === 'popularity') {
                return b.dataset.rating - a.dataset.rating; // Sort by rating (desc)
            } else if (sortBy === 'rating') {
                return b.dataset.rating - a.dataset.rating; // Sort by rating (desc)
            } else if (sortBy === 'newest') {
                return 0; // Assuming courses are already listed by newest first
            } else if (sortBy === 'price-asc') {
                return parseFloat(a.dataset.price) - parseFloat(b.dataset.price); // Sort by price (asc)
            } else if (sortBy === 'price-desc') {
                return parseFloat(b.dataset.price) - parseFloat(a.dataset.price); // Sort by price (desc)
            }
        });

        // Update the grid with filtered and sorted courses
        courseGrid.innerHTML = '';
        filteredCourses.forEach(course => courseGrid.appendChild(course));
    }

    // Add event listeners for the dropdowns
    sortBySelect.addEventListener('change', filterCourses);
    levelSelect.addEventListener('change', filterCourses);

    // Initial filter on page load
    filterCourses();
    document.addEventListener('DOMContentLoaded', () => {
        const courseData = [{
                title: 'Introduction au développement web Full-Stack',
                description: 'Apprenez HTML, CSS, JavaScript, Node.js et MongoDB',
                level: 'beginner',
                price: '29.99',
                rating: '4.8',
                image: 'https://via.placeholder.com/400x225'
            },
            {
                title: 'Intelligence Artificielle : De Zéro à Expert',
                description: 'Python, TensorFlow, Deep Learning et Machine Learning',
                level: 'intermediate',
                price: '49.99',
                rating: '4.9',
                image: 'https://via.placeholder.com/400x225'
            },
            {
                title: 'Design UX/UI Masterclass 2025',
                description: 'Figma, Adobe XD, Principes de design et Portfolio',
                level: 'beginner',
                price: '39.99',
                rating: '4.7',
                image: 'https://via.placeholder.com/400x225'
            },
            {
                title: 'Marketing Digital : Stratégies Avancées',
                description: 'SEO, SEM, Médias Sociaux et Analyse de Données',
                level: 'advanced',
                price: '59.99',
                rating: '4.6',
                image: 'https://via.placeholder.com/400x225'
            },
            {
                title: 'Développement Mobile avec Flutter',
                description: 'Créez des applications Android et iOS avec Flutter',
                level: 'beginner',
                price: '19.99',
                rating: '4.5',
                image: 'https://via.placeholder.com/400x225'
            },
            {
                title: 'Python Avancé : Développement et Data Science',
                description: 'Bibliothèques Python, Pandas, NumPy et Machine Learning',
                level: 'intermediate',
                price: '45.99',
                rating: '4.8',
                image: 'https://via.placeholder.com/400x225'
            }
        ];

        const courseGrid = document.getElementById('courseGrid');

        courseData.forEach(course => {
            const courseCard = document.createElement('article');
            courseCard.classList.add('course-card', 'bg-white', 'rounded-lg', 'shadow-md',
                'overflow-hidden', 'group');
            courseCard.setAttribute('data-level', course.level);
            courseCard.setAttribute('data-price', course.price);
            courseCard.setAttribute('data-rating', course.rating);

            // Thumbnail
            const thumbnailWrapper = document.createElement('div');
            thumbnailWrapper.classList.add('relative');
            const thumbnailImage = document.createElement('img');
            thumbnailImage.src = course.image;
            thumbnailImage.alt = `Thumbnail du cours de ${course.title}`;
            thumbnailImage.classList.add('w-full', 'object-cover');
            thumbnailWrapper.appendChild(thumbnailImage);

            // Preview Button (Aperçu du cours)
            const previewButtonWrapper = document.createElement('div');
            previewButtonWrapper.classList.add('absolute', 'inset-0', 'bg-black', 'bg-opacity-50',
                'opacity-0', 'group-hover:opacity-100', 'transition-opacity', 'flex',
                'items-center', 'justify-center');
            const previewButton = document.createElement('button');
            previewButton.classList.add('bg-purple-600', 'text-white', 'px-4', 'py-2', 'rounded-lg',
                'hover:bg-purple-700');
            previewButton.textContent = 'Aperçu du cours';
            previewButtonWrapper.appendChild(previewButton);

            thumbnailWrapper.appendChild(previewButtonWrapper);

            // Course Content
            const courseContent = document.createElement('div');
            courseContent.classList.add('p-4');

            const courseTitle = document.createElement('h3');
            courseTitle.classList.add('font-semibold', 'mb-2');
            courseTitle.textContent = course.title;

            const courseDescription = document.createElement('p');
            courseDescription.classList.add('text-gray-600', 'text-sm', 'mb-2');
            courseDescription.textContent = course.description;

            const courseRating = document.createElement('div');
            courseRating.classList.add('flex', 'items-center', 'mb-2');
            const ratingSpan = document.createElement('span');
            ratingSpan.classList.add('text-yellow-500', 'font-bold');
            ratingSpan.textContent = course.rating;
            const starsWrapper = document.createElement('div');
            starsWrapper.classList.add('flex', 'text-yellow-500', 'ml-1');
            for (let i = 0; i < 5; i++) {
                const star = document.createElement('i');
                star.classList.add('fas', 'fa-star');
                if (i >= Math.floor(course.rating)) {
                    star.classList.add('fa-star-half-alt'); // For partial stars
                }
                starsWrapper.appendChild(star);
            }
            courseRating.appendChild(ratingSpan);
            courseRating.appendChild(starsWrapper);

            const coursePriceWrapper = document.createElement('div');
            coursePriceWrapper.classList.add('flex', 'items-center');
            const priceWrapper = document.createElement('div');
            priceWrapper.classList.add('flex-1');
            const price = document.createElement('span');
            price.classList.add('font-bold', 'text-xl');
            price.textContent = `${course.price} €`;
            priceWrapper.appendChild(price);

            coursePriceWrapper.appendChild(priceWrapper);

            // Append all elements to the course card
            courseContent.appendChild(courseTitle);
            courseContent.appendChild(courseDescription);
            courseContent.appendChild(courseRating);
            courseContent.appendChild(coursePriceWrapper);

            courseCard.appendChild(thumbnailWrapper);
            courseCard.appendChild(courseContent);

            courseGrid.appendChild(courseCard);
        });
    });
    </script>
</body>

</html>