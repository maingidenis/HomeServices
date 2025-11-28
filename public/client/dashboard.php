<?php
require_once __DIR__ . '/../../app/middleware/auth.php';
require_once __DIR__ . '/../../app/middleware/client_only.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard - Home Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="bi bi-person-circle"></i> Client Portal
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#clientNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="clientNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../dashboard.php">
                            <i class="bi bi-arrow-left"></i> Main Dashboard
                        </a>
                    </li>
                </ul>
                <div class="d-flex">
                    <a class="btn btn-outline-light" href="../logout.php">
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
                <h2><i class="bi bi-person-badge"></i> Client Dashboard</h2>
                <p class="text-muted">Welcome, <?= htmlspecialchars($_SESSION['user_id']); ?> (Client)</p>
            </div>
        </div>

        <!-- Info Card -->
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="card bg-light h-100">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-info-circle"></i> Your Personal Area</h5>
                        <p class="card-text">You can view services, appointments, and notifications.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-lightning-fill"></i> Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <a href="../index.php?page=service" class="btn btn-outline-primary w-100">
                                    <i class="bi bi-tools"></i> View Services
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="../index.php?page=appointment" class="btn btn-outline-success w-100">
                                    <i class="bi bi-calendar-check"></i> My Appointments
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="../index.php?page=notification" class="btn btn-outline-warning w-100">
                                    <i class="bi bi-bell"></i> Notifications
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Back Links -->
        <div class="mt-4">
            <a href="../dashboard.php" class="btn btn-secondary me-2">
                <i class="bi bi-arrow-left"></i> Back
            </a>
            <a href="../logout.php" class="btn btn-outline-danger">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
