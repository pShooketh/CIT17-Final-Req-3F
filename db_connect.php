<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "spa_booking_system";

try {
    $options = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_PERSISTENT => false, // Don't use persistent connections
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
        PDO::ATTR_TIMEOUT => 3600, // Set timeout to 1 hour
    );

    // First, connect without database name
    $conn = new mysqli($servername, $username, $password);
    
    // Create database if it doesn't exist
    $sql = "CREATE DATABASE IF NOT EXISTS $dbname";
    $conn->query($sql);
    
    // Close the initial connection
    $conn->close();
    
    // Create PDO connection with database name
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password, $options);
    
    // Function to check and reconnect if needed
    function checkConnection($pdo) {
        try {
            $pdo->query("SELECT 1");
        } catch (PDOException $e) {
            global $servername, $username, $password, $dbname, $options;
            $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password, $options);
        }
        return $pdo;
    }

} catch(Exception $e) {
    die("Connection failed: " . $e->getMessage());
}

// Increase PHP timeout and memory limit
set_time_limit(300); // 5 minutes
ini_set('memory_limit', '256M');

// Increase MySQL timeout settings
$pdo->exec("SET SESSION wait_timeout=3600"); // 1 hour
$pdo->exec("SET SESSION interactive_timeout=3600"); // 1 hour
?>