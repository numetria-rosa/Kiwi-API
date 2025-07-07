<?php
include '../config/database.php';

header('Content-Type: application/json');

if (!isset($_GET['query']) || strlen($_GET['query']) < 3) {
    echo json_encode([]);
    exit;
}

$query = $_GET['query'] . '%';

try {
    $sql = "SELECT id, name, city_name, country_name 
            FROM airports 
            WHERE name LIKE :query 
            OR city_name LIKE :query 
            ORDER BY name ASC 
            LIMIT 10";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['query' => $query]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($results);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error']);
}
?> 