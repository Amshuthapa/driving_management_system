<?php
require_once '../models/Vehicles.php';

class VehicleController {
    private $model;

    public function __construct($pdo) {
        $this->model = new VehicleModel($pdo);
    }

    // Logic to handle adding a vehicle from a POST request
    public function handleAddVehicle() {
        $status = ['success' => false, 'message' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_vehicle'])) {
            $type  = trim($_POST['vehicle_type']);
            $model = trim($_POST['vehicle_model']);
            $no    = trim($_POST['vehicle_no']);

            if (!empty($type) && !empty($model) && !empty($no)) {
                if ($this->model->createVehicle($type, $model, $no)) {
                    $status['success'] = true;
                    $status['message'] = "Vehicle registered successfully!";
                } else {
                    $status['message'] = "Error: Could not register vehicle. (Possible duplicate plate number)";
                }
            } else {
                $status['message'] = "All fields are required.";
            }
        }
        return $status;
    }

    // Fetch vehicles for the view
    public function listVehicles() {
        return $this->model->getAllVehicles();
    }
}