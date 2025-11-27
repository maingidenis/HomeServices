<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

// Placeholder for Notification feature
// This will be implemented in a future update
?>
<!DOCTYPE html>
<html>
<head>
    <title>Notifications - Home Services</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include '../app/views/header.php'; ?>
    
    <div class="container">
        <h1>Notifications</h1>
        <p>This feature is currently under development.</p>
        <p>Notification system will be available soon.</p>
        <a href="index.php?page=dashboard" class="btn">Back to Dashboard</a>
    </div>
    
    <?php include '../app/views/footer.php'; ?>
</body>
</html>
