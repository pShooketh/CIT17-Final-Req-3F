<?php
include 'db_connect.php';

$stmt = $pdo->query("SELECT * FROM users");
$users = $stmt->fetchAll();
?>

<a href="create.php">Add User</a>
<table>
    <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($users as $user): ?>
    <tr>
        <td><?= htmlspecialchars($user['name']) ?></td>
        <td><?= htmlspecialchars($user['email']) ?></td>
        <td>
            <a href="edit.php?id=<?= $user['id'] ?>">Edit</a>
            <a href="delete.php?id=<?= $user['id'] ?>">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>