<?php
require_once __DIR__ . '/../../app/middleware/auth.php';
require_once __DIR__ . '/../../app/middleware/admin_only.php';
require_once __DIR__ . '/../../app/models/User.php';

$userId = $_GET['id'];
$newRole = $_GET['role'];

$model = new User();
$model->updateRole($userId, $newRole);

header("Location: manage_users.php");
exit;
