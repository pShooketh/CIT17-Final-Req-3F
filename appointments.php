<?php
session_start();
include 'db_connect.php';
include 'header.php';

// Get all appointments with user and service details
$stmt = $pdo->query("
    SELECT a.*, 
           u.full_name as client_name,
           t.full_name as therapist_name,
           s.service_name
    FROM appointments a
    JOIN users u ON a.user_id = u.user_id
    JOIN users t ON a.therapist_id = t.user_id
    JOIN services s ON a.service_id = s.service_id
    ORDER BY a.appointment_date DESC, a.start_time DESC
");
$appointments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Appointments Management</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="card">
            <a href="index.php" class="btn btn-home">Home</a>
            
            <h1>Appointments Management</h1>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="success">
                    <?php 
                        if ($_GET['success'] == 'appointment_added') echo "Appointment successfully scheduled";
                        if ($_GET['success'] == 'appointment_updated') echo "Appointment successfully updated";
                        if ($_GET['success'] == 'appointment_deleted') echo "Appointment successfully cancelled";
                    ?>
                </div>
            <?php endif; ?>

            <a href="createappointment.php" class="btn">Schedule New Appointment</a>
            
            <div class="filter-section">
                <input type="date" id="date-filter" class="filter-input">
                <select id="status-filter" class="filter-input">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="completed">Completed</option>
                    <option value="canceled">Canceled</option>
                </select>
            </div>

            <table>
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Client</th>
                    <th>Therapist</th>
                    <th>Service</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($appointments as $appointment): ?>
                <tr>
                    <td><?= date('M d, Y', strtotime($appointment['appointment_date'])) ?></td>
                    <td><?= date('h:i A', strtotime($appointment['start_time'])) ?></td>
                    <td><?= htmlspecialchars($appointment['client_name']) ?></td>
                    <td><?= htmlspecialchars($appointment['therapist_name']) ?></td>
                    <td><?= htmlspecialchars($appointment['service_name']) ?></td>
                    <td>
                        <span class="status-badge status-<?= $appointment['status'] ?>">
                            <?= ucfirst($appointment['status']) ?>
                        </span>
                    </td>
                    <td>
                        <a href="editappointment.php?id=<?= $appointment['appointment_id'] ?>" 
                           class="btn-edit">Edit</a>
                        <?php if ($appointment['status'] !== 'canceled'): ?>
                            <a href="cancelappointment.php?id=<?= $appointment['appointment_id'] ?>" 
                               onclick="return confirm('Are you sure you want to cancel this appointment?')" 
                               class="btn-delete">Cancel</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</body>
</html> 