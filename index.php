<?php
// Démarrage de la session
session_start();

// Chargement des fichiers nécessaires
require_once __DIR__ . '/src/Router.php';
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/controllers/UserController.php';
require_once __DIR__ . '/models/User.php';

// Initialisation du Router
$router = new Router();

// Récupération de l'action demandée
$action = $_GET['action'] ?? null;

// Traitement des actions
if ($action) {
    $userController = new UserController();
    
    switch ($action) {
        case 'login':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $userController->login();
            }
            break;
            
        case 'register':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $userController->register();
            }
            break;
            
        case 'logout':
            $userController->logout();
            break;
            
        default:
            // Action non reconnue, redirection vers la page d'accueil
            header('Location: index.php');
            exit;
    }
}

// Chargement de la vue appropriée
$router->view();
