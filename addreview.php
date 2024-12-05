<?php
session_start();
include 'db_connect.php';
include 'functions.php';
include 'header.php';

// Only logged-in users can add reviews
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];
    
    try {
        // First check if reviews table exists
        $stmt = $pdo->query("SHOW TABLES LIKE 'reviews'");
        if ($stmt->rowCount() == 0) {
            // Table doesn't exist, create it
            $sql = "CREATE TABLE IF NOT EXISTS reviews (
                review_id INT PRIMARY KEY AUTO_INCREMENT,
                user_id INT,
                rating INT NOT NULL,
                comment TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
            )";
            $pdo->exec($sql);
        }

        $stmt = executeQuery($pdo, "
            INSERT INTO reviews (user_id, rating, comment) 
            VALUES (?, ?, ?)
        ", [$user_id, $rating, $comment]);
        header("Location: index.php?success=review_added");
        exit();
    } catch (PDOException $e) {
        $error = "Failed to add review. Please try again or contact support if the problem persists.";
        error_log("Review addition failed: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Review - Spa Booking System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h1>Write a Review</h1>
            
            <?php if (isset($error)): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Rating:</label>
                    <div class="rating-input">
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                            <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>" required>
                            <label for="star<?= $i ?>">⭐</label>
                        <?php endfor; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label>Your Review:</label>
                    <textarea name="comment" rows="4" required placeholder="Share your experience..."></textarea>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn">Submit Review</button>
                    <a href="index.php" class="btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html> 