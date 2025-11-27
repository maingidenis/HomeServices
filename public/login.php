<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../app/controllers/UserController.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $ctrl = new UserController();
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $user_id = $ctrl->loginUser($email, $password);

    if ($user_id) {
        // Save user ID in session
        $_SESSION['user_id'] = $user_id;

        // Redirect to dashboard
        header('Location: index.php?page=dashboard');
        exit;
    } else {
        $error = "Login failed: invalid email or password";
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
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="post" action="index.php?page=login">
    <input name="email" type="email" required placeholder="Email">
    <input name="password" type="password" required placeholder="Password">
    <button type="submit">Login</button>

            <!-- OAuth Login Buttons -->
        <div style="margin-top: 20px; text-align: center;">
            <p style="margin: 15px 0; color: #666;">Or sign in with:</p>
            
            <a href="oauth_login.php?provider=google" style="display: inline-block; padding: 12px 24px; margin: 5px; background-color: #4285F4; color: white; text-decoration: none; border-radius: 4px; font-weight: bold;">
                Sign in with Google
            </a>
            
            <a href="oauth_login.php?provider=facebook" style="display: inline-block; padding: 12px 24px; margin: 5px; background-color: #1877F2; color: white; text-decoration: none; border-radius: 4px; font-weight: bold;">
                Sign in with Facebook
            </a>
        </div>
</form>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
