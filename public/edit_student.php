<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

require_once '../config/db.php';
require_once '../controllers/StudentController.php';
require_once '../controllers/DriverController.php';
require_once '../controllers/VehicleController.php';

$pdo = (new \Config\Database())->connect();

$studentCtrl = new StudentController($pdo);
$driverCtrl  = new DriverController($pdo);
$vehicleCtrl = new VehicleController($pdo);

// Get student ID
if (!isset($_GET['id'])) {
    header("Location: manage_students.php");
    exit();
}

$student_id = $_GET['id'];
$student = $studentCtrl->getStudent($student_id);

if (!$student) {
    header("Location: manage_students.php?msg=notfound");
    exit();
}

// Handle update
$response = $studentCtrl->handleUpdateStudent();

// Fetch dropdown data
$drivers  = $driverCtrl->listDrivers();
$vehicles = $vehicleCtrl->listVehicles();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Student | Driving System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #6366f1; --glass: rgba(255, 255, 255, 0.03); --border: rgba(255, 255, 255, 0.1); }
        body { margin: 0; font-family: 'Inter', sans-serif; background: #0f172a; color: white; display: flex; background-image: radial-gradient(circle at top right, #1e1b4b, #0f172a); }
        main { flex: 1; padding: 3rem; margin-left: 260px; display: flex; justify-content: center; align-items: center; }
        .glass-card { background: var(--glass); backdrop-filter: blur(12px); border: 1px solid var(--border); border-radius: 1.5rem; padding: 2.5rem; width: 100%; max-width: 550px; }
        input, select { width: 100%; padding: 0.8rem; background: rgba(0,0,0,0.2); border: 1px solid var(--border); border-radius: 0.75rem; color: white; margin-bottom: 1.2rem; box-sizing: border-box; }
        .btn { width: 100%; background: var(--primary); color: white; border: none; padding: 1rem; border-radius: 0.75rem; font-weight: 600; cursor: pointer; }
        .btn-secondary { background: rgba(255,255,255,0.08); }
    </style>
</head>
<body>
<?php include('../includes/navbar.php'); ?>

<main>
    <div class="glass-card">
        <h2 style="margin-top:0">Edit Student</h2>
        <p style="color:#94a3b8; margin-bottom: 2rem;">Update student details and assignment.</p>

        <?php if (!empty($response['message'])): ?>
            <div style="color: <?= $response['success'] ? '#4ade80' : '#f87171' ?>; margin-bottom: 1rem;">
                <?= $response['message'] ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <input type="hidden" name="student_id" value="<?= $student['student_id'] ?>">
            <input type="hidden" name="user_id" value="<?= $student['user_id'] ?>">

            <label>Full Name</label>
            <input type="text" name="fullname" required
                   value="<?= htmlspecialchars($student['fullname']) ?>">

            <label>Email</label>
            <input type="email" name="email" required
                   value="<?= htmlspecialchars($student['email']) ?>">

            <label>Phone</label>
            <input type="text" name="phonenumber"
                   value="<?= htmlspecialchars($student['phonenumber'] ?? '') ?>">

            <hr style="border-color: rgba(255,255,255,0.1); margin: 1.5rem 0;">

            <label>Vehicle</label>
            <select name="vehicle_assigned_id">
                <option value="">Unassigned</option>
                <?php foreach ($vehicles as $v): ?>
                    <option value="<?= $v['vehicle_id'] ?>"
                        <?= ($student['vehicle_assigned_id'] == $v['vehicle_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($v['vehicle_no']) ?> — <?= htmlspecialchars($v['vehicle_model']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Driver</label>
            <select name="driver_assigned_id">
                <option value="">Unassigned</option>
                <?php foreach ($drivers as $d): ?>
                    <option value="<?= $d['driver_id'] ?>"
                        <?= ($student['driver_assigned_id'] == $d['driver_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($d['fullname']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Assigned At</label>
            <input type="datetime-local" name="assigned_at"
                   value="<?= $student['assigned_at']
                       ? date('Y-m-d\TH:i', strtotime($student['assigned_at']))
                       : '' ?>">

            <button type="submit" name="update_student" class="btn">
                Save Changes
            </button>

            <a href="manage_students.php"
               style="display:block; text-align:center; margin-top:1.5rem; color:#94a3b8; text-decoration:none; font-size:0.9rem;">
                Back to Directory
            </a>
        </form>
    </div>
</main>
</body>
</html>
