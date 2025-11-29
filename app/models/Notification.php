<?php
/**
 * Notification.php - Updated for SQLite + MySQL Dual-Mode
 * Location: app/models/Notification.php
 */

class Notification {
    private $conn;

    public function __construct() {
        require_once __DIR__ . '/../../config/Database.php';
        $this->conn = $GLOBALS['db']->getConnection();
    }

    public function create($user_id, $message) {
        $stmt = $this->conn->prepare("INSERT INTO Notification (user_id, message) VALUES (?, ?)");
        return $stmt->execute([$user_id, $message]);
    }

    public function getByUser($user_id) {
        $stmt = $this->conn->prepare("SELECT * FROM Notification WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function markAsRead($notification_id) {
        $stmt = $this->conn->prepare("UPDATE Notification SET is_read = 1 WHERE notification_id = ?");
        return $stmt->execute([$notification_id]);
    }

    public function delete($notification_id) {
        $stmt = $this->conn->prepare("DELETE FROM Notification WHERE notification_id = ?");
        return $stmt->execute([$notification_id]);
    }

    public function getUnreadCount($user_id) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM Notification WHERE user_id = ? AND is_read = 0");
        $stmt->execute([$user_id]);
        return $stmt->fetchColumn();
    }
}
?>
