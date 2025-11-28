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
    $email = filter_var($rawEmail, FILTER_SANITIZE_EMAIL);      // remove illegal chars [web:12][web:16]
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {           // ensure proper email format [web:13][web:19]
        $error = "Please enter a valid email address.";
    }

    // Validate password (server-side, before using it)
    // Do NOT strip characters from the password, only validate. [web:56][web:70]
    if (empty($rawPassword)) {
        $error = "Password is required.";
    } elseif (strlen($rawPassword) < 8) {
        $error = "Password must be at least 8 characters long.";
    } elseif (
        !preg_match('/[A-Z]/', $rawPassword) ||   // at least one uppercase [web:56][web:67]
        !preg_match('/[a-z]/', $rawPassword) ||   // at least one lowercase [web:56]
        !preg_match('/[0-9]/', $rawPassword) ||   // at least one digit [web:56]
        !preg_match('/[^a-zA-Z0-9]/', $rawPassword) // at least one special char [web:56][web:58]
    ) {
        $error = "Password must include upper, lower, number and special character.";
    }

    if (empty($error)) {
        // Use the original (trimmed) password, not modified
        $password = $rawPassword;

        // Delegate authentication to controller (should use prepared statements and password_hash) [web:21][web:29]
        $user_id = $ctrl->loginUser($email, $password);

        if ($user_id) {
            // Generate OTP
            $otp = random_int(100000, 999999);
            $expires = date("Y-m-d H:i:s", time() + 300); // expires in 5 minutes
            $model = new User();
            // Save OTP
            $model->setOTP($user_id, $otp, $expires);

            // Send email using PHPMailer
            require_once __DIR__ . '/../app/services/Mailer.php';
            $mailer = new Mailer();
            $mailer->sendMail($email, "Your Login OTP", "<h2>Your OTP: <b>$otp</b></h2>");

            // Store pending ID
            $_SESSION['pending_user_id'] = $user_id;

            // Redirect to OTP verification page
            header("Location: verify_otp.php");
            exit;

            // $_SESSION['user_id'] = $user_id;
            // // Fetch role
            // $model = new User();
            // $user = $model->findById($user_id);
            // $_SESSION['role'] = $user['role'];

            // // redirect based on role
            // if ($user['role'] === 'client') {
            //     header("Location: dashboard.php"); // or admin-specific page
            // }
            // exit;
        }
        else {
            $error = "Invalid email or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body>
<?php if (!empty($error)): ?>
    <!-- Escape output to prevent XSS in error messages -->
    <p style="color:red;"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p> <!-- [web:18][web:21] -->
<?php endif; ?>

<form method="post" action="index.php?page=login">
    <!-- Use value attribute with escaped content if you want to preserve email input on error -->
    <label for="email">Email</label>
    <input name="email"
           type="email"
           placeholder="Email"
           value="<?= isset($rawEmail) ? htmlspecialchars($rawEmail, ENT_QUOTES, 'UTF-8') : '' ?>"><!-- [web:18][web:25] -->

    <label for="password">Password</label>
    <input name="password"
           type="password"
           placeholder="Password">
    <button
        class="btn btn-primary"
        type="submit">Login</button>

    <!-- OAuth Login Buttons -->
    <div style="margin-top: 20px; text-align: center;">
        <p style="margin: 15px 0; color: #666;">Or sign in with:</p>

        <a href="oauth_login.php?provider=google"
           style="display: inline-block; padding: 12px 24px; margin: 5px; background-color: #4285F4; color: white; text-decoration: none; border-radius: 4px; font-weight: bold;">
            Sign in with Google
        </a>

        <a href="oauth_login.php?provider=facebook"
           style="display: inline-block; padding: 12px 24px; margin: 5px; background-color: #1877F2; color: white; text-decoration: none; border-radius: 4px; font-weight: bold;">
            Sign in with Facebook
        </a>
    </div>
</form>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
