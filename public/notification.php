<?php
//session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

// Sample notification data (in real implementation, fetch from database)
$notifications = [
    [
        'id' => 1,
        'type' => 'appointment',
        'title' => 'Upcoming Appointment',
        'message' => 'Your plumbing service appointment is scheduled for tomorrow at 10:00 AM',
        'time' => '2 hours ago',
        'read' => false,
        'icon' => 'calendar-check'
    ],
    [
        'id' => 2,
        'type' => 'payment',
        'title' => 'Payment Received',
        'message' => 'We have received your payment of $125.50 for Service #1234',
        'time' => '5 hours ago',
        'read' => true,
        'icon' => 'credit-card'
    ],
    [
        'id' => 3,
        'type' => 'service',
        'title' => 'Service Completed',
        'message' => 'Your AC maintenance service has been completed successfully',
        'time' => '1 day ago',
        'read' => true,
        'icon' => 'check-circle'
    ],
    [
        'id' => 4,
        'type' => 'reminder',
        'title' => 'Service Reminder',
        'message' => 'Your annual HVAC maintenance is due next month',
        'time' => '2 days ago',
        'read' => false,
        'icon' => 'bell'
    ],
    [
        'id' => 5,
        'type' => 'promotion',
        'title' => 'Special Offer',
        'message' => 'Get 20% off on your next electrical service booking',
        'time' => '3 days ago',
        'read' => true,
        'icon' => 'gift'
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - Home Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
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
                        <a class="nav-link" href="index.php?page=visitlog"><i class="bi bi-calendar-check"></i> Visit Log</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php?page=notification"><i class="bi bi-bell"></i> Notifications</a>
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
            <div class="col-md-8">
                <h2 class="mb-3"><i class="bi bi-bell text-primary"></i> Notifications</h2>
                <p class="text-muted">Stay updated with your service activities</p>
            </div>
            <div class="col-md-4 text-end">
                <button class="btn btn-outline-primary">
                    <i class="bi bi-check-all"></i> Mark All as Read
                </button>
            </div>
        </div>

        <!-- Notification Stats -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card dashboard-card">
                    <div class="card-body text-center">
                        <i class="bi bi-bell-fill text-primary" style="font-size: 2rem;"></i>
                        <h3 class="stat-value mt-2"><?php echo count(array_filter($notifications, fn($n) => !$n['read'])); ?></h3>
                        <p class="stat-label mb-0">Unread</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card dashboard-card accent">
                    <div class="card-body text-center">
                        <i class="bi bi-envelope-open text-warning" style="font-size: 2rem;"></i>
                        <h3 class="stat-value mt-2" style="color: #f97316;"><?php echo count(array_filter($notifications, fn($n) => $n['read'])); ?></h3>
                        <p class="stat-label mb-0">Read</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card dashboard-card success">
                    <div class="card-body text-center">
                        <i class="bi bi-inbox-fill text-success" style="font-size: 2rem;"></i>
                        <h3 class="stat-value mt-2" style="color: #10b981;"><?php echo count($notifications); ?></h3>
                        <p class="stat-label mb-0">Total</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="row">
            <div class="col-md-12">
                <?php foreach ($notifications as $notification): ?>
                <div class="notification-item <?php echo !$notification['read'] ? 'unread' : ''; ?>">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <div class="d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: <?php echo !$notification['read'] ? '#fff7ed' : '#f3f4f6'; ?>; border-radius: 50%;">
                                <i class="bi bi-<?php echo $notification['icon']; ?>" style="font-size: 1.5rem; color: <?php echo !$notification['read'] ? '#f97316' : '#6b7280'; ?>;"></i>
                            </div>
                        </div>
                        <div class="col">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="mb-1" style="font-weight: 600;"><?php echo $notification['title']; ?></h5>
                                    <p class="mb-1 text-muted"><?php echo $notification['message']; ?></p>
                                    <small class="text-muted">
                                        <i class="bi bi-clock"></i> <?php echo $notification['time']; ?>
                                    </small>
                                </div>
                                <div>
                                    <?php if (!$notification['read']): ?>
                                    <span class="badge bg-primary">New</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger ms-2">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Empty State (hidden when there are notifications) -->
        <?php if (empty($notifications)): ?>
        <div class="text-center py-5">
            <i class="bi bi-inbox" style="font-size: 4rem; color: #e5e7eb;"></i>
            <h4 class="mt-3 text-muted">No notifications yet</h4>
            <p class="text-muted">You're all caught up! Check back later for updates.</p>
        </div>
        <?php endif; ?>

        <!-- Load More Button -->
        <?php if (!empty($notifications)): ?>
        <div class="text-center mt-4">
            <button class="btn btn-outline-primary">
                <i class="bi bi-arrow-down-circle"></i> Load More Notifications
            </button>
        </div>
        <?php endif; ?>
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
