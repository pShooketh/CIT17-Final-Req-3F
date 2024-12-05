<?php
session_start();
include 'db_connect.php';
include 'functions.php';

// Define business hours
define('BUSINESS_START', '09:00');
define('BUSINESS_END', '18:00');

// Fetch all services
$stmt = $pdo->query("SELECT * FROM services ORDER BY service_name");
$services = $stmt->fetchAll();

// Fetch all therapists
$stmt = $pdo->query("SELECT * FROM users WHERE role = 'therapist' ORDER BY full_name");
$therapists = $stmt->fetchAll();

// Fetch all customers
$stmt = $pdo->query("SELECT * FROM users WHERE role = 'customer' ORDER BY full_name");
$customers = $stmt->fetchAll();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $therapist_id = $_POST['therapist_id'];
    $service_id = $_POST['service_id'];
    $appointment_date = $_POST['appointment_date'];
    $start_time = $_POST['appointment_time'];

    // Validate business hours
    $appointment_time = DateTime::createFromFormat('H:i', $start_time);
    $business_start = DateTime::createFromFormat('H:i', BUSINESS_START);
    $business_end = DateTime::createFromFormat('H:i', BUSINESS_END);

    if ($appointment_time < $business_start || $appointment_time > $business_end) {
        $error = "Please select a time between " . BUSINESS_START . " and " . BUSINESS_END;
    } else {
        // Calculate end time based on service duration
        $stmt = $pdo->prepare("SELECT duration FROM services WHERE service_id = ?");
        $stmt->execute([$service_id]);
        $duration = $stmt->fetchColumn();
        
        // Calculate end time
        $end_time = date('H:i:s', strtotime($start_time . ' + ' . $duration . ' minutes'));
        
        // Check if end time is within business hours
        $service_end_time = DateTime::createFromFormat('H:i:s', $end_time);
        if ($service_end_time > $business_end) {
            $error = "The service duration extends beyond our business hours. Please select an earlier time.";
        } else {
            // Check for overlapping appointments
            $stmt = $pdo->prepare("
                SELECT COUNT(*) FROM appointments 
                WHERE therapist_id = ? 
                AND appointment_date = ? 
                AND (
                    (start_time <= ? AND end_time > ?) OR
                    (start_time < ? AND end_time >= ?) OR
                    (start_time >= ? AND end_time <= ?)
                )
                AND status != 'canceled'
            ");
            $stmt->execute([
                $therapist_id, 
                $appointment_date, 
                $start_time, 
                $start_time,
                $end_time, 
                $end_time,
                $start_time,
                $end_time
            ]);
            
            if ($stmt->fetchColumn() > 0) {
                $error = "This time slot is already booked. Please select another time.";
            } else {
                try {
                    $stmt = $pdo->prepare("
                        INSERT INTO appointments (user_id, therapist_id, service_id, appointment_date, start_time, end_time, status) 
                        VALUES (?, ?, ?, ?, ?, ?, 'pending')
                    ");
                    $stmt->execute([$user_id, $therapist_id, $service_id, $appointment_date, $start_time, $end_time]);
                    header("Location: appointments.php?success=appointment_added");
                    exit();
                } catch (PDOException $e) {
                    $error = "Booking failed: " . $e->getMessage();
                }
            }
        }
    }
}

// Get today's date in Y-m-d format
$today = date('Y-m-d');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Schedule Appointment</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <div class="form-container">
        <a href="index.php" class="btn btn-home">Home</a>
        <h1>Schedule New Appointment</h1>

        <?php if (isset($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" id="appointmentForm">
            <div class="form-group">
                <label>Client:</label>
                <select name="user_id" required>
                    <option value="">Select Client</option>
                    <?php foreach ($customers as $customer): ?>
                        <option value="<?= $customer['user_id'] ?>">
                            <?= htmlspecialchars($customer['full_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Service:</label>
                <select name="service_id" required>
                    <option value="">Select Service</option>
                    <?php foreach ($services as $service): ?>
                        <option value="<?= $service['service_id'] ?>">
                            <?= htmlspecialchars($service['service_name']) ?> 
                            (<?= $service['duration'] ?> mins - $<?= $service['price'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Therapist:</label>
                <select name="therapist_id" required>
                    <option value="">Select Therapist</option>
                    <?php foreach ($therapists as $therapist): ?>
                        <option value="<?= $therapist['user_id'] ?>">
                            <?= htmlspecialchars($therapist['full_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Date:</label>
                <input type="date" name="appointment_date" required 
                       min="<?= $today ?>">
            </div>

            <div class="form-group">
                <label>Time: (<?= BUSINESS_START ?> - <?= BUSINESS_END ?>)</label>
                <input type="time" name="appointment_time" required
                       min="<?= BUSINESS_START ?>" max="<?= BUSINESS_END ?>"
                       step="1800"> <!-- 30-minute intervals -->
            </div>

            <div class="form-group">
                <button type="submit" class="btn">Schedule Appointment</button>
                <a href="appointments.php" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>