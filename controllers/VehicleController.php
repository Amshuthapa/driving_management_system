<?php
require_once __DIR__ . '/../models/Vehicles.php';

class VehicleController {
    private $model;

    public function __construct($pdo) {
        $this->model = new VehicleModel($pdo);
    }

    public function handleAddVehicle() {
        $status = ['success' => false, 'message' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_vehicle'])) {

            $vehicle_no    = trim($_POST['vehicle_no'] ?? '');
            $vehicle_model = trim($_POST['vehicle_model'] ?? '');

            if ($vehicle_no === '' || $vehicle_model === '') {
                $status['message'] = "Vehicle No and Vehicle Model are required.";
                return $status;
            }

            if ($this->model->createVehicle($vehicle_no, $vehicle_model)) {
                $status['success'] = true;
                $status['message'] = "Vehicle registered successfully!";
            } else {
                $status['message'] = "Could not register vehicle. (Possible duplicate vehicle number)";
            }
        }

        return $status;
    }

    public function handleUpdateVehicle() {
        $status = ['success' => false, 'message' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_vehicle'])) {

            $vehicle_id    = (int)($_POST['vehicle_id'] ?? 0);
            $vehicle_no    = trim($_POST['vehicle_no'] ?? '');
            $vehicle_model = trim($_POST['vehicle_model'] ?? '');

            if ($vehicle_id <= 0 || $vehicle_no === '' || $vehicle_model === '') {
                $status['message'] = "All fields are required.";
                return $status;
            }

            if ($this->model->updateVehicle($vehicle_id, $vehicle_no, $vehicle_model)) {
                $status['success'] = true;
                $status['message'] = "Vehicle updated successfully!";
            } else {
                $status['message'] = "Could not update vehicle. (Possible duplicate vehicle number)";
            }
        }

        return $status;
    }

    public function handleDeleteVehicle() {
        if (isset($_GET['delete_id'])) {
            $id = (int)$_GET['delete_id'];
            if ($id > 0) {
                $this->model->deleteVehicle($id);
                header("Location: manage_vehicles.php?msg=deleted");
                exit();
            }
        }
    }

    public function listVehicles() {
        return $this->model->getAllVehicles();
    }

    public function getVehicle($id) {
        return $this->model->getVehicleById((int)$id);
    }
}
