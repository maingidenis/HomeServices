<?php
require_once __DIR__ . '/../app/controllers/UserController.php';

$error = '';
$oldName = '';
$oldEmail = '';
$oldRole = 'client';

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

    // Store for repopulating form
    $oldName = $rawName;
    $oldEmail = $rawEmail;
    $oldRole = $rawRole;

    // 1) Name: strip tags + length check
    $name = strip_tags($rawName);
    if ($name === '' || strlen($name) < 2 || strlen($name) > 100) {
        $error = "Please enter a valid name (2 to 100 characters).";
    }

    // 2) Email: sanitize then validate
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

    // 3) Password: strong policy
    if (empty($error)) {
        if ($rawPassword === '') {
            $error = "Password is required.";
        } elseif (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[^a-zA-Z0-9]).{8,}$/', $rawPassword)) {
            $error = "Password must be at least 8 characters and include upper, lower, number and special character.";
        }
    }

    // 4) Role: validate
    if (empty($error)) {
        $allowedRoles = ['client', 'provider', 'admin'];
        if (!in_array($rawRole, $allowedRoles, true)) {
            $error = "Please select a valid role.";
        }
    }

    // If no errors, register
    if (empty($error)) {
        $ok = $ctrl->registerUser($name, $email, $rawPassword, $rawRole);
        if ($ok) {
            header("Location: index.php?page=login&registered=1");
            exit;
        } else {
            $error = "Registration failed. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Home Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow">
                    <div class="card-body p-4">
                        <h3 class="card-title text-center mb-4">
                            <i class="bi bi-person-plus-fill text-primary"></i> Create Account
                        </h3>
                        
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="post" action="">
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="name" 
                                       name="name" 
                                       placeholder="Enter your full name"
                                       minlength="2"
                                       maxlength="100"
                                       value="<?= htmlspecialchars($oldName, ENT_QUOTES, 'UTF-8') ?>"
                                       required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" 
                                       class="form-control" 
                                       id="email" 
                                       name="email" 
                                       placeholder="Enter your email"
                                       value="<?= htmlspecialchars($oldEmail, ENT_QUOTES, 'UTF-8') ?>"
                                       required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" 
                                       class="form-control" 
                                       id="password" 
                                       name="password" 
                                       placeholder="Create a strong password"
                                       required>
                                <div class="form-text">
                                    At least 8 characters with uppercase, lowercase, number, and special character.
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="role" class="form-label">Register as</label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="" disabled>Select your role</option>
                                    <option value="client" <?= $oldRole === 'client' ? 'selected' : '' ?>>Client</option>
                                    <option value="provider" <?= $oldRole === 'provider' ? 'selected' : '' ?>>Service Provider</option>
                                    <option value="admin" <?= $oldRole === 'admin' ? 'selected' : '' ?>>Admin</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 mb-3">
                                <i class="bi bi-person-plus"></i> Register
                            </button>
                        </form>
                        
                        <hr>
                        <p class="text-center mb-0">
                            Already have an account? <a href="index.php?page=login">Login here</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
