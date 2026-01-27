<?php
require_once __DIR__ . '/../models/UserModal.php';

class AuthController {
    private $model;

    public function __construct($pdo) {
        $this->model = new UserModel($pdo);
    }

    public function login() {
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if ($email !== '' && $password !== '') {

                $user = $this->model->login($email, $password);

                if ($user) {
                    if (session_status() === PHP_SESSION_NONE) {
                        session_start();
                    }

                    $_SESSION['user_id']  = $user['user_id'];
                    $_SESSION['role']     = $user['role'];
                    $_SESSION['fullname'] = $user['fullname'];
                    $_SESSION['email']    = $user['email'];

                    if ($user['role'] === 'admin') {
                        header("Location: dashboard_admin.php?loggedin=1");
                    } elseif ($user['role'] === 'driver') {
                        header("Location: dashboard_driver.php?loggedin=1");
                    } else {
                        header("Location: dashboard_student.php?loggedin=1");
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

    // ✅ Controller only clears session and redirects to logout page (no loop risk)
    public function logoutAndRedirect() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];
        session_destroy();

        // redirect to the logout page that clears localStorage and then goes to index.php
        header("Location: logout.php");
        exit();
    }
}
