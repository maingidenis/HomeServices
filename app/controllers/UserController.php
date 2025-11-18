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
}
?>
