<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Database connection
    $host = 'localhost';
    $dbname = 'kiwi';
    $username = 'root';
    $password = '';

    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Test the connection
    echo "Database connection successful!<br>";

    // Check if airports table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'locations'");
    if ($stmt->rowCount() > 0) {
        echo "Locations table exists!<br>";
        
        // Get table structure
        $stmt = $pdo->query("DESCRIBE locations");
        echo "Table structure:<br>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "Column: " . $row['Field'] . " - Type: " . $row['Type'] . "<br>";
        }

        // Get sample data
        $stmt = $pdo->query("SELECT * FROM locations WHERE name LIKE '%Barcelona%' OR city_name LIKE '%Barcelona%' LIMIT 5");
        echo "<br>Sample data:<br>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "ID: " . $row['id'] . ", Name: " . $row['name'] . ", City: " . $row['city_name'] . ", Country: " . $row['country_name'] . "<br>";
        }
    } else {
        echo "Airports table does not exist!<br>";
    }

} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "<br>";
    echo "Error Code: " . $e->getCode() . "<br>";
}
?> 