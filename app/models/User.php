<?php
/**
 * User.php - Updated for SQLite + MySQL Dual-Mode
 * Location: app/models/User.php
 */

class User {
    private $conn;

    public function __construct() {
        // Use the dual-mode database configuration
        require_once __DIR__ . '/../../config/Database.php';
        $this->conn = $GLOBALS['db']->getConnection();
    }

    public function register($name, $email, $password, $role) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO User (name, email, password_hash, role) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$name, $email, $hash, $role]);
    }

    public function getAllUsers() {
        $stmt = $this->conn->query("SELECT * FROM User ORDER BY user_id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($user_id) {
        $stmt = $this->conn->prepare("SELECT * FROM User WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateRole($user_id, $role) {
        $stmt = $this->conn->prepare("UPDATE User SET role = ? WHERE user_id = ?");
        return $stmt->execute([$role, $user_id]);
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

    public function setOTP($user_id, $otp, $expires) {
        $stmt = $this->conn->prepare("UPDATE User SET otp_code=?, otp_expires_at=? WHERE user_id=?");
        return $stmt->execute([$otp, $expires, $user_id]);
    }

    public function verifyOTP($user_id, $otp) {
        $stmt = $this->conn->prepare("SELECT otp_code, otp_expires_at FROM User WHERE user_id=?");
        $stmt->execute([$user_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$row) return false;
        if ($row['otp_code'] !== $otp) return false;
        if (strtotime($row['otp_expires_at']) < time()) return false;
        
        return true;
    }

    public function emailExists($email) {
        $stmt = $this->conn->prepare("SELECT user_id FROM User WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
    }

    public function countAll() {
        $stmt = $this->conn->query('SELECT COUNT(*) FROM User');
        return $stmt->fetchColumn();
    }

    public function loginWithOAuth($provider, $oauth_id, $email, $name, $picture) {
        // Check if user exists with this OAuth provider
        $user = $this->findByOAuth($provider, $oauth_id);
        if ($user) {
            return $user['user_id'];
        }

        // Check if user exists with this email
        if ($email) {
            $stmt = $this->conn->prepare("SELECT user_id FROM User WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                // Link OAuth to existing account
                $this->linkOAuthAccount($user['user_id'], $provider, $oauth_id, $picture);
                return $user['user_id'];
            }
        }

        // Create new user
        $password_hash = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO User (name, email, password_hash, role, oauth_provider, oauth_id, profile_picture) VALUES (?, ?, ?, 'client', ?, ?, ?)");
        $stmt->execute([$name, $email, $password_hash, $provider, $oauth_id, $picture]);
        return $this->conn->lastInsertId();
    }

    public function findByOAuth($provider, $oauth_id) {
        $stmt = $this->conn->prepare("SELECT * FROM User WHERE oauth_provider = ? AND oauth_id = ?");
        $stmt->execute([$provider, $oauth_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function linkOAuthAccount($user_id, $provider, $oauth_id, $picture = null) {
        $stmt = $this->conn->prepare("UPDATE User SET oauth_provider = ?, oauth_id = ?, profile_picture = ? WHERE user_id = ?");
        return $stmt->execute([$provider, $oauth_id, $picture, $user_id]);
    }

    public function adminExists() {
        $stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM User WHERE role = 'admin'");
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] > 0;
    }
}
?>