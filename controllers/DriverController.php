<?php
require_once '../config/db.php'; 
require_once '../models/DriverModel.php';

class DriverController {

    private $model;
    public function __construct($pdo) {
        $this->model = new DriverModel($pdo);
    }

    public function index() {
        return $this->model->getAll();
    }

    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name']; 
            $license = $_POST['license'];
            $v_type = $_POST['v_type'];

            // Optional: Basic validation
            if (!empty($name) && !empty($license)) {
                $this->model->create($name, $license, $v_type);
                header("Location: index.php");
                exit(); // Always good practice to exit after header redirect
            }
        }
    }

    public function edit() {
        if ($_SERVER["REQUEST_METHOD"] === "PUT") {
            $name = $_POST["name"];
            $license = $_POST[""];
            $v_type = $_POST[""];
            if (!empty($name) && !empty($license)) {
                $this->model->update($name, $license, $v_type);
                header("");
                exit();
            }
        }   
    }

    public function delete($id) {
        if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
            if (!empty($id)) {
                $this->model->delete( $id);
                header("");
                exit();
            }
        }
    } 
}
?>
