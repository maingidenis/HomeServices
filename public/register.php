<?php
/**
 * Registration Page
 * 
 * Handles new user registration with form validation.
 * Uses shared header and footer includes for consistent styling.
 */

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
    if (strlen($name) < 2 || strlen($name) > 100) {
        $error = "Name must be between 2 and 100 characters.";
    }

    // 2) Email: sanitize + validate
    $email = filter_var($rawEmail, FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    }

    // 3) Password: at least 8 chars, must contain uppercase, lowercase, digit, and special char
    if (strlen($rawPassword) < 8 ||
        !preg_match('/[A-Z]/', $rawPassword) ||
        !preg_match('/[a-z]/', $rawPassword) ||
        !preg_match('/[0-9]/', $rawPassword) ||
        !preg_match('/[^A-Za-z0-9]/', $rawPassword)) {
        $error = "Password must be at least 8 characters and include uppercase, lowercase, number, and special character.";
    }

    // 4) Role: whitelist
    $allowedRoles = ['client', 'provider', 'admin'];
    if (!in_array($rawRole, $allowedRoles, true)) {
        $error = "Invalid role selected.";
    }

    if (empty($error)) {
        $result = $ctrl->register($name, $email, $rawPassword, $rawRole);
        if ($result['success']) {
            header('Location: index.php?page=login&registered=1');
            exit;
        } else {
            $error = htmlspecialchars($result['error'], ENT_QUOTES, 'UTF-8');
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
