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
        <div class="hero-section">
            <h1>Welcome to Spa Booking System</h1>
            <p class="hero-text">Manage your spa services, appointments, and users all in one place</p>
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
                <p>Please login to access the system</p>
                <a href="login.php" class="btn btn-large">Login Now</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html> 