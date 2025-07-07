<?php
/**
 * Fonctions utilitaires pour l'application
 */

if (!function_exists('format_price')) {
    function format_price($price, $currency = 'EUR') {
        $currency_symbols = [
            'EUR' => '€',
            'USD' => '$',
            'GBP' => '£',
            'CAD' => 'C$',
            'CHF' => 'CHF'
        ];

        $symbol = isset($currency_symbols[$currency]) ? $currency_symbols[$currency] : $currency;

        if ($currency == 'EUR') {
            return number_format($price, 2, ',', ' ') . ' ' . $symbol;
        } else {
            return $symbol . ' ' . number_format($price, 2, '.', ',');
        }
    }
}

if (!function_exists('format_duration')) {
    /**
     * Calculer la durée entre deux timestamps et la renvoyer sous forme lisible
     *
     * @param int $timestamp_depart Timestamp de départ
     * @param int $timestamp_arrivee Timestamp d'arrivée
     * @return string Durée formatée (ex: "2h 30min")
     */
    function format_duration($timestamp_depart, $timestamp_arrivee) {
        $duree_secondes = $timestamp_arrivee - $timestamp_depart;
        $heures = floor($duree_secondes / 3600);
        $minutes = floor(($duree_secondes % 3600) / 60);

        return $heures . 'h ' . ($minutes > 0 ? $minutes . 'min' : '');
    }
}

/**
 * Formater une date en fonction de la langue
 *
 * @param string $date Date au format Y-m-d
 * @param string $locale Code de langue (fr, en, etc.)
 * @return string Date formatée
 */
function format_date($date, $locale = 'fr') {
    $timestamp = strtotime($date);

    if ($locale == 'fr') {
        return date('d/m/Y', $timestamp);
    } else {
        return date('Y-m-d', $timestamp);
    }
}

/**
 * Formater une heure
 *
 * @param string $time Heure au format H:i:s
 * @return string Heure formatée (ex: "14:30")
 */
function format_time($time) {
    return date('H:i', strtotime($time));
}

/**
 * Sanitize une chaîne pour éviter les injections et autres problèmes de sécurité
 *
 * @param string $input Chaîne à nettoyer
 * @return string Chaîne nettoyée
 */
function sanitize_input($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Générer une URL relative sécurisée
 *
 * @param string $route Nom de la route
 * @param array $params Paramètres GET (optionnel)
 * @return string URL générée
 */
function generate_url($route, $params = []) {
    $url = 'index.php?route=' . urlencode($route);

    foreach ($params as $key => $value) {
        $url .= '&' . urlencode($key) . '=' . urlencode($value);
    }

    return $url;
}

/**
 * Rediriger vers une autre page
 *
 * @param string $route Nom de la route
 * @param array $params Paramètres GET (optionnel)
 */
function redirect($route, $params = []) {
    $url = generate_url($route, $params);
    header('Location: ' . $url);
    exit;
}

/**
 * Vérifier si l'utilisateur est connecté
 *
 * @return bool True si l'utilisateur est connecté, false sinon
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Retourne l'extension d'un fichier
 *
 * @param string $filename Nom du fichier
 * @return string Extension du fichier
 */
function get_file_extension($filename) {
    return pathinfo($filename, PATHINFO_EXTENSION);
}

/**
 * Tronquer un texte à une longueur donnée
 *
 * @param string $text Texte à tronquer
 * @param int $length Longueur maximale
 * @param string $suffix Suffixe à ajouter (par défaut "...")
 * @return string Texte tronqué
 */
function truncate_text($text, $length, $suffix = '...') {
    if (mb_strlen($text) <= $length) {
        return $text;
    }

    return mb_substr($text, 0, $length) . $suffix;
}
