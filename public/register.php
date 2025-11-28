<?php
require_once __DIR__ . '/../app/controllers/UserController.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ctrl = new UserController();

    // Get raw values with defaults
    $rawName     = $_POST['name']     ?? '';
    $rawEmail    = $_POST['email']    ?? '';
    $rawPassword = $_POST['password'] ?? '';
    $rawRole     = $_POST['role']     ?? '';

    // Trim
    $rawName     = trim($rawName);
    $rawEmail    = trim($rawEmail);
    $rawPassword = trim($rawPassword);
    $rawRole     = trim($rawRole);

    // 1) Name: strip tags + length check [web:16][web:23]
    $name = strip_tags($rawName);
    if ($name === '' || strlen($name) < 2 || strlen($name) > 100) {
        $error = "Please enter a valid name (2 to 100 characters).";
    }

    // 2) Email: sanitize then validate [web:16][web:23]
    if (empty($error)) {
        $email = filter_var($rawEmail, FILTER_SANITIZE_EMAIL);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Please enter a valid email address.";
        }
    }

    // 2b) Check duplicate email
    if (empty($error)) {
        $model = new User();
        if ($model->emailExists($email)) {
            $error = "An account with this email already exists.";
        }
    }

    // 3) Password: strong policy, only validate (do not modify chars) [web:54][web:52]
    if (empty($error)) {
        if ($rawPassword === '') {
            $error = "Password is required.";
        } elseif (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[^a-zA-Z0-9]).{8,}$/', $rawPassword)) {
            $error = "Password must be at least 8 characters and include upper, lower, number and special character.";
        }
    }

    // 4) Role: whitelist allowed values [web:16][web:26]
    if (empty($error)) {
        $allowedRoles = ['client', 'provider', 'admin'];
        if (!in_array($rawRole, $allowedRoles, true)) {
            $error = "Invalid role selected.";
        } else {
            $role = $rawRole;
        }
    }


    if (empty($error)) {
        // Use validated values; controller should hash password & use prepared statements [web:26][web:76]
        $result = $ctrl->registerUser($name, $email, $rawPassword, $role);

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
    } else {
        // Show error via JS alert, with escaping to avoid XSS [web:18][web:84]
        echo '<script>alert("'.htmlspecialchars($error, ENT_QUOTES, 'UTF-8').'");</script>';
    }

    // For re-filling form fields on error
    $oldName  = $name  ?? $rawName;
    $oldEmail = $email ?? $rawEmail;
    $oldRole  = $role  ?? $rawRole;
} else {
    $oldName  = '';
    $oldEmail = '';
    $oldRole  = 'client';
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body>
    <form method="post" action="">
        <label for="name">Name</label>
        <input name="name"
               type="text"
               required
               minlength="2"
               maxlength="100"
               placeholder="Name"
               value="<?= htmlspecialchars($oldName, ENT_QUOTES, 'UTF-8') ?>"><!-- [web:23][web:86] -->
    
        <label for="email">Email</label>
        <input name="email"
               type="email"
               required
               placeholder="Email"
               value="<?= htmlspecialchars($oldEmail, ENT_QUOTES, 'UTF-8') ?>"><!-- [web:23][web:84] -->
    
        <label for="password">Password</label>
        <input name="password"
               type="password"
               required
               placeholder="Password"><!-- [web:88][web:93][web:65] -->
    
        <select name="role" required>
            <option value="client"   <?= $oldRole === 'client'   ? 'selected' : '' ?>>Client</option>
            <option value="provider" <?= $oldRole === 'provider' ? 'selected' : '' ?>>Provider</option>
            <option value="admin"    <?= $oldRole === 'admin'    ? 'selected' : '' ?>>Admin</option>
        </select>
    
        <button type="submit">Register</button>
    </form>
    
</body>
</html>
