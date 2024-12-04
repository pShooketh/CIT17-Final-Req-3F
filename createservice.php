<?php
session_start();
include 'db_connect.php';
include 'header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service_name = $_POST['service_name'];
    $description = $_POST['description'];
    $duration = $_POST['duration'];
    $price = $_POST['price'];
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO services (service_name, description, duration, price) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$service_name, $description, $duration, $price]);
        header("Location: services.php?success=service_added");
        exit();
    } catch (PDOException $e) {
        $error = "Creation failed: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add New Service</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="form-container">
            <a href="index.php" class="btn btn-home">Home</a>
            
            <h1>Add New Service</h1>
            
            <?php if (isset($error)): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Service Name:</label>
                    <input type="text" name="service_name" required>
                </div>

                <div class="form-group">
                    <label>Description:</label>
                    <textarea name="description" rows="4" required></textarea>
                </div>

                <div class="form-group">
                    <label>Duration (minutes):</label>
                    <input type="number" name="duration" min="15" step="15" required>
                </div>

                <div class="form-group">
                    <label>Price ($):</label>
                    <input type="number" name="price" min="0" step="0.01" required>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn">Create Service</button>
                    <a href="services.php" class="btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html> 