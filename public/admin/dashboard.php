<?php
require_once __DIR__ . '/../../app/middleware/auth.php';
require_once __DIR__ . '/../../app/middleware/admin_only.php';
require_once __DIR__ . '/../../app/models/User.php';

$model = new User();
$totalUsers = $model->countAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<h1>Admin Dashboard</h1>
<p>Welcome, <?= htmlspecialchars($_SESSION['user_id']); ?> (Admin)</p>

<div class="stats-box">
    <h2>System Stats</h2>
    <p>Total Users: <?= $totalUsers ?></p>
</div>

<br>

<a href="manage_users.php">Manage Users</a> | 
<a href="../dashboard.php">Back</a> | 
<a href="../logout.php">Logout</a>

</body>
</html>
