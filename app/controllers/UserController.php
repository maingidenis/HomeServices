<?php
require_once __DIR__.'/../models/User.php';
class UserController {
    public function registerUser($name, $email, $password, $role) {
        $model = new User();
        return $model->register($name, $email, $password, $role);
    }
    public function loginUser($email, $password) {
        $model = new User();
        return $model->login($email, $password);
    }
    public function setHealthStatus($user_id, $status) {
        $model = new User();
        return $model->updateHealthStatus($user_id, $status);
    }
    public function adminExists() {
    $model = new User();
    return $model->adminExists();
}

    
}
?>
