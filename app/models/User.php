<?php
require_once __DIR__ . '/../../config/Database.php';
class User {
    private $conn;
    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }
    public function register($name, $email, $password, $role) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO User (name, email, password_hash, role) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$name, $email, $hash, $role]);
    }
    public function login($email, $password) {
        $stmt = $this->conn->prepare("SELECT user_id, password_hash FROM User WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password_hash'])) {
            return $user['user_id'];
        }
        return false;
    }
    public function updateHealthStatus($user_id, $status) {
        $stmt = $this->conn->prepare("UPDATE User SET health_status = ? WHERE user_id = ?");
        return $stmt->execute([$status, $user_id]);
    }

    // Dashboard related methods
    public function countAll() {
        $stmt = $this->conn->query('SELECT COUNT(*) FROM User');
        return $stmt->fetchColumn();
    }
    
    
}
?>
