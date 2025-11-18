<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once __DIR__ . '/../app/controllers/UserController.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ctrl = new UserController();
    $user_id = $ctrl->loginUser($_POST['email'], $_POST['password']);
    if ($user_id) {
        $_SESSION['user_id'] = $user_id;
        header('Location: index.php?page=dashboard');
        exit;
    } else {
        echo "Login failed";
    }
}
?>
<form method="post">
    <input name="email" type="email" required>
    <input name="password" type="password" required>
    <button type="submit">Login</button>
</form>
