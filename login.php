<?php
session_start();
include 'db_connect.php';
include 'functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];
            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid credentials";
        }
    } catch (PDOException $e) {
        $error = "Login failed: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Spa Booking System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-box">
            <a href="index.php" class="btn btn-home">Home</a>
            
            <div class="login-header">
                <h1>Welcome Back</h1>
                <p class="login-subtext">Please login to your account</p>
            </div>
            
            <?php if (isset($_GET['success']) && $_GET['success'] == 'account_created'): ?>
                <div class="success">Account created successfully! Please login.</div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="" class="login-form">
                <div class="form-group">
                    <label>Email Address</label>
                    <div class="input-group">
                        <span class="input-icon">✉️</span>
                        <input type="email" name="email" required 
                               placeholder="Enter your email">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Password</label>
                    <div class="input-group">
                        <span class="input-icon">🔒</span>
                        <input type="password" name="password" required 
                               placeholder="Enter your password">
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-login">Login</button>
                </div>

                <div class="login-footer">
                    <p>Don't have an account? 
                        <a href="signup.php" class="signup-link">Sign up now</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>