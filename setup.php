<?php
include 'db_connect.php';

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS property_rental_pos";
if ($conn->query($sql) === TRUE) {
echo "Database created successfully\n";
} else {
echo "Error creating database: " . $conn->error;
}

// Use the database
$conn->select_db($database);

// Create Users table
$sql = "CREATE TABLE IF NOT EXISTS users (
id INT(11) AUTO_INCREMENT PRIMARY KEY,
name VARCHAR(100) NOT NULL,
email VARCHAR(100) UNIQUE NOT NULL,
password VARCHAR(255) NOT NULL,
phone VARCHAR(15),
role ENUM('admin', 'customer') DEFAULT 'customer',
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if ($conn->query($sql) === TRUE) {
echo "Table 'users' created successfully\n";
} else {
echo "Error creating table 'users': " . $conn->error . "\n";
}

// Create Services table
$sql = "CREATE TABLE IF NOT EXISTS services (
id INT(11) AUTO_INCREMENT PRIMARY KEY,
service_name VARCHAR(100) NOT NULL,
description TEXT,
price DECIMAL(10, 2) NOT NULL,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if ($conn->query($sql) === TRUE) {
echo "Table 'services' created successfully\n";
} else {
echo "Error creating table 'services': " . $conn->error . "\n";
}

// Create Insert Test table
$sql = "CREATE TABLE IF NOT EXISTS insert_test (
id INT(11) AUTO_INCREMENT PRIMARY KEY,
test_data TEXT NOT NULL,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if ($conn->query($sql) === TRUE) {
echo "Table 'insert_test' created successfully\n";
} else {
echo "Error creating table 'insert_test': " . $conn->error . "\n";
}

// Create Appointments table
$sql = "CREATE TABLE IF NOT EXISTS appointments (
id INT(11) AUTO_INCREMENT PRIMARY KEY,
user_id INT(11) NOT NULL,
service_id INT(11) NOT NULL,
appointment_date DATETIME NOT NULL,
payment_status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
availability ENUM('available', 'unavailable') DEFAULT 'available',
review TEXT,
promotion_code VARCHAR(50),
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (user_id) REFERENCES users(id),
FOREIGN KEY (service_id) REFERENCES services(id)
)";
if ($conn->query($sql) === TRUE) {
echo "Table 'appointments' created successfully\n";
} else {
echo "Error creating table 'appointments': " . $conn->error . "\n";
}

$conn->close();
?>