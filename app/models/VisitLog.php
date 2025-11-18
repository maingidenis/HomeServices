<?php
require_once __DIR__.'/../config/Database.php';
class VisitLog {
    private $conn;
    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
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
    // Add update, delete, by-user, by-appointment methods as needed
}
?>
