<?php
session_start();
include 'db_connect.php';
include 'header.php';

$stmt = $pdo->query("SELECT * FROM services");
$services = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Spa Services Management</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="card">
            <a href="index.php" class="btn btn-home">Home</a>
            
            <h1>Services Management</h1>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="success">
                    <?php 
                        if ($_GET['success'] == 'service_added') echo "Service successfully added";
                        if ($_GET['success'] == 'service_updated') echo "Service successfully updated";
                        if ($_GET['success'] == 'service_deleted') echo "Service successfully deleted";
                    ?>
                </div>
            <?php endif; ?>

            <a href="createservice.php" class="btn">Add New Service</a>
            
            <table>
                <tr>
                    <th>Service Name</th>
                    <th>Description</th>
                    <th>Duration (mins)</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($services as $service): ?>
                <tr>
                    <td><?= htmlspecialchars($service['service_name']) ?></td>
                    <td><?= htmlspecialchars($service['description']) ?></td>
                    <td><?= htmlspecialchars($service['duration']) ?></td>
                    <td>$<?= htmlspecialchars($service['price']) ?></td>
                    <td>
                        <a href="editservice.php?id=<?= $service['service_id'] ?>" class="btn-edit">Edit</a>
                        <a href="deleteservice.php?id=<?= $service['service_id'] ?>" 
                           onclick="return confirm('Are you sure you want to delete this service?')" 
                           class="btn-delete">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</body>
</html> 