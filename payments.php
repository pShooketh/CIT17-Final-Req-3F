<?php
session_start();
include 'db_connect.php';
include 'header.php';

// Get all payments with appointment details
$stmt = $pdo->query("
    SELECT p.*, 
           a.appointment_date,
           u.full_name as client_name,
           s.service_name,
           s.price as service_price
    FROM payments p
    JOIN appointments a ON p.appointment_id = a.appointment_id
    JOIN users u ON a.user_id = u.user_id
    JOIN services s ON a.service_id = s.service_id
    ORDER BY p.payment_date DESC
");
$payments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment Management</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="card">
            <a href="index.php" class="btn btn-home">Home</a>
            
            <h1>Payment Management</h1>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="success">
                    <?php 
                        if ($_GET['success'] == 'payment_added') echo "Payment successfully recorded";
                        if ($_GET['success'] == 'payment_updated') echo "Payment successfully updated";
                        if ($_GET['success'] == 'payment_refunded') echo "Payment successfully refunded";
                    ?>
                </div>
            <?php endif; ?>

            <div class="filter-section">
                <input type="date" id="date-filter" class="filter-input" placeholder="Filter by date">
                <select id="status-filter" class="filter-input">
                    <option value="">All Payment Status</option>
                    <option value="paid">Paid</option>
                    <option value="unpaid">Unpaid</option>
                    <option value="refunded">Refunded</option>
                </select>
            </div>

            <table>
                <tr>
                    <th>Date</th>
                    <th>Client</th>
                    <th>Service</th>
                    <th>Amount</th>
                    <th>Payment Method</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($payments as $payment): ?>
                <tr>
                    <td><?= date('M d, Y', strtotime($payment['payment_date'])) ?></td>
                    <td><?= htmlspecialchars($payment['client_name']) ?></td>
                    <td><?= htmlspecialchars($payment['service_name']) ?></td>
                    <td>$<?= number_format($payment['amount'], 2) ?></td>
                    <td><?= ucfirst($payment['payment_method']) ?></td>
                    <td>
                        <span class="status-badge status-<?= $payment['payment_status'] ?>">
                            <?= ucfirst($payment['payment_status']) ?>
                        </span>
                    </td>
                    <td>
                        <a href="editpayment.php?id=<?= $payment['payment_id'] ?>" 
                           class="btn-edit">Edit</a>
                        <?php if ($payment['payment_status'] === 'paid'): ?>
                            <a href="refundpayment.php?id=<?= $payment['payment_id'] ?>" 
                               onclick="return confirm('Are you sure you want to refund this payment?')" 
                               class="btn-delete">Refund</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</body>
</html> 