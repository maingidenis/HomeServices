<?php
require_once __DIR__ . '/../../app/middleware/auth.php';
require_once __DIR__ . '/../../app/middleware/admin_only.php';
require_once __DIR__ . '/../../app/models/User.php';

$model = new User();
$users = $model->getAllUsers(); // we will add this method

?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<h1>Manage Users</h1>

<table border="1">
    <tr>
        <th>User ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Role</th>
        <th>Actions</th>
    </tr>

    <?php foreach ($users as $u): ?>
    <tr>
        <td><?= $u['user_id'] ?></td>
        <td><?= htmlspecialchars($u['name']) ?></td>
        <td><?= htmlspecialchars($u['email']) ?></td>
        <td><?= $u['role'] ?></td>
        <td>
            <?php if ($u['role'] === 'client'): ?>
                <a href="update_role.php?id=<?= $u['user_id'] ?>&role=admin">Promote to Admin</a>
            <?php else: ?>
                <a href="update_role.php?id=<?= $u['user_id'] ?>&role=client">Demote to Client</a>
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>

</table>

<br>
<a href="dashboard.php">Back to Dashboard</a>

</body>
</html>
