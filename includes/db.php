<?php
// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
$host = 'localhost';
$dbname = 'academy_db'; // Change to your cPanel DB name
$username = 'root';     // Change to your cPanel DB user
$password = '';         // Change to your cPanel DB password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<h3>Database connection issue:</h3>";
    echo "Please ensure the database <strong>$dbname</strong> exists in your local phpMyAdmin.";
    echo "<br>Error: " . $e->getMessage();
    exit;
}
?>
