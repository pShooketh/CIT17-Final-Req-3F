<?php
session_start();
include 'db_connect.php';
include 'header.php';

// Get all therapists
$stmt = $pdo->query("SELECT user_id, full_name FROM users WHERE role = 'therapist'");
$therapists = $stmt->fetchAll();

// Get selected therapist's availability
$therapist_id = $_GET['therapist_id'] ?? $therapists[0]['user_id'] ?? null;
if ($therapist_id) {
    $stmt = $pdo->prepare("
        SELECT * FROM availability 
        WHERE therapist_id = ? 
        ORDER BY date, start_time
    ");
    $stmt->execute([$therapist_id]);
    $availability = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Therapist Availability</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="card">
            <a href="index.php" class="btn btn-home">Home</a>
            
            <h1>Therapist Availability</h1>

            <?php if (isset($_GET['success'])): ?>
                <div class="success">
                    <?php 
                        if ($_GET['success'] == 'availability_added') echo "Availability successfully added";
                        if ($_GET['success'] == 'availability_updated') echo "Availability successfully updated";
                        if ($_GET['success'] == 'availability_deleted') echo "Availability successfully removed";
                    ?>
                </div>
            <?php endif; ?>

            <div class="therapist-selector">
                <label>Select Therapist:</label>
                <select id="therapist-select" onchange="window.location.href='?therapist_id='+this.value">
                    <?php foreach ($therapists as $therapist): ?>
                        <option value="<?= $therapist['user_id'] ?>" 
                                <?= $therapist_id == $therapist['user_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($therapist['full_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <a href="addavailability.php?therapist_id=<?= $therapist_id ?>" class="btn">Add Availability</a>

            <div class="availability-calendar">
                <?php
                if (!empty($availability)) {
                    $current_date = null;
                    foreach ($availability as $slot) {
                        if ($current_date !== $slot['date']) {
                            if ($current_date !== null) echo "</div>";
                            $current_date = $slot['date'];
                            echo "<div class='date-group'>";
                            echo "<h3>" . date('l, M d', strtotime($slot['date'])) . "</h3>";
                        }
                        ?>
                        <div class="time-slot">
                            <span class="time">
                                <?= date('h:i A', strtotime($slot['start_time'])) ?> - 
                                <?= date('h:i A', strtotime($slot['end_time'])) ?>
                            </span>
                            <div class="slot-actions">
                                <a href="editavailability.php?id=<?= $slot['availability_id'] ?>" 
                                   class="btn-edit">Edit</a>
                                <a href="deleteavailability.php?id=<?= $slot['availability_id'] ?>" 
                                   onclick="return confirm('Are you sure you want to remove this availability?')" 
                                   class="btn-delete">Remove</a>
                            </div>
                        </div>
                        <?php
                    }
                    echo "</div>";
                } else {
                    echo "<p class='no-data'>No availability set for this therapist.</p>";
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html> 