<?php
require_once __DIR__ . '/../../config/Database.php';

class Service {
    private $conn;
    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function create($title, $description, $provider_id, $category) {
        $stmt = $this->conn->prepare("INSERT INTO Service (title, description, provider_id, category) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$title, $description, $provider_id, $category]);
    }

    public function getAll() {
        $stmt = $this->conn->prepare("SELECT * FROM Service");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($service_id) {
        $stmt = $this->conn->prepare("SELECT * FROM Service WHERE service_id = ?");
        $stmt->execute([$service_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function countAll() {
        $stmt = $this->conn->query("SELECT COUNT(*) FROM Service");
        return $stmt->fetchColumn();
    }
}
?>
