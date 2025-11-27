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
</head>
<body>
<?php if (!empty($error)): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="post" action="index.php?page=login">
    <input name="email" type="email" required placeholder="Email">
    <input name="password" type="password" required placeholder="Password">
    <button type="submit">Login</button>
</form>
</body>
</html>
