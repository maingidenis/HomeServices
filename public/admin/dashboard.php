<?php
require_once __DIR__ . '/../../app/middleware/auth.php';
require_once __DIR__ . '/../../app/middleware/admin_only.php';
require_once __DIR__ . '/../../app/models/User.php';

$model = new User();
$totalUsers = $model->countAll();
$user = $model->findById($_SESSION['user_id']);


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Home Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-danger">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="bi bi-shield-lock-fill"></i> Admin Panel
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="adminNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_users.php">
                            <i class="bi bi-people"></i> Manage Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../dashboard.php">
                            <i class="bi bi-arrow-left"></i> Back to Main
                        </a>
                    </li>
                </ul>
                <div class="d-flex">
                    <a href="../logout.php" class="btn btn-danger">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4">
        <div class="row mb-4">
            <div class="col">
                <h2><i class="bi bi-shield-check"></i> Admin Dashboard</h2>
                <p class="text-muted">Welcome, <?= htmlspecialchars($user['name']); ?> (Admin)</p>
            </div>
        </div>

        <!-- Stats Card -->
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
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="bi bi-gear-fill"></i> Admin Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <a href="manage_users.php" class="btn btn-outline-primary w-100">
                                    <i class="bi bi-people"></i> Manage Users
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="../dashboard.php" class="btn btn-outline-secondary w-100">
                                    <i class="bi bi-house"></i> Main Dashboard
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="../logout.php" class="btn btn-outline-danger w-100">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
