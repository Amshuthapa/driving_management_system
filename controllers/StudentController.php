<?php
require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/../models/UserModal.php';

class StudentController {
    private $studentModel;
    private $userModel;
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->studentModel = new StudentModel($pdo);
        $this->userModel = new UserModel($pdo);
    }

    // Admin: List all students with booking + assignment info
    public function listStudents() {
        return $this->studentModel->getAllStudents();
    }

    // Admin: Delete student + user
    public function handleDelete() {
        if (isset($_GET['delete_id'])) {
            $id = (int)$_GET['delete_id'];

            if ($id > 0 && $this->studentModel->deleteStudent($id)) {
                header("Location: manage_students.php?msg=deleted");
                exit();
            }
        }
    }

    // Admin: Fetch student for edit
    public function getStudent($id) {
        return $this->studentModel->getStudentById((int)$id);
    }

    // ✅ Admin: Update ONLY user info (not booking/assignment)
    public function handleUpdateStudentUserInfo() {
        $res = ['success' => false, 'message' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_student'])) {

            $student_id  = (int)($_POST['student_id'] ?? 0);
            $user_id     = (int)($_POST['user_id'] ?? 0);

            $fullname    = trim($_POST['fullname'] ?? '');
            $email       = trim($_POST['email'] ?? '');
            $phonenumber = trim($_POST['phonenumber'] ?? '');

            if ($student_id <= 0 || $user_id <= 0 || $fullname === '' || $email === '') {
                $res['message'] = "Please fill required fields.";
                return $res;
            }

            if ($this->studentModel->updateStudentUserInfo($student_id, $user_id, $fullname, $email, $phonenumber)) {
                $res['success'] = true;
                $res['message'] = "Student updated successfully!";
            } else {
                $res['message'] = "Failed to update student.";
            }
        }

        return $res;
    }

    // ✅ Admin: Add student (Admin creates student user + Students entry)
    public function handleAddStudent() {
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

            try {
                $this->pdo->beginTransaction();

                if (!$this->userModel->create($fullname, $email, $phonenumber, $pass, 'student')) {
                    $this->pdo->rollBack();
                    $res['message'] = "Registration failed. Email might already exist.";
                    return $res;
                }

                $stmt = $this->pdo->prepare("SELECT user_id FROM Users WHERE email = ? LIMIT 1");
                $stmt->execute([$email]);
                $user_id = $stmt->fetchColumn();

                if (!$user_id) {
                    $this->pdo->rollBack();
                    $res['message'] = "User created but user_id not found.";
                    return $res;
                }

                if (!$this->studentModel->createStudentEntry($user_id)) {
                    $this->pdo->rollBack();
                    $res['message'] = "Failed to create student entry.";
                    return $res;
                }

                $this->pdo->commit();
                $res['success'] = true;
                $res['message'] = "Student registered successfully!";
                return $res;

            } catch (Exception $e) {
                $this->pdo->rollBack();
                $res['message'] = "Something went wrong. Please try again.";
                return $res;
            }
        }

        return $res;
    }

    // ✅ Admin: Assign driver + vehicle for a requested booking
    public function handleAssignStudent() {
        $res = ['success' => false, 'message' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_student'])) {

            $student_id = (int)($_POST['student_id'] ?? 0);
            $vehicle_id = (int)($_POST['vehicle_assigned_id'] ?? 0);
            $driver_id  = (int)($_POST['driver_assigned_id'] ?? 0);

            if ($student_id <= 0 || $vehicle_id <= 0 || $driver_id <= 0) {
                $res['message'] = "All fields are required.";
                return $res;
            }

            $student = $this->studentModel->getStudentById($student_id);
            if (!$student) {
                $res['message'] = "Student not found.";
                return $res;
            }

            if (($student['booking_status'] ?? '') !== 'requested') {
                $res['message'] = "Student has not requested a booking yet.";
                return $res;
            }

            $date = $student['requested_date'] ?? null;
            $time = $student['requested_time'] ?? null;

            if (!$date || !$time) {
                $res['message'] = "Requested slot missing (date/time).";
                return $res;
            }

            // Slot availability check at assignment time
            if ($this->studentModel->countAvailableDrivers($date, $time) <= 0) {
                $res['message'] = "No drivers available for this slot.";
                return $res;
            }
            if ($this->studentModel->countAvailableVehicles($date, $time) <= 0) {
                $res['message'] = "No vehicles available for this slot.";
                return $res;
            }

            if ($this->studentModel->assignStudent($student_id, $vehicle_id, $driver_id)) {
                $res['success'] = true;
                $res['message'] = "Student assigned successfully!";
            } else {
                $res['message'] = "Failed to assign student.";
            }
        }

        return $res;
    }

    // ✅ Public Student Register (User side)
    public function handlePublicStudentRegister() {
        $res = ['success' => false, 'message' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_student'])) {

            $fullname    = trim($_POST['fullname'] ?? '');
            $email       = trim($_POST['email'] ?? '');
            $phonenumber = trim($_POST['phonenumber'] ?? '');
            $pass        = $_POST['password'] ?? '';

            if ($fullname === '' || $email === '' || $phonenumber === '' || $pass === '') {
                $res['message'] = "All fields are required.";
                return $res;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $res['message'] = "Invalid email address.";
                return $res;
            }

            try {
                $this->pdo->beginTransaction();

                if (!$this->userModel->create($fullname, $email, $phonenumber, $pass, 'student')) {
                    $this->pdo->rollBack();
                    $res['message'] = "Registration failed. Email might already exist.";
                    return $res;
                }

                $stmt = $this->pdo->prepare("SELECT user_id FROM Users WHERE email = ? LIMIT 1");
                $stmt->execute([$email]);
                $user_id = $stmt->fetchColumn();

                if (!$user_id) {
                    $this->pdo->rollBack();
                    $res['message'] = "User created but user_id not found.";
                    return $res;
                }

                if (!$this->studentModel->createStudentEntry($user_id)) {
                    $this->pdo->rollBack();
                    $res['message'] = "Failed to create student entry.";
                    return $res;
                }

                $this->pdo->commit();
                $res['success'] = true;
                $res['message'] = "Student registered successfully!";
                return $res;

            } catch (Exception $e) {
                $this->pdo->rollBack();
                $res['message'] = "Something went wrong. Please try again.";
                return $res;
            }
        }

        return $res;
    }

    // ✅ Student: Request booking (date + time)
    public function handleStudentBookingRequest() {
        $res = ['success' => false, 'message' => ''];

        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'student') {
            header("Location: login.php");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_booking'])) {
            $date = $_POST['requested_date'] ?? '';
            $time = $_POST['requested_time'] ?? '';

            if ($date === '' || $time === '') {
                $res['message'] = "Please select date and time.";
                return $res;
            }

            $dt = strtotime($date . ' ' . $time);
            if ($dt === false || $dt < time()) {
                $res['message'] = "Please choose a future date/time.";
                return $res;
            }

            if ($this->studentModel->countAvailableDrivers($date, $time) <= 0) {
                $res['message'] = "No drivers available for this slot.";
                return $res;
            }

            if ($this->studentModel->countAvailableVehicles($date, $time) <= 0) {
                $res['message'] = "No vehicles available for this slot.";
                return $res;
            }

            if ($this->studentModel->requestBooking($_SESSION['user_id'], $date, $time)) {
                $res['success'] = true;
                $res['message'] = "Booking request submitted. Admin will assign driver & vehicle.";
            } else {
                $res['message'] = "Failed to submit booking request.";
            }
        }

        return $res;
    }
    public function handleAdminBookingRequest() {
    $res = ['success' => false, 'message' => ''];

    if (session_status() === PHP_SESSION_NONE) session_start();

    // ✅ Admin only
    if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
        header("Location: ../index.php");
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_request_booking'])) {
        $student_id = (int)($_POST['student_id'] ?? 0);
        $date = $_POST['requested_date'] ?? '';
        $time = $_POST['requested_time'] ?? '';

        if ($student_id <= 0 || $date === '' || $time === '') {
            $res['message'] = "Please select student, date and time.";
            return $res;
        }

        $dt = strtotime($date . ' ' . $time);
        if ($dt === false || $dt < time()) {
            $res['message'] = "Please choose a future date/time.";
            return $res;
        }

        // ✅ Check availability
        if ($this->studentModel->countAvailableDrivers($date, $time) <= 0) {
            $res['message'] = "No drivers available for this slot.";
            return $res;
        }
        if ($this->studentModel->countAvailableVehicles($date, $time) <= 0) {
            $res['message'] = "No vehicles available for this slot.";
            return $res;
        }

        // ✅ Save request on Students table
        if ($this->studentModel->requestBookingByStudentId($student_id, $date, $time)) {
            $res['success'] = true;
            $res['message'] = "Booking request created. Now assign driver & vehicle from Assign Students.";
        } else {
            $res['message'] = "Failed to create booking request.";
        }
    }

    return $res;
}

    // ===== ADD THIS INSIDE class StudentController { ... } =====

public function getDashboardStudent() {
    if (session_status() === PHP_SESSION_NONE) session_start();

    if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'student') {
        header("Location: login.php");
        exit();
    }

    return $this->studentModel->getStudentDashboardByUserId((int)$_SESSION['user_id']);
}



}
