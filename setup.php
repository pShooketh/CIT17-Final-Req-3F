<?php
include 'db_connect.php';

try {
    // Create database
    $sql = "CREATE DATABASE IF NOT EXISTS spa_booking_system";
    if ($conn->query($sql)) {
        echo "Database created successfully<br>";
    }

    // Select the database
    $conn->select_db("spa_booking_system");

    // Create Users table
    $sql = "CREATE TABLE IF NOT EXISTS users (
        user_id INT PRIMARY KEY AUTO_INCREMENT,
        full_name VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        phone_number VARCHAR(15),
        password VARCHAR(255) NOT NULL,
        role ENUM('customer', 'therapist', 'admin') DEFAULT 'customer',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    if ($conn->query($sql)) {
        echo "Users table created successfully<br>";
    }

    // Create Services table
    $sql = "CREATE TABLE IF NOT EXISTS services (
        service_id INT PRIMARY KEY AUTO_INCREMENT,
        service_name VARCHAR(100) NOT NULL,
        description TEXT,
        duration INT NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    if ($conn->query($sql)) {
        echo "Services table created successfully<br>";
    }

    // Create Appointments table
    $sql = "CREATE TABLE IF NOT EXISTS appointments (
        appointment_id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT,
        therapist_id INT,
        service_id INT,
        appointment_date DATE NOT NULL,
        start_time TIME NOT NULL,
        end_time TIME NOT NULL,
        status ENUM('pending', 'confirmed', 'completed', 'canceled') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(user_id),
        FOREIGN KEY (therapist_id) REFERENCES users(user_id),
        FOREIGN KEY (service_id) REFERENCES services(service_id)
    )";
    if ($conn->query($sql)) {
        echo "Appointments table created successfully<br>";
    }

    // Create Payments table
    $sql = "CREATE TABLE IF NOT EXISTS payments (
        payment_id INT PRIMARY KEY AUTO_INCREMENT,
        appointment_id INT,
        amount DECIMAL(10,2) NOT NULL,
        payment_method ENUM('cash', 'credit_card', 'paypal') NOT NULL,
        payment_status ENUM('paid', 'unpaid', 'refunded') DEFAULT 'unpaid',
        transaction_id VARCHAR(100),
        payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (appointment_id) REFERENCES appointments(appointment_id)
    )";
    if ($conn->query($sql)) {
        echo "Payments table created successfully<br>";
    }

    // Insert default admin user
    $admin_password = password_hash("admin123", PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (full_name, email, password, role) 
            VALUES ('Admin User', 'admin@example.com', '$admin_password', 'admin')
            ON DUPLICATE KEY UPDATE email=email";
    if ($conn->query($sql)) {
        echo "Default admin user created successfully<br>";
    }

} catch (Exception $e) {
    die("Setup failed: " . $e->getMessage());
}
?>