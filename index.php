<?php
session_start();
include 'db_connect.php';
include 'header.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Spa Booking System - Home</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <?php if (isset($_GET['status']) && $_GET['status'] == 'logged_out'): ?>
            <div class="success">You have been successfully logged out.</div>
        <?php endif; ?>

        <div class="hero-section">
            <div class="hero-content">
                <h1>Welcome to Spa Booking System</h1>
                <p class="hero-text">Manage your spa services, appointments, and users all in one place</p>
                <div class="hero-features">
                    <div class="feature">
                        <span class="feature-icon">🌟</span>
                        <p>Professional Services</p>
                    </div>
                    <div class="feature">
                        <span class="feature-icon">⏰</span>
                        <p>Easy Scheduling</p>
                    </div>
                    <div class="feature">
                        <span class="feature-icon">💆‍♀️</span>
                        <p>Expert Therapists</p>
                    </div>
                </div>
            </div>
            <div class="hero-image"></div>
        </div>

        <div class="dashboard-menu">
            <a href="listusers.php" class="menu-card">
                <div class="card-icon">👥</div>
                <h3>User Management</h3>
                <p>Manage users, therapists, and administrators</p>
            </a>
            
            <a href="services.php" class="menu-card">
                <div class="card-icon">💆</div>
                <h3>Services</h3>
                <p>View and manage spa services</p>
            </a>
            
            <a href="appointments.php" class="menu-card">
                <div class="card-icon">📅</div>
                <h3>Appointments</h3>
                <p>Manage bookings and schedules</p>
            </a>
            
            <a href="reports.php" class="menu-card">
                <div class="card-icon">📊</div>
                <h3>Reports</h3>
                <p>View analytics and generate reports</p>
            </a>
        </div>

        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="quick-actions">
                <h2>Quick Actions</h2>
                <div class="action-buttons">
                    <a href="createuser.php" class="btn">Add New User</a>
                    <a href="appointments.php?action=create" class="btn">New Appointment</a>
                    <a href="services.php?action=create" class="btn">Add Service</a>
                </div>
            </div>
        <?php else: ?>
            <div class="login-prompt">
                <h2>Get Started</h2>
                <p>Please login or create an account to access the system</p>
                <div class="login-signup-buttons">
                    <a href="login.php" class="btn btn-large">Login</a>
                    <a href="signup.php" class="btn btn-large btn-signup">Sign Up</a>
                </div>
            </div>
        <?php endif; ?>

        <div class="reviews-section">
            <h2>What Our Clients Say</h2>
            <?php if (isset($_GET['success']) && $_GET['success'] == 'review_added'): ?>
                <div class="success">Thank you for your review!</div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="review-actions">
                    <a href="addreview.php" class="btn">Write a Review</a>
                </div>
            <?php endif; ?>
            
            <div class="reviews-grid">
                <?php
                try {
                    $stmt = $pdo->query("
                        SELECT r.*, u.full_name 
                        FROM reviews r 
                        JOIN users u ON r.user_id = u.user_id 
                        ORDER BY r.created_at DESC 
                        LIMIT 5
                    ");
                    $reviews = $stmt->fetchAll();
                    
                    if (empty($reviews)): ?>
                        <p class="no-reviews">No reviews yet.</p>
                    <?php else:
                        foreach ($reviews as $review): ?>
                            <div class="review-card">
                                <div class="review-rating">
                                    <?php 
                                    $rating = $review['rating'];
                                    $ratingText = '';
                                    switch($rating) {
                                        case 1:
                                            $ratingText = '⭐ Poor';
                                            break;
                                        case 2:
                                            $ratingText = '⭐⭐ Fair';
                                            break;
                                        case 3:
                                            $ratingText = '⭐⭐⭐ Good';
                                            break;
                                        case 4:
                                            $ratingText = '⭐⭐⭐⭐ Very Good';
                                            break;
                                        case 5:
                                            $ratingText = '⭐⭐⭐⭐⭐ Excellent';
                                            break;
                                    }
                                    echo $ratingText;
                                    ?>
                                </div>
                                <div class="review-date">
                                    <?= date('M d, Y', strtotime($review['created_at'])) ?>
                                </div>
                                <p class="review-text"><?= htmlspecialchars($review['comment']) ?></p>
                                <p class="review-author">- <?= htmlspecialchars($review['full_name']) ?></p>
                            </div>
                        <?php endforeach;
                    endif;
                } catch (PDOException $e) {
                    echo '<p class="error">Unable to load reviews at this time.</p>';
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html> 