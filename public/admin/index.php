<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../../app/controllers/UserController.php';
require_once __DIR__ . '/../../app/models/User.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $ctrl = new UserController();

    // Raw input
    $rawEmail = $_POST['email'] ?? '';
    $rawPassword = $_POST['password'] ?? '';

    // Trim & sanitize
    $rawEmail = trim($rawEmail);
    $rawPassword = trim($rawPassword);

    // Validate email
    $email = filter_var($rawEmail, FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    }

    // Validate password
    if (empty($rawPassword)) {
        $error = "Password is required.";
    }

    if (empty($error)) {
        // Attempt login
        $userModel = new User();
        $user_id = $ctrl->loginUser($email, $rawPassword);

        if ($user_id) {
            // Fetch full user info
            $user = $userModel->findById($user_id);

            // ALLOW ONLY ADMINS
            if ($user['role'] !== 'admin') {
                $error = "Access denied. Admins only.";
            } else {
                // SUCCESS: Log in admin
                // Generate OTP
                $otp = random_int(100000, 999999);
                $expires = date("Y-m-d H:i:s", time() + 300);

                // Save OTP
                $userModel->setOTP($user_id, $otp, $expires);

                // Send MFA email
                require_once __DIR__ . '/../../app/services/Mailer.php';
                $mailer = new Mailer();
                $mailer->sendMail($user['email'], "Admin MFA OTP", "<h2>Your admin OTP: <b>$otp</b></h2>");

                // Store pending admin ID
                $_SESSION['pending_user_id'] = $user_id;

                header("Location: ../verify_otp.php");
                exit;

                // $_SESSION['user_id'] = $user_id;
                // $_SESSION['role'] = $user['role'];

                // header("Location: dashboard.php");
                // exit;
            }
        } else {
            $error = "Invalid admin credentials.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

<h2 class="text-center mb-4">Admin Login</h2>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="POST" action="index.php">

    <div class="mb-3">
        <label>Email</label>
        <input type="email"
               name="email"
               class="form-control"
               value="<?= isset($rawEmail) ? htmlspecialchars($rawEmail) : '' ?>">
    </div>

    <div class="mb-3">
        <label>Password</label>
        <input type="password"
               name="password"
               class="form-control">
    </div>

    <button class="btn btn-primary w-100" type="submit">Login</button>

</form>

</body>
</html>
