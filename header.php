<!DOCTYPE html>
<html>
<head>
    <title>Spa Booking System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="nav-brand">Spa Booking System</a>
            <div class="nav-links">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="listusers.php">Users</a>
                    <a href="services.php">Services</a>
                    <a href="appointments.php">Appointments</a>
                    <a href="logout.php" onclick="return confirm('Are you sure you want to logout?')">Logout</a>
                <?php else: ?>
                    <div class="login-signup-nav">
                        <a href="login.php">Login</a>
                        <a href="signup.php" class="btn-signup">Sign Up</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <script>
    window.addEventListener('load', function() {
        document.body.classList.add('loaded');
    });
    </script>
</body>
</html> 