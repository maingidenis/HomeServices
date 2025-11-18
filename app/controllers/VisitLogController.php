<?php
require_once __DIR__ . '/../models/VisitLog.php';

class VisitLogController {
    private $model;

    public function __construct() {
        $this->model = new VisitLog();
    }

    public function createVisitLog($user_id, $appointment_id, $check_in_time, $checkout_time, $covid_status) {
        return $this->model->create($user_id, $appointment_id, $check_in_time, $checkout_time, $covid_status);
    }

    public function getVisitLogById($id) {
        return $this->model->getById($id);
    }

    public function getAllVisitLogs() {
        return $this->model->getAll();
    }

}
?>
