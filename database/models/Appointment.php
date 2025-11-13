<?php
require_once __DIR__.'/../config/Database.php';
class Appointment {
    private $conn;
    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }
    public function create($client_id, $service_id, $appointment_time, $status, $location) {
        $stmt = $this->conn->prepare("INSERT INTO Appointment (client_id, service_id, appointment_time, status, location) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$client_id, $service_id, $appointment_time, $status, $location]);
    }
    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM Appointment WHERE appointment_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getAll() {
        $stmt = $this->conn->prepare("SELECT * FROM Appointment");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Add update, delete, by-client, by-service methods as needed
}
?>
