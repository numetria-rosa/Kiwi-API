<?php
/**
 * Contrôleur principal de l'application
 * Ce fichier gère les routes et initialise l'application
 */

// Démarrer la session
session_start();

// Charger les fichiers de configuration
require_once 'config/database.php';
require_once 'config/api_config.php';

// Charger les fonctions utilitaires
require_once 'includes/functions.php';

// Charger les modèles
require_once 'models/Airport.php';
require_once 'models/Booking.php';
require_once 'models/Location.php';

// Définir la route par défaut
$route = isset($_GET['route']) ? $_GET['route'] : 'home';

// Routage simple
switch ($route) {
    case 'home':
        // Page d'accueil avec formulaire de recherche
        require_once 'views/home.php';
        break;

    case 'search':
        // Page de résultats de recherche de vols
        require_once 'views/search_results.php';
        break;

    case 'flight':
        // Page de détails d'un vol
        require_once 'views/flight_details.php';
        break;

    case 'booking':
        // Page de réservation
        require_once 'views/booking.php';
        break;

    case 'payment':
        // Page de paiement
        require_once 'views/payment.php';
        break;

    case 'confirmation':
        // Page de confirmation
        require_once 'views/confirmation.php';
        break;

    default:
        // Page 404 ou redirection vers la page d'accueil
        header('HTTP/1.0 404 Not Found');
        echo 'Page not found';
        break;
}
