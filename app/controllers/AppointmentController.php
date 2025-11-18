<?php
require_once __DIR__ . '/../models/Appointment.php';

class AppointmentController {
    private $model;

    public function __construct() {
        $this->model = new Appointment();
    }

    public function createAppointment($client_id, $service_id, $appointment_time, $status, $location) {
        return $this->model->create($client_id, $service_id, $appointment_time, $status, $location);
    }

    public function getAppointmentById($id) {
        return $this->model->getById($id);
    }

    public function getAllAppointments() {
        return $this->model->getAll();
    }

}
?>
