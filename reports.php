<?php
session_start();
include 'db_connect.php';
include 'header.php';

// Get total appointments by status
$stmt = $pdo->query("
    SELECT status, COUNT(*) as count 
    FROM appointments 
    GROUP BY status
");
$appointmentStats = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Get revenue by service
$stmt = $pdo->query("
    SELECT s.service_name, COUNT(*) as bookings, SUM(s.price) as revenue
    FROM appointments a
    JOIN services s ON a.service_id = s.service_id
    WHERE a.status = 'completed'
    GROUP BY s.service_id
    ORDER BY revenue DESC
    LIMIT 5
");
$serviceStats = $stmt->fetchAll();

// Get top therapists
$stmt = $pdo->query("
    SELECT u.full_name, COUNT(*) as appointments
    FROM appointments a
    JOIN users u ON a.therapist_id = u.user_id
    WHERE a.status = 'completed'
    GROUP BY a.therapist_id
    ORDER BY appointments DESC
    LIMIT 5
");
$therapistStats = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reports & Analytics</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <a href="index.php" class="btn btn-home">Home</a>
        
        <h1>Reports & Analytics</h1>

        <div class="reports-grid">
            <!-- Appointment Statistics -->
            <div class="report-card">
                <h2>Appointment Status</h2>
                <div class="stats-container">
                    <?php foreach ($appointmentStats as $status => $count): ?>
                        <div class="stat-item">
                            <span class="stat-label"><?= ucfirst($status) ?></span>
                            <span class="stat-value"><?= $count ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Top Services -->
            <div class="report-card">
                <h2>Top Services</h2>
                <table class="report-table">
                    <tr>
                        <th>Service</th>
                        <th>Bookings</th>
                        <th>Revenue</th>
                    </tr>
                    <?php foreach ($serviceStats as $service): ?>
                    <tr>
                        <td><?= htmlspecialchars($service['service_name']) ?></td>
                        <td><?= $service['bookings'] ?></td>
                        <td>$<?= number_format($service['revenue'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>

            <!-- Top Therapists -->
            <div class="report-card">
                <h2>Top Therapists</h2>
                <table class="report-table">
                    <tr>
                        <th>Therapist</th>
                        <th>Completed Appointments</th>
                    </tr>
                    <?php foreach ($therapistStats as $therapist): ?>
                    <tr>
                        <td><?= htmlspecialchars($therapist['full_name']) ?></td>
                        <td><?= $therapist['appointments'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>

        <!-- Export Options -->
        <div class="export-section">
            <h2>Export Reports</h2>
            <div class="export-buttons">
                <a href="export.php?type=appointments" class="btn">Export Appointments</a>
                <a href="export.php?type=revenue" class="btn">Export Revenue Report</a>
                <a href="export.php?type=therapists" class="btn">Export Therapist Performance</a>
            </div>
        </div>
    </div>
</body>
</html> 