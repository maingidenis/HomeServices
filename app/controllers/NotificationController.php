<?php
require_once __DIR__ . '/../models/Notification.php';

class NotificationController {
    private $model;

    public function __construct() {
        $this->model = new Notification();
    }

    public function createNotification($user_id, $message) {
        return $this->model->create($user_id, $message);
    }

    public function getUserNotifications($user_id) {
        return $this->model->getByUser($user_id);
    }

    public function markNotificationAsRead($notification_id) {
        return $this->model->markAsRead($notification_id);
    }
}
?>
