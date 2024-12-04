<?php
session_start();
include 'db_connect.php';
include 'header.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: payments.php?error=invalid_id");
    exit();
}

$id = $_GET['id'];
$stmt = $pdo->prepare("
    SELECT p.*, 
           a.appointment_date,
           u.full_name as client_name,
           s.service_name,
           s.price as service_price
    FROM payments p
    JOIN appointments a ON p.appointment_id = a.appointment_id
    JOIN users u ON a.user_id = u.user_id
    JOIN services s ON a.service_id = s.service_id
    WHERE p.payment_id = ?
");
$stmt->execute([$id]);
$payment = $stmt->fetch();

if (!$payment) {
    header("Location: payments.php?error=payment_not_found");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = $_POST['amount'];
    $payment_method = $_POST['payment_method'];
    $payment_status = $_POST['payment_status'];
    
    try {
        $stmt = $pdo->prepare("
            UPDATE payments 
            SET amount = ?, 
                payment_method = ?,
                payment_status = ?
            WHERE payment_id = ?
        ");
        $stmt->execute([$amount, $payment_method, $payment_status, $id]);
        header("Location: payments.php?success=payment_updated");
        exit();
    } catch (PDOException $e) {
        $error = "Update failed: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Payment</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="form-container">
            <a href="index.php" class="btn btn-home">Home</a>
            
            <h1>Edit Payment</h1>
            
            <?php if (isset($error)): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div class="payment-info">
                <p><strong>Client:</strong> <?= htmlspecialchars($payment['client_name']) ?></p>
                <p><strong>Service:</strong> <?= htmlspecialchars($payment['service_name']) ?></p>
                <p><strong>Date:</strong> <?= date('M d, Y', strtotime($payment['appointment_date'])) ?></p>
            </div>

            <form method="POST">
                <div class="form-group">
                    <label>Amount:</label>
                    <input type="number" name="amount" step="0.01" 
                           value="<?= htmlspecialchars($payment['amount']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Payment Method:</label>
                    <select name="payment_method" required>
                        <option value="cash" <?= $payment['payment_method'] === 'cash' ? 'selected' : '' ?>>
                            Cash
                        </option>
                        <option value="credit_card" <?= $payment['payment_method'] === 'credit_card' ? 'selected' : '' ?>>
                            Credit Card
                        </option>
                        <option value="paypal" <?= $payment['payment_method'] === 'paypal' ? 'selected' : '' ?>>
                            PayPal
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Payment Status:</label>
                    <select name="payment_status" required>
                        <option value="paid" <?= $payment['payment_status'] === 'paid' ? 'selected' : '' ?>>
                            Paid
                        </option>
                        <option value="unpaid" <?= $payment['payment_status'] === 'unpaid' ? 'selected' : '' ?>>
                            Unpaid
                        </option>
                        <option value="refunded" <?= $payment['payment_status'] === 'refunded' ? 'selected' : '' ?>>
                            Refunded
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn">Update Payment</button>
                    <a href="payments.php" class="btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html> 