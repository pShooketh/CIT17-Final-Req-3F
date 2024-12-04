<?php
include 'db_connect.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: listusers.php?error=invalid_id");
    exit();
}

$id = $_GET['id'];

try {
    $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->execute([$id]);
    header("Location: listusers.php?success=user_deleted");
    exit();
} catch (PDOException $e) {
    header("Location: listusers.php?error=delete_failed");
    exit();
}
?>