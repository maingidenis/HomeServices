<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

// Sample visit log data (in real implementation, fetch from database)
$visitLogs = [
    [
        'id' => 1,
        'service' => 'Plumbing Service',
        'technician' => 'John Smith',
        'date' => '2024-11-25',
        'time' => '10:00 AM',
        'status' => 'completed',
        'address' => '123 Main Street, Adelaide',
        'notes' => 'Fixed kitchen sink leak'
    ],
    [
        'id' => 2,
        'service' => 'Electrical Repair',
        'technician' => 'Sarah Johnson',
        'date' => '2024-11-26',
        'time' => '2:00 PM',
        'status' => 'pending',
        'address' => '456 Oak Avenue, Adelaide',
        'notes' => 'Install ceiling fan'
    ],
    [
        'id' => 3,
        'service' => 'AC Maintenance',
        'technician' => 'Mike Davis',
        'date' => '2024-11-20',
        'time' => '9:00 AM',
        'status' => 'completed',
        'address' => '789 Pine Road, Adelaide',
        'notes' => 'Annual AC checkup and cleaning'
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visit Log - Home Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="index.php?page=dashboard">
                <i class="bi bi-house-door-fill"></i> HomeServices
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?page=dashboard"><i class="bi bi-speedometer2"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?page=service"><i class="bi bi-tools"></i> Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php?page=visitlog"><i class="bi bi-calendar-check"></i> Visit Log</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?page=notification"><i class="bi bi-bell"></i> Notifications</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?page=logout"><i class="bi bi-box-arrow-right"></i> Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container my-5">
        <div class="row mb-4">
            <div class="col-md-12">
                <h2 class="mb-3"><i class="bi bi-calendar-check text-primary"></i> Visit Log</h2>
                <p class="text-muted">Track all your service visits and appointments</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Filter by Status</label>
                                <select class="form-select">
                                    <option value="">All Statuses</option>
                                    <option value="completed">Completed</option>
                                    <option value="pending">Pending</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">From Date</label>
                                <input type="date" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">To Date</label>
                                <input type="date" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Visit Log Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-list-ul"></i> Recent Visits</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Service</th>
                                <th>Technician</th>
                                <th>Date & Time</th>
                                <th>Address</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($visitLogs as $log): ?>
                            <tr>
                                <td>#<?php echo $log['id']; ?></td>
                                <td>
                                    <i class="bi bi-tools text-primary"></i>
                                    <?php echo $log['service']; ?>
                                </td>
                                <td>
                                    <i class="bi bi-person-circle"></i>
                                    <?php echo $log['technician']; ?>
                                </td>
                                <td>
                                    <small class="d-block"><?php echo $log['date']; ?></small>
                                    <small class="text-muted"><?php echo $log['time']; ?></small>
                                </td>
                                <td>
                                    <small><?php echo $log['address']; ?></small>
                                </td>
                                <td>
                                    <?php 
                                    $statusClass = $log['status'] === 'completed' ? 'completed' : 
                                                 ($log['status'] === 'pending' ? 'pending' : 'cancelled');
                                    ?>
                                    <span class="visit-status <?php echo $statusClass; ?>">
                                        <?php echo ucfirst($log['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#detailsModal<?php echo $log['id']; ?>">
                                        <i class="bi bi-eye"></i> View
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination justify-content-center">
                <li class="page-item disabled">
                    <a class="page-link" href="#">Previous</a>
                </li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item">
                    <a class="page-link" href="#">Next</a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Footer -->
    <footer class="footer mt-5">
        <div class="container">
            <div class="text-center">
                <p class="mb-0">&copy; 2024 HomeServices. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
