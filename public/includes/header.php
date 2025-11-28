<?php
/**
 * Shared Header Component
 * Include this file at the top of all pages for consistent styling
 * Usage: <?php $pageTitle = 'Page Name'; include 'includes/header.php'; ?>
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - ' : '' ?>Home Services</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- Leaflet CSS (for maps) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <!-- Custom Page Styles (optional) -->
    <?php if (isset($customStyles)): ?>
    <style><?= $customStyles ?></style>
    <?php endif; ?>
</head>
<body class="bg-light">
<?php
// Start session if not already started
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Include the navbar
include __DIR__ . '/navbar.php';
?>
