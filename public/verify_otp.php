<?php
session_start();

require_once __DIR__ . '/../app/models/User.php';

$error = '';

if (!isset($_SESSION['pending_user_id'])) {
    header("Location: index.php?page=login");
    exit;
}

$user_id = $_SESSION['pending_user_id'];
$model = new User();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = trim($_POST['otp']);

    if ($model->verifyOTP($user_id, $otp)) {
        // Fetch full user
        $user = $model->findById($user_id);

        // Mark MFA success
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['MFA_AUTHENTICATED'] = true;

        unset($_SESSION['pending_user_id']);

        // ADMIN vs CLIENT redirect
        if ($user['role'] === 'admin') {
            header("Location: admin/dashboard.php");
        } else {
            header("Location: index.php?page=dashboard");
        }
        exit;
    } else {
        $error = "Invalid or expired OTP.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP - Home Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-5 col-lg-4">
                <div class="card shadow">
                    <div class="card-body p-4">
                        <h3 class="card-title text-center mb-4">
                            <i class="bi bi-shield-lock-fill text-primary"></i> Verify OTP
                        </h3>
                        <p class="text-center text-muted mb-4">
                            Enter the 6-digit code sent to your email
                        </p>
                        
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($error) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="post">
                            <div class="mb-4">
                                <label for="otp" class="form-label">OTP Code</label>
                                <input type="text" 
                                       class="form-control form-control-lg text-center" 
                                       id="otp" 
                                       name="otp" 
                                       placeholder="000000"
                                       maxlength="6"
                                       pattern="[0-9]{6}"
                                       required
                                       autofocus>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 mb-3">
                                <i class="bi bi-check-circle"></i> Verify
                            </button>
                        </form>
                        
                        <hr>
                        <p class="text-center text-muted mb-0">
                            <small>Didn't receive the code? <a href="index.php?page=login">Try again</a></small>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
