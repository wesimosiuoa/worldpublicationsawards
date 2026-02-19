<?php
// Database connection configuration for World Publication Awards
$servername = "localhost";
$username = "root";  // Default XAMPP MySQL username
$password = "";      // Default XAMPP MySQL password (empty)
$dbname = "wpa";    // Database name for World Publication Awards
$port = 3307;       // MySQL port (changed from default 3306)

try {
    $pdo = new PDO("mysql:host=$servername;port=$port;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Uncomment the line below if you want to confirm connection in development
    // echo "Connected successfully to WPA database on port $port";
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>