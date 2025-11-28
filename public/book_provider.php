<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

require_once __DIR__ . '/../app/models/ServiceProvider.php';
require_once __DIR__ . '/../app/models/ProviderBooking.php';
require_once __DIR__ . '/../config/Database.php';

$database = new Database();
$db = $database->getConnection();
$serviceProvider = new ServiceProvider($db);
$providerBooking = new ProviderBooking($db);

$message = '';
$messageType = '';
$providerId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$providerId) {
    header('Location: service.php?tab=specific');
    exit;
}

$provider = $serviceProvider->getById($providerId);
if (!$provider) {
    header('Location: service.php?tab=specific&error=provider_not_found');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookingData = [
        'provider_id' => $providerId,
        'client_user_id' => $_SESSION['user_id'],
        'client_name' => filter_input(INPUT_POST, 'client_name', FILTER_SANITIZE_SPECIAL_CHARS),
        'client_email' => filter_input(INPUT_POST, 'client_email', FILTER_SANITIZE_EMAIL),
        'client_mobile' => filter_input(INPUT_POST, 'client_mobile', FILTER_SANITIZE_SPECIAL_CHARS),
        'service_type' => filter_input(INPUT_POST, 'service_type', FILTER_SANITIZE_SPECIAL_CHARS),
        'service_description' => filter_input(INPUT_POST, 'service_description', FILTER_SANITIZE_SPECIAL_CHARS),
        'preferred_date' => filter_input(INPUT_POST, 'preferred_date', FILTER_SANITIZE_SPECIAL_CHARS),
        'preferred_time' => filter_input(INPUT_POST, 'preferred_time', FILTER_SANITIZE_SPECIAL_CHARS),
        'estimated_duration' => filter_input(INPUT_POST, 'estimated_duration', FILTER_SANITIZE_SPECIAL_CHARS),
        'service_address' => filter_input(INPUT_POST, 'service_address', FILTER_SANITIZE_SPECIAL_CHARS),
        'client_vaccinated' => isset($_POST['client_vaccinated']) ? 1 : 0,
        'client_test_provided' => isset($_POST['client_test_provided']) ? 1 : 0,
        'mask_agreement' => isset($_POST['mask_agreement']) ? 1 : 0,
        'client_notes' => filter_input(INPUT_POST, 'client_notes', FILTER_SANITIZE_SPECIAL_CHARS)
    ];
    
    $result = $providerBooking->createBooking($bookingData);
    if ($result) {
        $message = 'Booking request submitted successfully! Reference: ' . $result['booking_ref'];
        $messageType = 'success';
    } else {
        $message = 'Failed to submit booking request. Please try again.';
        $messageType = 'danger';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Provider - HomeServices</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php"><i class="bi bi-house-door"></i> HomeServices</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="service.php?tab=specific"><i class="bi bi-arrow-left"></i> Back to Search</a>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <?php if ($message): ?>
        <div class="alert alert-<?= $messageType ?> alert-dismissible fade show">
            <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-person-badge"></i> Provider Details</h5>
                    </div>
                    <div class="card-body">
                        <h4><?= htmlspecialchars($provider['business_name']) ?></h4>
                        <p class="text-muted"><?= htmlspecialchars($provider['service_category']) ?></p>
                        <hr>
                        <p><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($provider['address']) ?></p>
                        <p><i class="bi bi-telephone"></i> <?= htmlspecialchars($provider['phone']) ?></p>
                        <hr>
                        <h6>COVID Safety Status</h6>
                        <?php if ($provider['covid_vaccinated']): ?>
                            <span class="badge bg-success"><i class="bi bi-shield-check"></i> Vaccinated</span>
                        <?php endif; ?>
                        <?php if ($provider['covid_safe_certified']): ?>
                            <span class="badge bg-info"><i class="bi bi-patch-check"></i> COVID-Safe Certified</span>
                        <?php endif; ?>
                        <hr>
                        <small class="text-muted">Working Hours: <?= htmlspecialchars($provider['working_hours_start']) ?> - <?= htmlspecialchars($provider['working_hours_end']) ?></small>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-calendar-plus"></i> Book This Provider</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Your Name *</label>
                                    <input type="text" class="form-control" name="client_name" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email *</label>
                                    <input type="email" class="form-control" name="client_email" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Mobile *</label>
                                    <input type="tel" class="form-control" name="client_mobile" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Service Type *</label>
                                    <input type="text" class="form-control" name="service_type" value="<?= htmlspecialchars($provider['service_category']) ?>" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Preferred Date *</label>
                                    <input type="date" class="form-control" name="preferred_date" required min="<?= date('Y-m-d') ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Preferred Time</label>
                                    <input type="time" class="form-control" name="preferred_time">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Est. Duration</label>
                                    <select class="form-select" name="estimated_duration">
                                        <option value="1 hour">1 Hour</option>
                                        <option value="2-3 hours">2-3 Hours</option>
                                        <option value="half day">Half Day</option>
                                        <option value="full day">Full Day</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Service Address *</label>
                                <textarea class="form-control" name="service_address" rows="2" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Service Description</label>
                                <textarea class="form-control" name="service_description" rows="3" placeholder="Describe what you need..."></textarea>
                            </div>
                            <div class="card bg-light mb-3">
                                <div class="card-body">
                                    <h6><i class="bi bi-shield-check"></i> COVID Safety Agreement</h6>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="client_vaccinated" id="client_vaccinated">
                                        <label class="form-check-label" for="client_vaccinated">I am fully vaccinated</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="client_test_provided" id="client_test_provided">
                                        <label class="form-check-label" for="client_test_provided">I can provide a negative COVID test if required</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="mask_agreement" id="mask_agreement">
                                        <label class="form-check-label" for="mask_agreement">I agree to wear a mask during service if required</label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Additional Notes</label>
                                <textarea class="form-control" name="client_notes" rows="2"></textarea>
                            </div>
                            <button type="submit" class="btn btn-success btn-lg w-100">
                                <i class="bi bi-check-circle"></i> Submit Booking Request
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
