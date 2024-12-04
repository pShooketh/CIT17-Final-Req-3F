<?php
session_start();
include 'db_connect.php';
include 'header.php';

$stmt = $pdo->query("SELECT * FROM promotions ORDER BY start_date DESC");
$promotions = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Promotions Management</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="card">
            <a href="index.php" class="btn btn-home">Home</a>
            
            <h1>Promotions Management</h1>

            <?php if (isset($_GET['success'])): ?>
                <div class="success">
                    <?php 
                        if ($_GET['success'] == 'promo_added') echo "Promotion successfully created";
                        if ($_GET['success'] == 'promo_updated') echo "Promotion successfully updated";
                        if ($_GET['success'] == 'promo_deleted') echo "Promotion successfully deleted";
                    ?>
                </div>
            <?php endif; ?>

            <a href="createpromotion.php" class="btn">Create New Promotion</a>

            <div class="promotions-grid">
                <?php foreach ($promotions as $promo): ?>
                    <div class="promo-card <?= strtotime($promo['end_date']) < time() ? 'expired' : '' ?>">
                        <div class="promo-header">
                            <h3><?= htmlspecialchars($promo['promo_code']) ?></h3>
                            <span class="discount-badge">
                                <?= $promo['discount_percent'] ?>% OFF
                            </span>
                        </div>
                        
                        <p class="promo-description">
                            <?= htmlspecialchars($promo['description']) ?>
                        </p>
                        
                        <div class="promo-dates">
                            <span>Valid from: <?= date('M d, Y', strtotime($promo['start_date'])) ?></span>
                            <span>Until: <?= date('M d, Y', strtotime($promo['end_date'])) ?></span>
                        </div>
                        
                        <div class="promo-actions">
                            <a href="editpromotion.php?id=<?= $promo['promo_id'] ?>" 
                               class="btn-edit">Edit</a>
                            <a href="deletepromotion.php?id=<?= $promo['promo_id'] ?>" 
                               onclick="return confirm('Are you sure you want to delete this promotion?')" 
                               class="btn-delete">Delete</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html> 