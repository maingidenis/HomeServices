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
<html>
<head>
<title>Verify OTP</title>
</head>
<body>

<h2>Enter the OTP sent to your email</h2>

<?php if ($error): ?>
<div style="color:red;"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="post">
    <input type="text" name="otp" placeholder="6-digit code" required>
    <button type="submit">Verify</button>
</form>

</body>
</html>
