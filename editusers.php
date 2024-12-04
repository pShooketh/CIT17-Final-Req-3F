<?php
include 'db_connect.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];

    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
    $stmt->execute([$name, $email, $id]);
    header("Location: index.php");
}
?>

<form method="POST">
    Name: <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
    Email: <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
    <button type="submit">Update User</button>
</form>