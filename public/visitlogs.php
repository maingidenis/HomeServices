<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ini_set('display_errors', 1);


require_once __DIR__ . '/../app/middleware/mfa.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

require_once __DIR__ . '/../app/models/User.php';

$userModel   = new User();
$currentUser = $userModel->findById($_SESSION['user_id']) ?? [];

$currentName  = $currentUser['name']  ?? 'Current User';
$currentEmail = $currentUser['email'] ?? 'you@example.com';

$visitLogs = [
    [
        'name'    => $currentName,
        'email'   => $currentEmail,
        'role'    => 'You',
        'service' => 'Plumbing inspection',
        'date'    => '2025-11-29',
        'time'    => '10:15',
        'status'  => 'Completed'
    ],
    [
        'name'    => 'Amelia Green',
        'email'   => 'amelia.green@example.com',
        'role'    => 'Customer',
        'service' => 'Garden maintenance',
        'date'    => '2025-11-28',
        'time'    => '14:30',
        'status'  => 'Scheduled'
    ],
    [
        'name'    => 'Liam Patel',
        'email'   => 'liam.patel@example.com',
        'role'    => 'Provider',
        'service' => 'Electrical safety check',
        'date'    => '2025-11-27',
        'time'    => '09:00',
        'status'  => 'Completed'
    ],
    [
        'name'    => 'Sophia Ng',
        'email'   => 'sophia.ng@example.com',
        'role'    => 'Customer',
        'service' => 'General handyman visit',
        'date'    => '2025-11-26',
        'time'    => '16:45',
        'status'  => 'Cancelled'
    ],
];

$pageTitle = 'Visit Logs - Home Services';
include __DIR__ . '/includes/header.php';
?>

<main class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0"><i class="bi bi-journal-text"></i> Visit logs</h4>
        <a href="index.php?page=dashboard" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back to dashboard
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">Recent visits</h5>
                    <small class="text-muted">Showing activity for you and other users</small>
                </div>
                <span class="badge bg-secondary">Placeholder data</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">User</th>
                            <th scope="col">Role</th>
                            <th scope="col">Service</th>
                            <th scope="col">Date</th>
                            <th scope="col">Time</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($visitLogs as $log): ?>
                            <tr>
                                <td>
                                    <div class="fw-semibold">
                                        <?= htmlspecialchars($log['name']) ?>
                                        <?php if ($log['name'] === $currentName): ?>
                                            <span class="badge bg-primary ms-1">You</span>
                                        <?php endif; ?>
                                    </div>
                                    <small class="text-muted"><?= htmlspecialchars($log['email']) ?></small>
                                </td>
                                <td><?= htmlspecialchars($log['role']) ?></td>
                                <td><?= htmlspecialchars($log['service']) ?></td>
                                <td><?= htmlspecialchars($log['date']) ?></td>
                                <td><?= htmlspecialchars($log['time']) ?></td>
                                <td>
                                    <?php
                                    $status = $log['status'];
                                    $badgeClass = 'bg-secondary';
                                    if ($status === 'Completed') $badgeClass = 'bg-success';
                                    elseif ($status === 'Scheduled') $badgeClass = 'bg-info';
                                    elseif ($status === 'Cancelled') $badgeClass = 'bg-danger';
                                    ?>
                                    <span class="badge <?= $badgeClass ?>">
                                        <?= htmlspecialchars($status) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($visitLogs)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    No visit logs to display yet.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer text-end">
            <button type="button" class="btn btn-outline-primary btn-sm" disabled>
                <i class="bi bi-download"></i> Export (coming soon)
            </button>
        </div>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>