<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$mysqli = new mysqli('localhost', 'root', '', 'kiwi');

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

echo "Connected successfully<br>";

// Test query
$query = "Barcelona";
$searchTerm = "%{$query}%";

$sql = "SELECT id, name, city_name, country_name, code FROM locations WHERE name LIKE ? OR city_name LIKE ? LIMIT 5";
$stmt = $mysqli->prepare($sql);

if (!$stmt) {
    die("Prepare failed: " . $mysqli->error);
}

$stmt->bind_param("ss", $searchTerm, $searchTerm);

if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}

$result = $stmt->get_result();

echo "Number of results: " . $result->num_rows . "<br>";

while ($row = $result->fetch_assoc()) {
    echo "ID: " . $row['id'] . ", Name: " . $row['name'] . ", City: " . $row['city_name'] . ", Country: " . $row['country_name'] . "<br>";
}

$stmt->close();
$mysqli->close();
?> 