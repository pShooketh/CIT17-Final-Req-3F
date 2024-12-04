<?php
include 'db_connect.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: listusers.php?error=invalid_id");
    exit();
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    header("Location: listusers.php?error=user_not_found");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $role = $_POST['role'];
    
    try {
        $stmt = $pdo->prepare("UPDATE users SET 
            full_name = ?, 
            email = ?, 
            phone_number = ?, 
            role = ? 
            WHERE user_id = ?");
            
        $stmt->execute([$full_name, $email, $phone, $role, $id]);
        header("Location: listusers.php?success=user_updated");
        exit();
    } catch (PDOException $e) {
        $error = "Update failed: " . $e->getMessage();
    }
}
?>

<div class="container">
    <a href="index.php" class="btn btn-home">Home</a>
    
    <h1>Edit User</h1>
    
    <?php if (isset($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Full Name:</label>
            <input type="text" name="full_name" 
                   value="<?= htmlspecialchars($user['full_name']) ?>" required>
        </div>

        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email" 
                   value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>

        <div class="form-group">
            <label>Phone:</label>
            <input type="tel" name="phone" 
                   value="<?= htmlspecialchars($user['phone_number']) ?>">
        </div>

        <div class="form-group">
            <label>Role:</label>
            <select name="role" required>
                <option value="customer" <?= $user['role'] === 'customer' ? 'selected' : '' ?>>
                    Customer
                </option>
                <option value="therapist" <?= $user['role'] === 'therapist' ? 'selected' : '' ?>>
                    Therapist
                </option>
                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>
                    Admin
                </option>
            </select>
        </div>

        <div class="form-group">
            <button type="submit" class="btn">Update User</button>
            <a href="listusers.php" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>