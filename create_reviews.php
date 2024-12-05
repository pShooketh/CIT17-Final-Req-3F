<?php
try {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "spa_booking_system";

    // Create connection
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create reviews table
    $sql = "CREATE TABLE IF NOT EXISTS reviews (
        review_id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT,
        rating INT NOT NULL,
        comment TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
    )";
    
    $pdo->exec($sql);
    echo "Reviews table created successfully!<br>";

    // Check if table is empty
    $stmt = $pdo->query("SELECT COUNT(*) FROM reviews");
    $count = $stmt->fetchColumn();

    if ($count == 0) {
        // Add sample reviews
        $reviews = [
            ['John Smith', 5, 'Amazing experience! The therapist was very professional and skilled.'],
            ['Sarah Johnson', 4, 'Great service, very relaxing atmosphere. Will definitely come back!'],
            ['Michael Brown', 5, 'Best spa experience I\'ve ever had. Highly recommended!'],
            ['Emily Davis', 4, 'Very professional staff and excellent massage. The facility is clean and welcoming.'],
            ['David Wilson', 5, 'Outstanding service! The therapist really helped with my back pain.']
        ];

        foreach ($reviews as $review) {
            // Create user if not exists
            $email = strtolower(str_replace(' ', '.', $review[0])) . '@example.com';
            $password = password_hash('password123', PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("
                INSERT IGNORE INTO users (full_name, email, password, role) 
                VALUES (?, ?, ?, 'customer')
            ");
            $stmt->execute([$review[0], $email, $password]);
            
            // Get user_id
            $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user_id = $stmt->fetchColumn();
            
            // Add review
            if ($user_id) {
                $stmt = $pdo->prepare("
                    INSERT INTO reviews (user_id, rating, comment) 
                    VALUES (?, ?, ?)
                ");
                $stmt->execute([$user_id, $review[1], $review[2]]);
            }
        }
        echo "Sample reviews added successfully!";
    }

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 