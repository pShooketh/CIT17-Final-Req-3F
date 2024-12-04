<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $role = $_POST['role'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO users (full_name, email, phone_number, password, role) 
                              VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$full_name, $email, $phone, $password, $role]);
        header("Location: listusers.php?success=user_created");
        exit();
    } catch (PDOException $e) {
        $error = "Creation failed: " . $e->getMessage();
    }
}
?>

<div class="container">
    <a href="index.php" class="btn btn-home">Home</a>
    
    <h1>Create New User</h1>
    
    <?php if (isset($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Full Name:</label>
            <input type="text" name="full_name" required>
        </div>

        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email" required>
        </div>

        <div class="form-group">
            <label>Phone:</label>
            <input type="tel" name="phone">
        </div>

        <div class="form-group">
            <label>Password:</label>
            <input type="password" name="password" required>
        </div>

        <div class="form-group">
            <label>Role:</label>
            <select name="role" required>
                <option value="customer">Customer</option>
                <option value="therapist">Therapist</option>
                <option value="admin">Admin</option>
            </select>
        </div>

        <div class="form-group">
            <button type="submit" class="btn">Create User</button>
            <a href="listusers.php" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>