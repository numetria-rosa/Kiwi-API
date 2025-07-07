<?php
/**
 * Configuration de l'API Tequila de Kiwi.com
 */

// Configuration de l'API Tequila
define('TEQUILA_API_KEY', 'XXXXXXXXXXXXXXXXXXXXXXXXXX'); // Remplacer par votre clé API
define('TEQUILA_API_URL', 'https://api.tequila.kiwi.com');

// Endpoints API Tequila
define('TEQUILA_LOCATIONS_ENDPOINT', '/locations/query');
define('TEQUILA_SEARCH_ENDPOINT', '/v2/search');
define('TEQUILA_BOOKING_ENDPOINT', '/v2/booking/create');

// Options par défaut pour les recherches
$default_search_options = [
    'currency' => 'EUR',
    'locale' => 'fr',
    'max_stopovers' => 2,
    'limit' => 200
];

// Options pour les langues supportées
$supported_languages = [
    'fr' => 'Français',
    'en' => 'English',
    'de' => 'Deutsch',
    'es' => 'Español',
    'it' => 'Italiano'
];

// Options pour les devises supportées
$supported_currencies = [
    'EUR' => '€',
    'USD' => '$',
    'GBP' => '£',
    'CAD' => 'C$',
    'CHF' => 'CHF'
];
