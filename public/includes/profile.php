<?php
require_once __DIR__ . '/../../app/middleware/mfa.php';


if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

require_once __DIR__ . '/../../app/models/User.php';

$userModel = new User();
$user = $userModel->findById($_SESSION['user_id']) ?? [];

// Page title and header
$pageTitle = 'My Profile - Home Services';
include __DIR__ . '/header.php';
?>

<main class="container py-4">
    <div class="row g-4">
        <!-- Profile summary card -->
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <div class="mb-3">
                        <span class="rounded-circle bg-primary text-white d-inline-flex justify-content-center align-items-center"
                              style="width:80px;height:80px;font-size:32px;">
                            <?= isset($user['name']) ? strtoupper($user['name'][0]) : 'U' ?>
                        </span>
                    </div>
                    <h5 class="card-title mb-1">
                        <?= htmlspecialchars($user['name'] ?? 'Your Name') ?>
                    </h5>
                    <p class="text-muted mb-2">
                        <?= htmlspecialchars($user['email'] ?? 'you@example.com') ?>
                    </p>
                    <span class="badge bg-success mb-2">Verified User</span>
                    <p class="small text-muted mb-0">
                        This is your profile overview. Future enhancements can show role, rating, and activity here.
                    </p>
                </div>
            </div>
        </div>

        <!-- Profile details / editable fields (placeholder) -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-person"></i> Profile details</h5>
                    <span class="badge bg-secondary">Placeholder view</span>
                </div>
                <div class="card-body">
                    <form>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Full name</label>
                                <input type="text" class="form-control"
                                       value="<?= htmlspecialchars($user['name'] ?? '') ?>"
                                       placeholder="John Smith" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email address</label>
                                <input type="email" class="form-control"
                                       value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                                       placeholder="you@example.com" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="text" class="form-control"
                                       placeholder="+61 4xx xxx xxx (coming soon)" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Location</label>
                                <input type="text" class="form-control"
                                       placeholder="Adelaide, SA (coming soon)" disabled>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Short bio</label>
                                <textarea class="form-control" rows="3"
                                          placeholder="Add a short description about yourself (coming soon)" disabled></textarea>
                            </div>
                        </div>

                        <div class="mt-4 d-flex justify-content-between">
                            <a href="index.php?page=dashboard" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Back to dashboard
                            </a>
                            <button type="button" class="btn btn-primary" disabled>
                                <i class="bi bi-pencil-square"></i> Edit profile (coming soon)
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/footer.php'; ?>
