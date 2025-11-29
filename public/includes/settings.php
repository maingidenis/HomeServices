<?php
// includes/settings.php
require_once __DIR__ . '/../../app/middleware/mfa.php';


if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

require_once __DIR__ . '/../../app/models/User.php';

$pageTitle = 'Account Settings - Home Services';
include __DIR__ . '/header.php';
?>

<main class="container py-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-gear"></i> Account settings</h5>
                </div>
                <div class="card-body">
                    <!-- Nav pills for future settings sections -->
                    <ul class="nav nav-pills mb-3" id="settingsTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="general-tab" data-bs-toggle="pill"
                                    data-bs-target="#general" type="button" role="tab">
                                General
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="security-tab" data-bs-toggle="pill"
                                    data-bs-target="#security" type="button" role="tab">
                                Security
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="notifications-tab" data-bs-toggle="pill"
                                    data-bs-target="#notifications" type="button" role="tab">
                                Notifications
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- General -->
                        <div class="tab-pane fade show active" id="general" role="tabpanel">
                            <p class="text-muted">
                                This is a placeholder for general account preferences such as language, timezone, and default location.
                            </p>
                            <form>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Default city</label>
                                        <input type="text" class="form-control"
                                               placeholder="Adelaide (coming soon)" disabled>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Preferred radius</label>
                                        <select class="form-select" disabled>
                                            <option selected>10 km (coming soon)</option>
                                            <option>25 km</option>
                                            <option>50 km</option>
                                        </select>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-primary mt-3" disabled>
                                    Save changes (coming soon)
                                </button>
                            </form>
                        </div>

                        <!-- Security -->
                        <div class="tab-pane fade" id="security" role="tabpanel">
                            <p class="text-muted">
                                Security options like password change, MFA, and login alerts will be configured here.
                            </p>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control"
                                       placeholder="******** (change coming soon)" disabled>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="mfaToggle" disabled checked>
                                <label class="form-check-label" for="mfaToggle">
                                    Multi‑factor authentication enabled (managed elsewhere)
                                </label>
                            </div>
                            <button type="button" class="btn btn-outline-secondary" disabled>
                                Manage security (coming soon)
                            </button>
                        </div>

                        <!-- Notifications -->
                        <div class="tab-pane fade" id="notifications" role="tabpanel">
                            <p class="text-muted">
                                Control how you receive booking, reminder, and marketing notifications.
                            </p>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="emailNotif" disabled checked>
                                <label class="form-check-label" for="emailNotif">
                                    Email notifications (coming soon)
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="smsNotif" disabled>
                                <label class="form-check-label" for="smsNotif">
                                    SMS notifications (coming soon)
                                </label>
                            </div>
                            <button type="button" class="btn btn-primary mt-3" disabled>
                                Save preferences (coming soon)
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <a href="index.php?page=dashboard" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to dashboard
            </a>
        </div>
    </div>
</main>

<?php include __DIR__ . '/footer.php'; ?>
