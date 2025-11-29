<?php
/**
 * Dashboard Page
 * 
 * Main dashboard for authenticated users showing stats and quick actions.
 * Uses shared header and footer includes for consistent styling.
 */

require_once __DIR__ . '/../app/middleware/mfa.php';

// Session is started by header.php include

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
        }
    }
    return 0;
}

$totalUsers = safeCount($userModel);
$totalAppointments = safeCount($appointmentModel);
$totalServices = safeCount($serviceModel);

// Set page title and include header
$pageTitle = 'Dashboard - Home Services';
include 'includes/header.php';
?>

<!-- Main Content -->
<main class="container py-4 flex-grow-1">
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
                        <i class="bi bi-people fs-1 opacity-50"></i>
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
                        <i class="bi bi-calendar-check fs-1 opacity-50"></i>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <small>All bookings</small>
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
                            <a href="index.php?page=service" class="btn btn-outline-primary w-100">
                                <i class="bi bi-search"></i> Browse Services
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="index.php?page=appointment" class="btn btn-outline-success w-100">
                                <i class="bi bi-calendar-plus"></i> Book Appointment
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
</main>

<?php include 'includes/footer.php'; ?>
