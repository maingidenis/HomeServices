<?php
require_once __DIR__ . '/../app/controllers/UserController.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ctrl = new UserController();
    $result = $ctrl->registerUser($_POST['name'], $_POST['email'], $_POST['password'], $_POST['role']);
    if ($result) {
        echo '<script>
            alert("Registration Successful");
            window.location.href = "index.php?page=login&msg=registered";
        </script>';
        exit;
    } else {
        echo '<script>alert("Error! Registration failed.");</script>';
    }
    if ($result) {
        header('Location: index.php?page=login&msg=registered');
        exit;
    }
}


?>
<form method="post">
    <input name="name" type="text" required>
    <input name="email" type="email" required>
    <input name="password" type="password" required>
    <select name="role">
        <option value="client">Client</option>
        <option value="provider">Provider</option>
        <option value="admin">Admin</option>
    </select>
    <button type="submit">Register</button>
</form>
