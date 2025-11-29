<?php
require_once __DIR__ . '/../app/controllers/UserController.php';

$error = '';
$oldName = '';
$oldEmail = '';
$oldRole = 'client';

    $ctrl = new UserController();
    $adminExists = $ctrl->adminExists();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

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
    if (empty($name) || strlen($name) < 2) {
        $error = "Name is required and must be at least 2 characters.";
    }

    // 2) Email: sanitize + validate
    $email = filter_var($rawEmail, FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    }

    // 3) Password: basic check (you can add stricter rules)
    if (empty($rawPassword)) {
        $error = "Password is required.";
    }

    // 3b) Strong password validation
    if (!empty($rawPassword)) {
        $password = $rawPassword;

        // At least 8 chars, 1 uppercase, 1 lowercase, 1 number, 1 special char
        $pattern = "/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/";

        if (!preg_match($pattern, $password)) {
            $error = "
                    <strong>Password must contain:</strong>
                    <ul>
                        <li>Minimum 8 characters</li>
                        <li>At least 1 uppercase letter</li>
                        <li>At least 1 lowercase letter</li>
                        <li>At least 1 number</li>
                        <li>At least 1 special character</li>
                    </ul>";
        }
    }


    // 4) Role: whitelist
    $allowedRoles = ['client', 'provider', 'admin'];
    if (!in_array($rawRole, $allowedRoles, true)) {
        $error = "Invalid role selected.";
    }

    // 4b) Prevent registration as admin if an admin already exists
    if ($rawRole === 'admin' && $adminExists) {
        $error = "An administrator already exists. You cannot register as an admin.";
    }


    if (empty($error)) {
        $password = $rawPassword;
        $user_id = $ctrl->registerUser($name, $email, $password, $rawRole);
        if ($user_id) {
            header('Location: index.php?page=login');
            exit;
        } else {
            $error = "Registration failed. Email may already be in use.";
        }
    }
}

// Set page title and include header
$pageTitle = 'Register - Home Services';
include 'includes/header.php';
?>

<!-- Main Content -->
<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow">
                <div class="card-header bg-success text-white text-center">
                    <h4 class="mb-0"><i class="bi bi-person-plus"></i> Register</h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="bi bi-exclamation-triangle"></i> <?= $error ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="name" 
                                name="name" 
                                placeholder="Enter your full name"
                                value="<?= htmlspecialchars($oldName, ENT_QUOTES, 'UTF-8') ?>"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input 
                                type="email" 
                                class="form-control" 
                                id="email" 
                                name="email" 
                                placeholder="Enter your email"
                                value="<?= htmlspecialchars($oldEmail, ENT_QUOTES, 'UTF-8') ?>"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input 
                                type="password" 
                                class="form-control" 
                                id="password" 
                                name="password" 
                                placeholder="Create a password"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Register as</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="" disabled>Select your role</option>
                                <option value="client" <?= $oldRole === 'client' ? 'selected' : '' ?>>Client</option>
                                <option value="provider" <?= $oldRole === 'provider' ? 'selected' : '' ?>>Service Provider</option>

                                <?php if (!$adminExists): ?>
                                    <option value="admin" <?= $oldRole === 'admin' ? 'selected' : '' ?>>Admin</option>
                                <?php endif; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success w-100 mb-3">
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
</main>

<?php include 'includes/footer.php'; ?>
