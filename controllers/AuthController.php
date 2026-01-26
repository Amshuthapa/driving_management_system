<?php
// Adjust paths as necessary
require_once '../config/db.php';
require_once '../models/UserModal.php';

class AuthController {
    private $model;

    public function __construct($pdo) {
        $this->model = new UserModel($pdo);
    }

    public function login() {
        $error = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Updated: Using email instead of username
            $email = trim($_POST['email']); 
            $password = trim($_POST['password']);

            if (!empty($email) && !empty($password)) {
                $user = $this->model->login($email, $password);

                if ($user) {
                    if (session_status() === PHP_SESSION_NONE) {
                        session_start();
                    }

                    // Updated: Use user_id and fullname from your new schema
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['fullname'] = $user['fullname'];
                    $_SESSION['email'] = $user['email'];

                    // Redirect based on Role (Lower-case to match DB ENUM)
                    if ($user['role'] === 'admin') {
                        header("Location: dashboard_admin.php");
                    } elseif ($user['role'] === 'driver') {
                        header("Location: dashboard_driver.php");
                    } else {
                        header("Location: dashboard_student.php");
                    }
                    exit();
                } else {
                    $error = "Invalid email or password.";
                }
            } else {
                $error = "Please fill in all fields.";
            }
        }
        return $error;
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
        header("Location: index.php");
        exit();
    }
}
?>