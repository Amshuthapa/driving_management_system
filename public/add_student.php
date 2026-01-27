<?php
session_start();
require_once '../config/db.php';
require_once '../controllers/StudentController.php';

$pdo = (new \Config\Database())->connect();
$studentCtrl = new StudentController($pdo);
$response = $studentCtrl->handleAddStudent(); // Uses the logic we built earlier
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register Student | Driving System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #6366f1; --glass: rgba(255, 255, 255, 0.03); --border: rgba(255, 255, 255, 0.1); }
        body { margin: 0; font-family: 'Inter', sans-serif; background: #0f172a; color: white; display: flex; background-image: radial-gradient(circle at top right, #1e1b4b, #0f172a); }
        main { flex: 1; padding: 3rem; margin-left: 260px; display: flex; justify-content: center; align-items: center; }
        .glass-card { background: var(--glass); backdrop-filter: blur(12px); border: 1px solid var(--border); border-radius: 1.5rem; padding: 2.5rem; width: 100%; max-width: 500px; }
        input { width: 100%; padding: 0.8rem; background: rgba(0,0,0,0.2); border: 1px solid var(--border); border-radius: 0.75rem; color: white; margin-bottom: 1.2rem; box-sizing: border-box; }
        .btn { width: 100%; background: var(--primary); color: white; border: none; padding: 1rem; border-radius: 0.75rem; font-weight: 600; cursor: pointer; }
    </style>
</head>
<body>
    <?php include('../includes/navbar.php'); ?>

    <main>
        <div class="glass-card">
            <h2 style="margin-top:0">Register New Student</h2>
            <p style="color:#94a3b8; margin-bottom: 2rem;">Create a new student account.</p>
            
            <?php if($response): ?>
                <div style="color: <?= $response['success'] ? '#4ade80' : '#f87171' ?>; margin-bottom: 1rem;">
                    <?= $response['message'] ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <input type="text" name="fullname" placeholder="Full Name" required>
                <input type="email" name="email" placeholder="Email Address" required>
                <input type="text" name="phonenumber" placeholder="Phone Number">
                <input type="password" name="password" placeholder="Temporary Password" required>

                <!-- Optional: assignment fields (can remove for now) -->
                <!--
                <input type="number" name="vehicle_assigned_id" placeholder="Vehicle ID">
                <input type="number" name="driver_assigned_id" placeholder="Driver ID">
                -->

                <button type="submit" name="add_new_person" class="btn">
                    Register Student
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
