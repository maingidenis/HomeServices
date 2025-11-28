<?php
require_once __DIR__ . '/../app/middleware/mfa.php';

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
require_once __DIR__ . '/../app/middleware/mfa.php';

$userModel = new User();
$appointmentModel = new Appointment();
$serviceModel = new Service();

function safeCount($model, $methods = ['countAll', 'count', 'getCount', 'total']) {
    foreach ($methods as $m) {
        if (method_exists($model, $m)) {
            $res = $model->$m();
            if (is_numeric($res)) {
                return (int)$res;
            }
            if (is_array($res) || $res instanceof Countable) {
                return count($res);
            }
        }
    }
    if (method_exists($model, 'getAll')) {
        $res = $model->getAll();
        if (is_array($res) || $res instanceof Countable) {
            return count($res);
        }
    }
    return 0;
}

$totalUsers = safeCount($userModel);
$totalAppointments = safeCount($appointmentModel);
$totalServices = safeCount($serviceModel);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Home Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="bi bi-house-door-fill"></i> Home Services
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php?page=dashboard">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?page=service">
                            <i class="bi bi-tools"></i> Services
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?page=appointment">
                            <i class="bi bi-calendar-check"></i> Appointments
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?page=visitlog">
                            <i class="bi bi-journal-text"></i> Visit Logs
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?page=notification">
                            <i class="bi bi-bell"></i> Notifications
                        </a>
                    </li>
                </ul>
                <div class="d-flex">
                    <a class="btn btn-outline-light" href="index.php?page=logout">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4">
        <div class="row mb-4">
            <div class="col">
                <h2><i class="bi bi-speedometer2"></i> Dashboard</h2>
                <p class="text-muted">Welcome to your Home Services dashboard!</p>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-subtitle mb-2">Total Users</h6>
                                <h2 class="card-title mb-0"><?= $totalUsers ?></h2>
                            </div>
                            <i class="bi bi-people-fill fs-1 opacity-50"></i>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <small>Registered users</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-subtitle mb-2">Total Appointments</h6>
                                <h2 class="card-title mb-0"><?= $totalAppointments ?></h2>
                            </div>
                            <i class="bi bi-calendar-check-fill fs-1 opacity-50"></i>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <small>All appointments</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-subtitle mb-2">Total Services</h6>
                                <h2 class="card-title mb-0"><?= $totalServices ?></h2>
                            </div>
                            <i class="bi bi-tools fs-1 opacity-50"></i>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <small>Available services</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-lightning-fill"></i> Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <a href="index.php?page=appointment" class="btn btn-outline-primary w-100">
                                    <i class="bi bi-plus-circle"></i> New Appointment
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="index.php?page=service" class="btn btn-outline-success w-100">
                                    <i class="bi bi-plus-circle"></i> Add Service
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="index.php?page=visitlog" class="btn btn-outline-info w-100">
                                    <i class="bi bi-journal-plus"></i> Log Visit
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="index.php?page=notification" class="btn btn-outline-warning w-100">
                                    <i class="bi bi-bell"></i> Notifications
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer mt-5 py-3 bg-light">
        <div class="container text-center">
            <span class="text-muted">&copy; 2024 Home Services. All rights reserved.</span>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
