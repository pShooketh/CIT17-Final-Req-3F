<?php
session_start();
include 'db_connect.php';

header('Content-Type: application/json');

if (!isset($_GET['therapist_id'], $_GET['date'], $_GET['duration'])) {
    echo json_encode(['error' => 'Missing parameters']);
    exit;
}

$therapist_id = $_GET['therapist_id'];
$date = $_GET['date'];
$duration = $_GET['duration'];

try {
    // Get therapist's availability
    $stmt = $pdo->prepare("
        SELECT start_time, end_time 
        FROM availability 
        WHERE therapist_id = ? AND date = ?
    ");
    $stmt->execute([$therapist_id, $date]);
    $availability = $stmt->fetchAll();

    // Get existing appointments
    $stmt = $pdo->prepare("
        SELECT start_time, end_time 
        FROM appointments 
        WHERE therapist_id = ? 
        AND appointment_date = ? 
        AND status != 'canceled'
    ");
    $stmt->execute([$therapist_id, $date]);
    $appointments = $stmt->fetchAll();

    $available_slots = [];
    
    foreach ($availability as $slot) {
        $current_time = strtotime($slot['start_time']);
        $end_time = strtotime($slot['end_time']);
        
        while ($current_time + ($duration * 60) <= $end_time) {
            $is_available = true;
            $slot_end = $current_time + ($duration * 60);
            
            // Check if slot conflicts with any appointment
            foreach ($appointments as $appointment) {
                $appt_start = strtotime($appointment['start_time']);
                $appt_end = strtotime($appointment['end_time']);
                
                if ($current_time < $appt_end && $slot_end > $appt_start) {
                    $is_available = false;
                    break;
                }
            }
            
            if ($is_available) {
                $available_slots[] = [
                    'time' => date('H:i:s', $current_time),
                    'formatted_time' => date('h:i A', $current_time)
                ];
            }
            
            $current_time += 30 * 60; // 30-minute intervals
        }
    }

    echo json_encode($available_slots);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?> 