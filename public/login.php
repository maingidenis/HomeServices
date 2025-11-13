<?php
// login.php
session_start();
require_once "Database.php";
require_once "User.php";

$db = (new Database())->connect();
$user = new User($db);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"] ?? '';
    $password = $_POST["password"] ?? '';
    if ($user->login($email, $password)) {
        header("Location: dashboard.php");
        exit;
    } else {
        echo "Invalid login credentials.";
    }
}
?>
<!-- Simple login form -->
<form method="post" action="">
    <input type="email" name="email" required placeholder="Email" />
    <input type="password" name="password" required placeholder="Password" />
    <button type="submit">Login</button>
</form>
?>