<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_NAME ?> - Page non trouvée</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="flex items-center justify-center min-h-screen">
        <div class="text-center">
            <h1 class="text-6xl font-bold text-gray-800 mb-4">404</h1>
            <p class="text-2xl text-gray-600 mb-8">Page non trouvée</p>
            <a href="<?= SITE_URL ?>" class="bg-purple-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-purple-700">
                Retour à l'accueil
            </a>
        </div>
    </div>
</body>
</html>

