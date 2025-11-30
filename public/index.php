<?php
session_start();

$page = $_GET['page'] ?? 'home';

switch ($page) {
    case 'login':
        require_once 'login.php';
        break;
    case 'register':
        require_once 'register.php';
        break;
    case 'dashboard':
        require_once 'dashboard.php';
        break;
    case 'appointment':
        require_once 'appointment.php';
        break;
        case 'service':
        require_once 'service.php';
        break;
    case 'visitlog':
         require_once 'visitlog.php';
        break;
     case 'notification':
         require_once 'notification.php';
        break;
    case 'profile':
        require_once __DIR__ . '/includes/profile.php';
        break;
    case 'settings':
        require_once __DIR__ . '/includes/settings.php';
        break;
    case 'logout':
        session_destroy();
        header('Location: index.php?page=login');
        exit;
    case 'home':
    default:
        // Redirect to login page as default
        header('Location: index.php?page=login');
        exit;
}
?>
