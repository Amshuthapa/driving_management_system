<?php
require_once '../models/Driver.php';
require_once '../models/UserModal.php'; 

class DriverController {
    private $driverModel;
    private $userModel; 
    private $pdo;       

    public function __construct($pdo) {
        $this->pdo = $pdo; 
        $this->driverModel = new DriverModel($pdo);
        $this->userModel = new UserModel($pdo); 
    }

    public function listDrivers() {
        return $this->driverModel->getAllDrivers();
    }

    public function handleDelete() {
        if (isset($_GET['delete_id'])) {
            $id = $_GET['delete_id'];
            if ($this->driverModel->deleteDriver($id)) {
                header("Location: manage_drivers.php?msg=deleted");
                exit();
            }
        }
    }

    public function getDriver($id) {
        return $this->driverModel->getDriverById($id);
    }

    public function handleUpdateDriver() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_driver'])) {
            $id = $_POST['driver_id'];
            $user_id = $_POST['user_id'];
            $fullname = $_POST['fullname'];
            $email = $_POST['email'];
            $phonenumber = $_POST['phonenumber'];

            if ($this->driverModel->updateDriver($id, $user_id, $fullname, $email, $phonenumber)) {
                header("Location: manage_drivers.php?msg=updated");
                exit();
            }
        }
    }

    public function handleAddDriver() {
        $res = ['success' => false, 'message' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_new_person'])) {
            $fullname = trim($_POST['fullname']);
            $email    = trim($_POST['email']);
            $phonenumber    = trim($_POST['phonenumber'] ?? '');
            $pass     = $_POST['password'];

            // 1. Fixed the call to use $this->userModel (proper property name)
            if ($this->userModel->create($fullname, $email, $phonenumber, $pass, 'driver')) {
                
                // 2. Fixed use of $this->pdo
                $stmt = $this->pdo->prepare("SELECT user_id FROM Users WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch();

                // 3. Create the entry in the Drivers table
                if ($this->driverModel->createDriverEntry($user['user_id'])) {
                    $res['success'] = true;
                    $res['message'] = "Tutor registered successfully!";
                }
            } else {
                $res['message'] = "Registration failed. Email might already exist.";
            }
        }
        return $res;
    }
}