<?php
require_once __DIR__ . '/../models/Service.php';

class ServiceController {
    private $model;

    public function __construct() {
        $this->model = new Service();
    }

    public function addService($title, $description, $provider_id, $category) {
        return $this->model->create($title, $description, $provider_id, $category);
    }

    public function getAllServices() {
        return $this->model->getAll();
    }

    public function getServiceById($service_id) {
        return $this->model->getById($service_id);
    }

    public function getServiceCount() {
        return $this->model->countAll();
    }
}
?>
