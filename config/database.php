<?php


define('DB_HOST', 'localhost');
define('DB_NAME', 'kiwi');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

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
    // En production, vous voudriez loguer cette erreur plutôt que de l'afficher
    // echo "Erreur de connexion à la base de données: " . $e->getMessage();

    // Pour le développement, on peut afficher l'erreur ou simplement définir une variable
    $db_connection_error = true;
}
