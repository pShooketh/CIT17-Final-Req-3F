<?php
session_start();
include 'db_connect.php';
include 'header.php';

// Get unpaid appointments
$stmt = $pdo->query("
    SELECT a.*, 
           u.full_name as client_name,
           s.service_name,
           s.price as service_price
    FROM appointments a
    JOIN users u ON a.user_id = u.user_id
    JOIN services s ON a.service_id = s.service_id
    LEFT JOIN payments p ON a.appointment_id = p.appointment_id
    WHERE p.payment_id IS NULL
    ORDER BY a.appointment_date DESC
");
$unpaid_appointments = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointment_id = $_POST['appointment_id'];
    $amount = $_POST['amount'];
    $payment_method = $_POST['payment_method'];
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO payments (appointment_id, amount, payment_method, payment_status) 
            VALUES (?, ?, ?, 'paid')
        ");
        $stmt->execute([$appointment_id, $amount, $payment_method]);
        header("Location: payments.php?success=payment_added");
        exit();
    } catch (PDOException $e) {
        $error = "Payment failed: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Record Payment</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="form-container">
            <a href="index.php" class="btn btn-home">Home</a>
            
            <h1>Record Payment</h1>
            
            <?php if (isset($error)): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Select Appointment:</label>
                    <select name="appointment_id" required onchange="updateAmount(this)">
                        <option value="">Select an appointment</option>
                        <?php foreach ($unpaid_appointments as $appointment): ?>
                            <option value="<?= $appointment['appointment_id'] ?>" 
                                    data-price="<?= $appointment['service_price'] ?>">
                                <?= date('M d, Y', strtotime($appointment['appointment_date'])) ?> - 
                                <?= htmlspecialchars($appointment['client_name']) ?> - 
                                <?= htmlspecialchars($appointment['service_name']) ?> 
                                ($<?= number_format($appointment['service_price'], 2) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Amount:</label>
                    <input type="number" name="amount" step="0.01" required>
                </div>

                <div class="form-group">
                    <label>Payment Method:</label>
                    <select name="payment_method" required>
                        <option value="cash">Cash</option>
                        <option value="credit_card">Credit Card</option>
                        <option value="paypal">PayPal</option>
                    </select>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn">Record Payment</button>
                    <a href="payments.php" class="btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <script>
    function updateAmount(select) {
        const price = select.options[select.selectedIndex].dataset.price;
        document.querySelector('input[name="amount"]').value = price;
    }
    </script>
</body>
</html> 