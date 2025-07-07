<?php
/**
 * Configuration de la base de données
 */

// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'kiwi');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Initialize $pdo as null
$pdo = null;

// Tentative de connexion à la base de données
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    // Log the error (in production)
    error_log("Database connection error: " . $e->getMessage());

    // For development, you might want to show the error
    if (defined('DEVELOPMENT_MODE') && DEVELOPMENT_MODE) {
        echo "Database connection error: " . $e->getMessage();
    }
    
    // Set a flag to indicate connection failure
    $db_connection_error = true;
}

// Function to check if database connection is available
function isDatabaseConnected() {
    global $pdo;
    return $pdo !== null;
}
