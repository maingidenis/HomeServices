<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../app/controllers/UserController.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $ctrl = new UserController();

    // Trim raw input
    $rawEmail = $_POST['email'] ?? '';
    $rawPassword = $_POST['password'] ?? '';

    // Basic normalization
    $rawEmail = trim($rawEmail);
    $rawPassword = trim($rawPassword);

    // Sanitize and validate email
    $email = filter_var($rawEmail, FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    }

    // Validate password (server-side, before using it)
    if (empty($rawPassword)) {
        $error = "Password is required.";
    } elseif (strlen($rawPassword) < 8) {
        $error = "Password must be at least 8 characters long.";
    } elseif (
        !preg_match('/[A-Z]/', $rawPassword) ||
        !preg_match('/[a-z]/', $rawPassword) ||
        !preg_match('/[0-9]/', $rawPassword) ||
        !preg_match('/[^a-zA-Z0-9]/', $rawPassword)
    ) {
        $error = "Password must include upper, lower, number and special character.";
    }

    if (empty($error)) {
        $password = $rawPassword;
        $user_id = $ctrl->loginUser($email, $password);

        if ($user_id) {
            // Generate OTP
            $otp = random_int(100000, 999999);
            $expires = date("Y-m-d H:i:s", time() + 300);
            $model = new User();
            $model->setOTP($user_id, $otp, $expires);

            // Send email using PHPMailer
            require_once __DIR__ . '/../app/services/Mailer.php';
            $mailer = new Mailer();
            $mailer->sendMail($email, "Your Login OTP", "<h2>Your OTP: <b>$otp</b></h2>");

            $_SESSION['pending_user_id'] = $user_id;
            header("Location: verify_otp.php");
            exit;
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Home Services</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                            <i class="bi bi-house-door-fill text-primary"></i> Login
                        </h3>
                        
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="post" action="index.php?page=login">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" 
                                       class="form-control" 
                                       id="email" 
                                       name="email" 
                                       placeholder="Enter your email"
                                       value="<?= isset($rawEmail) ? htmlspecialchars($rawEmail, ENT_QUOTES, 'UTF-8') : '' ?>"
                                       required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" 
                                       class="form-control" 
                                       id="password" 
                                       name="password" 
                                       placeholder="Enter your password"
                                       required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 mb-3">
                                <i class="bi bi-box-arrow-in-right"></i> Login
                            </button>
                        </form>
                        
                        <hr>
                        <p class="text-center text-muted mb-3">Or sign in with:</p>
                        <div class="d-grid gap-2">
                            <a href="oauth_login.php?provider=google" class="btn btn-outline-danger">
                                <i class="bi bi-google"></i> Sign in with Google
                            </a>
                            <a href="oauth_login.php?provider=facebook" class="btn btn-outline-primary">
                                <i class="bi bi-facebook"></i> Sign in with Facebook
                            </a>
                        </div>
                        
                        <hr>
                        <p class="text-center mb-0">
                            Don't have an account? <a href="index.php?page=register">Register here</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
