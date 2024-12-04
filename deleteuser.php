<?php
include 'db_connect.php';

// Validate if ID exists and is numeric
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // Redirect to index page with an error message instead of showing "Invalid user ID"
    header("Location: index.php?error=invalid_id");
    exit();
}

$id = $_GET['id'];

try {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: index.php?success=user_deleted");
    exit();
} catch (PDOException $e) {
    header("Location: index.php?error=database_error");
    exit();
}
?>