<?php
$host = 'localhost';
$username = 'root'; // Your MySQL username
$password = ''; // Your MySQL password
$database = 'property_rental_pos';

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
die("Connection failed: " . $conn->connect_error);
}
?>