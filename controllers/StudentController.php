<?php
require_once '../models/Student.php';
require_once '../models/UserModal.php';

class StudentController {
    private $studentModel;
    private $userModel;
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->studentModel = new StudentModel($pdo);
        $this->userModel = new UserModel($pdo);
    }

    // List all students with assignment info
    public function listStudents() {
        return $this->studentModel->getAllStudents();
    }

    // Delete student + assignment + user account
    public function handleDelete() {
        if (isset($_GET['delete_id'])) {
            $id = $_GET['delete_id'];

            if ($this->studentModel->deleteStudent($id)) {
                header("Location: manage_students.php?msg=deleted");
                exit();
            }
        }
    }

    // Fetch a single student for edit modal
    public function getStudent($id) {
        return $this->studentModel->getStudentById($id);
    }

    // Update student + assignment
public function handleUpdateStudent() {
    $res = ['success' => false, 'message' => ''];

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_student'])) {

        $student_id = $_POST['student_id'];
        $user_id    = $_POST['user_id'];

        $fullname   = $_POST['fullname'];
        $email      = $_POST['email'];
        $phonenumber = $_POST['phonenumber'];

        $vehicle_assigned_id = $_POST['vehicle_assigned_id'] ?: null;
        $driver_assigned_id  = $_POST['driver_assigned_id'] ?: null;
        $assigned_at         = $_POST['assigned_at'] ?: null;

        if ($this->studentModel->updateStudent(
            $student_id,
            $user_id,
            $fullname,
            $email,
            $phonenumber,
            $vehicle_assigned_id,
            $driver_assigned_id,
            $assigned_at
        )) {
            $res['success'] = true;
            $res['message'] = "Student updated successfully!";
        } else {
            $res['message'] = "Failed to update student.";
        }
    }

    return $res;
}


    // Add new student + user + optional assignment
    public function handleAddStudent() {
        $res = ['success' => false, 'message' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_new_person'])) {
            $fullname    = trim($_POST['fullname']);
            $email       = trim($_POST['email']);
            $phonenumber = trim($_POST['phonenumber'] ?? '');
            $pass        = $_POST['password'];

            $vehicle_assigned_id = $_POST['vehicle_assigned_id'] ?? null;
            $driver_assigned_id  = $_POST['driver_assigned_id'] ?? null;
            $assigned_at         = $_POST['assigned_at'] ?? date('Y-m-d H:i:s');

            // 1. Create user account with role = student
            if ($this->userModel->create($fullname, $email, $phonenumber, $pass, 'student')) {

                // 2. Fetch the new user_id
                $stmt = $this->pdo->prepare("SELECT user_id FROM Users WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch();

                if ($user && isset($user['user_id'])) {

                    // 3. Create student entry
                    if ($this->studentModel->createStudentEntry($user['user_id'])) {

                        // 4. Fetch the new student_id
                        $stmt2 = $this->pdo->prepare("SELECT student_id FROM Students WHERE user_id = ?");
                        $stmt2->execute([$user['user_id']]);
                        $student = $stmt2->fetch();

                        if ($student && isset($student['student_id'])) {

                            // 5. Create assignment if provided
                            if ($vehicle_assigned_id && $driver_assigned_id) {
                                $this->studentModel->createAssignment(
                                    $student['student_id'],
                                    $user['user_id'],
                                    $vehicle_assigned_id,
                                    $driver_assigned_id,
                                    $assigned_at
                                );
                            }

                            $res['success'] = true;
                            $res['message'] = "Student registered successfully!";
                        }
                    }
                }

            } else {
                $res['message'] = "Registration failed. Email might already exist.";
            }
        }

        return $res;
    }
    public function handleAssignStudent() {
    $res = ['success' => false, 'message' => ''];

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_student'])) {

        $student_id = $_POST['student_id'];
        $vehicle_id = $_POST['vehicle_assigned_id'];
        $driver_id  = $_POST['driver_assigned_id'];

        if (!$student_id || !$vehicle_id || !$driver_id) {
            $res['message'] = "All fields are required.";
            return $res;
        }

        if ($this->studentModel->assignStudent(
            $student_id,
            $vehicle_id,
            $driver_id
        )) {
            $res['success'] = true;
            $res['message'] = "Student assigned successfully!";
        } else {
            $res['message'] = "Failed to assign student.";
        }
    }

    return $res;
}

}
