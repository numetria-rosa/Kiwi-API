<?php
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Database connection
    $mysqli = new mysqli('localhost', 'root', '', 'kiwi');

    // Check connection
    if ($mysqli->connect_error) {
        throw new Exception("Connection failed: " . $mysqli->connect_error);
    }

    // Get the search query
    $query = isset($_GET['query']) ? $_GET['query'] : '';
    
    if (empty($query)) {
        echo json_encode([]);
        exit;
    }

    // Prepare and execute the search query
    $searchTerm = "%{$query}%";
    $sql = "SELECT id, name, city_name, country_name, code FROM locations WHERE name LIKE ? OR city_name LIKE ? OR country_name LIKE ? OR code LIKE ? LIMIT 10";
    
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $mysqli->error);
    }

    $stmt->bind_param("ssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $results = [];
    
    while ($row = $result->fetch_assoc()) {
        $results[] = $row;
    }

    // Return the results as JSON
    echo json_encode($results);

} catch (Exception $e) {
    // Return a more detailed error message
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error',
        'message' => $e->getMessage()
    ]);
} finally {
    // Close the connection if it exists
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($mysqli)) {
        $mysqli->close();
    }
}
?> 