<?php
include 'db_connect.php';

$stmt = $pdo->query("SELECT * FROM users");
$users = $stmt->fetchAll();
?>

<div class="container">
    <a href="index.php" class="btn btn-home">Home</a>
    
    <h1>User Management</h1>
    <a href="createuser.php" class="btn">Add New User</a>
    
    <table>
        <tr>
            <th>Full Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($users as $user): ?>
        <tr>
            <td><?= htmlspecialchars($user['full_name']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td><?= htmlspecialchars($user['phone_number']) ?></td>
            <td><?= htmlspecialchars(ucfirst($user['role'])) ?></td>
            <td>
                <a href="editusers.php?id=<?= $user['user_id'] ?>" class="btn-edit">Edit</a>
                <a href="deleteuser.php?id=<?= $user['user_id'] ?>" 
                   onclick="return confirm('Are you sure you want to delete this user?')" 
                   class="btn-delete">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>