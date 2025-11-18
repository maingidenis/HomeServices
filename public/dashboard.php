<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
// Authorization check
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

// Collect stats using your models
require_once __DIR__ . '/../app/models/User.php';
require_once __DIR__ . '/../app/models/Appointment.php';
require_once __DIR__ . '/../app/models/Service.php';

$userModel = new User();
$appointmentModel = new Appointment();
$serviceModel = new Service();

$totalUsers = $userModel->countAll();
$totalAppointments = $appointmentModel->countAll();
$totalServices = $serviceModel->countAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Home Services App</a>
    <div>
      <a class="btn btn-outline-dark" href="index.php?page=logout">Logout</a>
    </div>
  </div>
</nav>

<div class="container">
    <div class="row text-center mb-4">
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-body">
                    <h3 class="card-title"><?= $totalUsers ?></h3>
                    <p class="card-text">Total Registered Users</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-body">
                    <h3 class="card-title"><?= $totalAppointments ?></h3>
                    <p class="card-text">Total Appointments</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-body">
                    <h3 class="card-title"><?= $totalServices ?></h3>
                    <p class="card-text">Total Services</p>
                </div>
            </div>
        </div>
    </div>
    <nav class="nav justify-content-center mb-3">
      <a class="nav-link" href="index.php?page=appointment">Appointments</a>
      <a class="nav-link" href="index.php?page=service">Services</a>
      <a class="nav-link" href="index.php?page=visitlog">Visit Logs</a>
      <a class="nav-link" href="index.php?page=notification">Notifications</a>
    </nav>
    <hr>
    <h4>Welcome to your dashboard!</h4>
    <p>Use the menu above to view and manage your home services and contact tracing info.</p>
</div>
</body>
</html>
