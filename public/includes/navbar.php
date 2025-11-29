<?php
/**
 * Shared Navigation Bar Component
 * Include this file in all pages for consistent navigation
 * Usage: <?php include 'includes/navbar.php'; ?>
 */

// Determine current page for active state
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$currentDir = basename(dirname($_SERVER['PHP_SELF']));

// Check user role for conditional menu items
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$isProvider = isset($_SESSION['role']) && $_SESSION['role'] === 'provider';
$isLoggedIn = isset($_SESSION['user_id']);
// Fetch user name from database if logged in
if (isset($_SESSION['user_id'])) {
    require_once __DIR__ . '/../../app/models/User.php';
    $userModel = new User();
    $userData = $userModel->getById($_SESSION['user_id']);
    $userName = $userData ? htmlspecialchars($userData['name'], ENT_QUOTES, 'UTF-8') : 'User';
} else {
    $userName = 'User';
}?>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php?page=dashboard">
            <i class="bi bi-house-door-fill"></i> Home Services
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <?php if ($isLoggedIn): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>" href="index.php?page=dashboard">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'service' ? 'active' : '' ?>" href="index.php?page=service">
                        <i class="bi bi-tools"></i> Services
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'appointment' ? 'active' : '' ?>" href="index.php?page=appointment">
                        <i class="bi bi-calendar-check"></i> Appointments
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'visitlog' ? 'active' : '' ?>" href="index.php?page=visitlog">
                        <i class="bi bi-journal-text"></i> Visit Logs
                    </a>
                </li>
                <?php if ($isAdmin): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?= $currentDir === 'admin' ? 'active' : '' ?>" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-shield-lock"></i> Admin
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark">
                        <li><a class="dropdown-item" href="admin/dashboard.php"><i class="bi bi-speedometer"></i> Admin Dashboard</a></li>
                        <li><a class="dropdown-item" href="admin/manage_users.php"><i class="bi bi-people"></i> Manage Users</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="admin/reports.php"><i class="bi bi-graph-up"></i> Reports</a></li>
                    </ul>
                </li>
                <?php endif; ?>
                <?php endif; ?>
            </ul>
            
            <ul class="navbar-nav">
                <?php if ($isLoggedIn): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i> <?= $userName ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end">
                        <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person"></i> Profile</a></li>
                        <li><a class="dropdown-item" href="settings.php"><i class="bi bi-gear"></i> Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                    </ul>
                </li>
                <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?page=login">
                        <i class="bi bi-box-arrow-in-right"></i> Login
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?page=register">
                        <i class="bi bi-person-plus"></i> Register
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
