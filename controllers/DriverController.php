<?php
require_once __DIR__ . '/../models/Driver.php';
require_once __DIR__ . '/../models/UserModal.php';

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
            $id = (int)$_GET['delete_id'];
            if ($id > 0 && $this->driverModel->deleteDriver($id)) {
                header("Location: manage_drivers.php?msg=deleted");
                exit();
            }
        }
    }

    public function getDriver($id) {
        return $this->driverModel->getDriverById((int)$id);
    }

    /**
     * ✅ Update driver (User info + driver active status)
     * Expects POST:
     * driver_id, user_id, fullname, email, phonenumber, is_active
     */
    public function handleUpdateDriver() {
        $res = ['success' => false, 'message' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_driver'])) {

            $driver_id   = (int)($_POST['driver_id'] ?? 0);
            $user_id     = (int)($_POST['user_id'] ?? 0);
            $fullname    = trim($_POST['fullname'] ?? '');
            $email       = trim($_POST['email'] ?? '');
            $phonenumber = trim($_POST['phonenumber'] ?? '');
            $is_active   = isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1;

            if ($driver_id <= 0 || $user_id <= 0 || $fullname === '' || $email === '') {
                $res['message'] = "Please fill required fields.";
                return $res;
            }

            try {
                $this->pdo->beginTransaction();

                // 1) Update user info
                $this->userModel->update($user_id, $fullname, $email, $phonenumber, 'driver');

                // 2) Update driver active status
                $this->driverModel->setDriverActive($driver_id, $is_active);

                $this->pdo->commit();

                $res['success'] = true;
                $res['message'] = "Driver updated successfully!";
                header("Location: manage_drivers.php?msg=updated");
                exit();

            } catch (Exception $e) {
                $this->pdo->rollBack();
                $res['message'] = "Failed to update driver.";
            }
        }

        return $res;
    }

    /**
     * ✅ Add new driver (Creates user with role=driver + inserts Drivers row)
     */
    public function handleAddDriver() {
        $res = ['success' => false, 'message' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_new_person'])) {

            $fullname    = trim($_POST['fullname'] ?? '');
            $email       = trim($_POST['email'] ?? '');
            $phonenumber = trim($_POST['phonenumber'] ?? '');
            $pass        = $_POST['password'] ?? '';

            if ($fullname === '' || $email === '' || $pass === '') {
                $res['message'] = "Fullname, Email and Password are required.";
                return $res;
            }

            // 1) Create user role = driver
            if ($this->userModel->create($fullname, $email, $phonenumber, $pass, 'driver')) {

                // 2) Get newly created user_id
                $stmt = $this->pdo->prepare("SELECT user_id FROM Users WHERE email = ? LIMIT 1");
                $stmt->execute([$email]);
                $user_id = $stmt->fetchColumn();

                if ($user_id) {
                    // 3) Create driver entry
                    if ($this->driverModel->createDriverEntry($user_id, 1)) {
                        $res['success'] = true;
                        $res['message'] = "Driver registered successfully!";
                        return $res;
                    }

                    $res['message'] = "Driver record create failed.";
                    return $res;
                }

                $res['message'] = "User created but user_id not found.";
                return $res;

            } else {
                $res['message'] = "Registration failed. Email might already exist.";
                return $res;
            }
        }

        return $res;
    }
}
