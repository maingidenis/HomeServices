<?php
require_once __DIR__ . '/../../app/middleware/auth.php';
require_once __DIR__ . '/../../app/middleware/client_only.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Client Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<h1>Client Dashboard</h1>
<p>Welcome, <?= htmlspecialchars($_SESSION['user_id']); ?> (Client)</p>

<div class="info-box">
    <h2>Your Personal Area</h2>
    <p>You can view services, appointments, and notifications.</p>
</div>

<br>

<a href="../dashboard.php">Back</a> |
<a href="../logout.php">Logout</a>

</body>
</html>
