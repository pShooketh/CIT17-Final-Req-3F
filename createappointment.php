<?php
session_start();
include 'db_connect.php';

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
    $start_time = $_POST['start_time'];

    if (empty($start_time)) {
        $error = "Please select a valid time slot.";
    } else {
        // Fetch service duration
        $stmt = $pdo->prepare("SELECT duration FROM services WHERE service_id = ?");
        $stmt->execute([$service_id]);
        $duration = $stmt->fetchColumn();

        // Calculate end time
        $end_time = date('H:i:s', strtotime($start_time . ' + ' . $duration . ' minutes'));

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

// Handle AJAX request for available slots
if (isset($_GET['therapist_id'], $_GET['date'], $_GET['duration'])) {
    $therapist_id = $_GET['therapist_id'];
    $date = $_GET['date'];
    $duration = $_GET['duration'];

    $stmt = $pdo->prepare("
        SELECT start_time, end_time 
        FROM appointments 
        WHERE therapist_id = ? AND appointment_date = ?
    ");
    $stmt->execute([$therapist_id, $date]);
    $bookedSlots = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $openingTime = "09:00:00"; // Opening time of the clinic
    $closingTime = "18:00:00"; // Closing time of the clinic
    $timeSlots = [];
    $current = strtotime($openingTime);
    $endOfDay = strtotime($closingTime);

    while ($current + ($duration * 60) <= $endOfDay) {
        $slotStart = date('H:i:s', $current);
        $slotEnd = date('H:i:s', $current + ($duration * 60));
        $isAvailable = true;

        foreach ($bookedSlots as $booked) {
            if (($slotStart >= $booked['start_time'] && $slotStart < $booked['end_time']) ||
                ($slotEnd > $booked['start_time'] && $slotEnd <= $booked['end_time'])) {
                $isAvailable = false;
                break;
            }
        }

        if ($isAvailable) {
            $timeSlots[] = [
                'time' => $slotStart,
                'formatted_time' => date('g:i A', $current),
            ];
        }
        $current += $duration * 60;
    }

    header('Content-Type: application/json');
    echo json_encode($timeSlots);
    exit();
}
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
                <select name="service_id" required onchange="updateDuration(this)">
                    <option value="">Select Service</option>
                    <?php foreach ($services as $service): ?>
                        <option value="<?= $service['service_id'] ?>" data-duration="<?= $service['duration'] ?>">
                            <?= htmlspecialchars($service['service_name']) ?> 
                            (<?= $service['duration'] ?> mins)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Therapist:</label>
                <select name="therapist_id" required onchange="updateAvailability()">
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
                       min="<?= date('Y-m-d') ?>" onchange="updateAvailability()">
            </div>

            <div class="form-group">
                <label>Available Time Slots:</label>
                <div class="time-slots" id="timeSlots">
                    <!-- Time slots will be dynamically loaded -->
                </div>
            </div>

            <div class="form-group">
                <button type="submit" class="btn">Schedule Appointment</button>
                <a href="appointments.php" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
let selectedDuration = 0;

function updateDuration(select) {
    selectedDuration = select.options[select.selectedIndex].dataset.duration;
    updateAvailability();
}

function updateAvailability() {
    const therapistId = document.querySelector('select[name="therapist_id"]').value;
    const date = document.querySelector('input[name="appointment_date"]').value;

    if (!therapistId || !date || !selectedDuration) return;

    fetch(`schedule_appointment.php?therapist_id=${therapistId}&date=${date}&duration=${selectedDuration}`)
        .then(response => response.json())
        .then(slots => {
            const container = document.getElementById('timeSlots');
            container.innerHTML = '';

            if (slots.length === 0) {
                container.innerHTML = '<p class="no-slots">No available time slots for this date.</p>';
                return;
            }

            slots.forEach(slot => {
                const radio = document.createElement('div');
                radio.className = 'time-slot-option';
                radio.innerHTML = `
                    <input type="radio" name="start_time" value="${slot.time}" id="slot_${slot.time}" required>
                    <label for="slot_${slot.time}">${slot.formatted_time}</label>
                `;
                container.appendChild(radio);
            });
        });
}
</script>
</body>
</html>