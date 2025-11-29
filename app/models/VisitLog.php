<?php
/**
 * VisitLog.php - Updated for SQLite + MySQL Dual-Mode
 * Location: app/models/VisitLog.php
 */

class VisitLog {
    private $conn;

    public function __construct() {
        require_once __DIR__ . '/../../config/Database.php';
        $this->conn = $GLOBALS['db']->getConnection();
    }

    public function create($user_id, $appointment_id, $check_in_time, $checkout_time, $covid_status) {
        $stmt = $this->conn->prepare("INSERT INTO VisitLog (user_id, appointment_id, check_in_time, checkout_time, covid_status) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$user_id, $appointment_id, $check_in_time, $checkout_time, $covid_status]);
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM VisitLog WHERE visit_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAll() {
        $stmt = $this->conn->prepare("SELECT * FROM VisitLog");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByUserId($user_id) {
        $stmt = $this->conn->prepare("SELECT * FROM VisitLog WHERE user_id = ? ORDER BY check_in_time DESC");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByAppointmentId($appointment_id) {
        $stmt = $this->conn->prepare("SELECT * FROM VisitLog WHERE appointment_id = ?");
        $stmt->execute([$appointment_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
