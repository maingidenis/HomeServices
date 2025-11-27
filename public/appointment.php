<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
// Authorization check
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}
require_once __DIR__ . '/../app/controllers/AppointmentController.php';

$appointmentCtrl = new AppointmentController();
$message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $client_id = $_SESSION['user_id'];
    $service_id = $_POST['service_id'];
    $appointment_time = $_POST['appointment_time'];
    $status = 'requested';
    $location = $_POST['location'];

    $ok = $appointmentCtrl->createAppointment($client_id, $service_id, $appointment_time, $status, $location);
    $message = $ok ? "Appointment created successfully!" : "Error creating appointment.";
}

$appointments = $appointmentCtrl->getAllAppointments();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Appointments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Create Appointment</h2>
    <?php if ($message): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <form method="post" class="mb-4">
        <div class="mb-3">
            <label class="form-label">Service ID</label>
            <input class="form-control" type="number" name="service_id" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Appointment Time</label>
            <input class="form-control" type="datetime-local" name="appointment_time" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Location</label>
            <input class="form-control" type="text" name="location" required>
        </div>
        <button class="btn btn-primary" type="submit">Book Appointment</button>
    </form>

    <h2>All Appointments</h2>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>ID</th>
            <th>Client</th>
            <th>Service</th>
            <th>Time</th>
            <th>Status</th>
            <th>Location</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($appointments as $appt): ?>
            <tr>
                <td><?= htmlspecialchars($appt['appointment_id']) ?></td>
                <td><?= htmlspecialchars($appt['client_id']) ?></td>
                <td><?= htmlspecialchars($appt['service_id']) ?></td>
                <td><?= htmlspecialchars($appt['appointment_time']) ?></td>
                <td><?= htmlspecialchars($appt['status']) ?></td>
                <td><?= htmlspecialchars($appt['location']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
