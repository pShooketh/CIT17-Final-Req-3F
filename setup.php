<?php
include 'db_connect.php';
include 'functions.php';

try {
    // Create database
    $sql = "CREATE DATABASE IF NOT EXISTS spa_booking_system";
    if ($conn->query($sql)) {
        echo "Database created successfully<br>";
    }

    // Select the database
    $conn->select_db("spa_booking_system");

    // Drop existing tables in reverse order to avoid foreign key constraints
    $tables = ['reviews', 'payments', 'appointments', 'services', 'users'];
    foreach ($tables as $table) {
        $sql = "DROP TABLE IF EXISTS $table";
        $conn->query($sql);
    }

    // Create Users table first (since it's referenced by other tables)
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

    // Insert default admin user
    $admin_password = password_hash("admin123", PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (full_name, email, password, role) 
            VALUES ('Admin User', 'admin@example.com', '$admin_password', 'admin')
            ON DUPLICATE KEY UPDATE email=email";
    if ($conn->query($sql)) {
        echo "Default admin user created successfully<br>";
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
        status ENUM('pending', 'confirmed', 'completed', 'canceled', 'rejected') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
        FOREIGN KEY (therapist_id) REFERENCES users(user_id) ON DELETE CASCADE,
        FOREIGN KEY (service_id) REFERENCES services(service_id) ON DELETE CASCADE
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
        FOREIGN KEY (appointment_id) REFERENCES appointments(appointment_id) ON DELETE CASCADE
    )";
    if ($conn->query($sql)) {
        echo "Payments table created successfully<br>";
    }

    // Create Reviews table last
    $sql = "CREATE TABLE IF NOT EXISTS reviews (
        review_id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT,
        rating INT NOT NULL,
        comment TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
    )";
    if ($conn->query($sql)) {
        echo "Reviews table created successfully<br>";
    }

    // Insert fake reviews
    $fake_reviews = [
        ['John Smith', 5, 'Amazing experience! The therapist was very professional and skilled.'],
        ['Sarah Johnson', 4, 'Great service, very relaxing atmosphere. Will definitely come back!'],
        ['Michael Brown', 5, 'Best spa experience I\'ve ever had. Highly recommended!'],
        ['Emily Davis', 4, 'Very professional staff and excellent massage. The facility is clean and welcoming.'],
        ['David Wilson', 5, 'Outstanding service! The therapist really helped with my back pain.']
    ];

    foreach ($fake_reviews as $review) {
        // Create user if doesn't exist
        $email = strtolower(str_replace(' ', '.', $review[0])) . '@example.com';
        $password = password_hash('password123', PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("
            INSERT IGNORE INTO users (full_name, email, password, role) 
            VALUES (?, ?, ?, 'customer')
        ");
        $stmt->execute([$review[0], $email, $password]);
        
        // Get user_id (whether just inserted or already existed)
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user_id = $stmt->fetchColumn();
        
        // Add review
        $stmt = $pdo->prepare("
            INSERT INTO reviews (user_id, rating, comment) 
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$user_id, $review[1], $review[2]]);
    }
    echo "Sample reviews added successfully<br>";

} catch (Exception $e) {
    die("Setup failed: " . $e->getMessage());
}
?>