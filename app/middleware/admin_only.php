<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: /index.php?page=login");
    exit;
}

if ($_SESSION['role'] !== 'admin') {
    header("Location: /index.php?page=login");
    exit;
}
