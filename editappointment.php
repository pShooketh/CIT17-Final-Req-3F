<?php
session_start();
include 'db_connect.php';
include 'header.php';

if (!isset($_GET['id'])) {
    header("Location: appointments.php");
    exit();
}

$appointment_id = $_GET['id'];

// Fetch appointment details
$stmt = $pdo->prepare("
    SELECT a.*, u.full_name as client_name, t.full_name as therapist_name, s.service_name
    FROM appointments a
    JOIN users u ON a.user_id = u.user_id
    JOIN users t ON a.therapist_id = t.user_id
    JOIN services s ON a.service_id = s.service_id
    WHERE a.appointment_id = ?
");
$stmt->execute([$appointment_id]);
$appointment = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'];
    
    try {
        $stmt = $pdo->prepare("
            UPDATE appointments 
            SET status = ?
            WHERE appointment_id = ?
        ");
        $stmt->execute([$status, $appointment_id]);
        header("Location: appointments.php?success=appointment_updated");
        exit();
    } catch (PDOException $e) {
        $error = "Update failed: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Appointment</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h1>Edit Appointment</h1>
            
            <?php if (isset($error)): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div class="appointment-details">
                <p><strong>Client:</strong> <?= htmlspecialchars($appointment['client_name']) ?></p>
                <p><strong>Therapist:</strong> <?= htmlspecialchars($appointment['therapist_name']) ?></p>
                <p><strong>Service:</strong> <?= htmlspecialchars($appointment['service_name']) ?></p>
                <p><strong>Date:</strong> <?= date('M d, Y', strtotime($appointment['appointment_date'])) ?></p>
                <p><strong>Time:</strong> <?= date('h:i A', strtotime($appointment['start_time'])) ?></p>
            </div>

            <form method="POST">
                <div class="form-group">
                    <label>Status:</label>
                    <select name="status" required>
                        <option value="pending" <?= $appointment['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="confirmed" <?= $appointment['status'] === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                        <option value="completed" <?= $appointment['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                        <option value="canceled" <?= $appointment['status'] === 'canceled' ? 'selected' : '' ?>>Canceled</option>
                        <option value="rejected" <?= $appointment['status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                    </select>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn">Update Status</button>
                    <a href="appointments.php" class="btn-secondary">Back to Appointments</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html> 