<?php
session_start();
require_once '/../app/controllers/UserController.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ctrl = new UserController();
    $user_id = $ctrl->loginUser($_POST['email'], $_POST['password']);
    if ($user_id) {
        $_SESSION['user_id'] = $user_id;
        echo "Login successful!";
        // header('Location: dashboard.php');  // Implement your dashboard
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
