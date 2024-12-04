<?php
session_start();
include 'db_connect.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: services.php?error=invalid_id");
    exit();
}

$id = $_GET['id'];

try {
    // Check if service is used in any appointments
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM appointments 
        WHERE service_id = ?
    ");
    $stmt->execute([$id]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        header("Location: services.php?error=service_in_use");
        exit();
    }

    // Delete the service if not in use
    $stmt = $pdo->prepare("DELETE FROM services WHERE service_id = ?");
    $stmt->execute([$id]);
    header("Location: services.php?success=service_deleted");
    exit();
} catch (PDOException $e) {
    header("Location: services.php?error=delete_failed");
    exit();
}
?> 