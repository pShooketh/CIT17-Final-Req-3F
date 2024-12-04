<?php
session_start();
include 'db_connect.php';
include 'header.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: services.php?error=invalid_id");
    exit();
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM services WHERE service_id = ?");
$stmt->execute([$id]);
$service = $stmt->fetch();

if (!$service) {
    header("Location: services.php?error=service_not_found");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service_name = $_POST['service_name'];
    $description = $_POST['description'];
    $duration = $_POST['duration'];
    $price = $_POST['price'];
    
    try {
        $stmt = $pdo->prepare("
            UPDATE services 
            SET service_name = ?,
                description = ?,
                duration = ?,
                price = ?
            WHERE service_id = ?
        ");
        $stmt->execute([$service_name, $description, $duration, $price, $id]);
        header("Location: services.php?success=service_updated");
        exit();
    } catch (PDOException $e) {
        $error = "Update failed: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Service</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="form-container">
            <a href="index.php" class="btn btn-home">Home</a>
            
            <h1>Edit Service</h1>
            
            <?php if (isset($error)): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Service Name:</label>
                    <input type="text" name="service_name" 
                           value="<?= htmlspecialchars($service['service_name']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Description:</label>
                    <textarea name="description" rows="4" required><?= 
                        htmlspecialchars($service['description']) 
                    ?></textarea>
                </div>

                <div class="form-group">
                    <label>Duration (minutes):</label>
                    <input type="number" name="duration" min="15" step="15" 
                           value="<?= htmlspecialchars($service['duration']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Price ($):</label>
                    <input type="number" name="price" min="0" step="0.01" 
                           value="<?= htmlspecialchars($service['price']) ?>" required>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn">Update Service</button>
                    <a href="services.php" class="btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html> 