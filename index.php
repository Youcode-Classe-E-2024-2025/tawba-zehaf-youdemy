<?php
// Démarrage de la session
session_start();

// Chargement des fichiers nécessaires
require_once __DIR__ . '/src/Router.php';
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/src/Controllers/AuthController.php';
require_once __DIR__ . '/src/Models/User.php';

// Initialisation du Router
$router = new Router(__DIR__ . '/src/Views', 'home', '404', __DIR__ . '/src/Controllers');

// Récupération de l'action demandée
$action = $_GET['action'] ?? null;

// Traitement des actions
if ($action) {
    $authController = new AuthController();
    
    switch ($action) {
        case 'login':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $authController->login();
            }
            break;
            
        case 'register':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $authController->register();
            }
            break;
            
        case 'logout':
            $authController->logout();
            break;
            
        default:
            // Action non reconnue, redirection vers la page d'accueil
            header('Location: index.php');
            exit;
    }
}

// Chargement de la vue appropriée
$router->view();