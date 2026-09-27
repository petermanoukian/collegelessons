<?php
// Include configuration parameters if not already loaded
require_once __DIR__ . '/config.php';

// Database Credentials
$host     = '127.0.0.1';
$db       = 'college';
$user     = 'root';
$pass     = '1'; // Default Laragon password is empty
$charset  = 'utf8mb4';

// Data Source Name
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// PDO Configuration Options
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetch associative arrays by default
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Enforce native prepared statements
];

try {
    // Create the PDO Instance
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // Stop execution and display a user-friendly error message
    die("Database Connection Failed: " . $e->getMessage());
}
?>