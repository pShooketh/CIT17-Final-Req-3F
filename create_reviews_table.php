<?php
include 'db_connect.php';
include 'functions.php';

try {
    // Create Reviews table
    $sql = "CREATE TABLE IF NOT EXISTS reviews (
        review_id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT,
        rating INT NOT NULL,
        comment TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(user_id)
    )";
    
    $pdo->exec($sql);
    echo "Reviews table created successfully!";
    
    // Insert sample reviews if table is empty
    $stmt = $pdo->query("SELECT COUNT(*) FROM reviews");
    $count = $stmt->fetchColumn();
    
    if ($count == 0) {
        $fake_reviews = [
            ['John Smith', 5, 'Amazing experience! The therapist was very professional and skilled.'],
            ['Sarah Johnson', 4, 'Great service, very relaxing atmosphere. Will definitely come back!'],
            ['Michael Brown', 5, 'Best spa experience I\'ve ever had. Highly recommended!'],
            ['Emily Davis', 4, 'Very professional staff and excellent massage. The facility is clean and welcoming.'],
            ['David Wilson', 5, 'Outstanding service! The therapist really helped with my back pain.']
        ];

        foreach ($fake_reviews as $review) {
            // First create the user if they don't exist
            $email = strtolower(str_replace(' ', '.', $review[0])) . '@example.com';
            $password = password_hash('password123', PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("
                INSERT INTO users (full_name, email, password, role) 
                VALUES (?, ?, ?, 'customer')
                ON DUPLICATE KEY UPDATE user_id=LAST_INSERT_ID(user_id)
            ");
            $stmt->execute([$review[0], $email, $password]);
            
            $user_id = $pdo->lastInsertId();
            
            // Then add their review
            $stmt = $pdo->prepare("
                INSERT INTO reviews (user_id, rating, comment) 
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$user_id, $review[1], $review[2]]);
        }
        echo "<br>Sample reviews added successfully!";
    }
} catch (PDOException $e) {
    die("Error creating reviews table: " . $e->getMessage());
}
?> 