<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}
require_once __DIR__ . '/../app/controllers/ServiceController.php';

$serviceCtrl = new ServiceController();

// Handle form submission for new service (e.g., if user is a provider)
$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $desc = $_POST['description'];
    $category = $_POST['category'];
    $provider_id = $_SESSION['user_id']; // assuming current user is provider
    $ok = $serviceCtrl->addService($title, $desc, $provider_id, $category);
    $message = $ok ? "Service added successfully!" : "Error adding service!";
}

// Fetch all services
$services = $serviceCtrl->getAllServices();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Add New Service</h2>
    <?php if ($message): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>
    <form method="post" class="mb-4">
        <div class="mb-3">
            <input class="form-control" type="text" name="title" placeholder="Title" required>
        </div>
        <div class="mb-3">
            <textarea class="form-control" name="description" placeholder="Description" required></textarea>
        </div>
        <div class="mb-3">
            <select class="form-select" name="category" required>
                <option value="" selected disabled>Category</option>
                <option value="cleaning">Cleaning</option>
                <option value="repairs">Repairs</option>
                <option value="maintenance">Maintenance</option>
                <option value="other">Other</option>
            </select>
        </div>
        <button class="btn btn-primary" type="submit">Add Service</button>
    </form>

    <h2>All Services</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Title</th><th>Description</th><th>Category</th><th>Provider</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($services as $service): ?>
            <tr>
                <td><?= htmlspecialchars($service['title']) ?></td>
                <td><?= htmlspecialchars($service['description']) ?></td>
                <td><?= htmlspecialchars($service['category']) ?></td>
                <td><?= htmlspecialchars($service['provider_id']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
