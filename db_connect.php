<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "spa_booking_system";

try {
    // First, connect without database name
    $conn = new mysqli($servername, $username, $password);
    
    // Create database if it doesn't exist
    $sql = "CREATE DATABASE IF NOT EXISTS $dbname";
    $conn->query($sql);
    
    // Close the initial connection
    $conn->close();
    
    // Reconnect with database name
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // Create PDO connection
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Database connection successful!";
} catch(Exception $e) {
    die("Connection failed: " . $e->getMessage());
}
?>