<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

require_once '../config/db.php';
require_once '../controllers/StudentController.php';

$pdo = (new \Config\Database())->connect();
$studentCtrl = new StudentController($pdo);

// Handle Delete Action
$studentCtrl->handleDelete();

$students = $studentCtrl->listStudents();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Directory | Driving System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #6366f1; --glass: rgba(255, 255, 255, 0.03); --border: rgba(255, 255, 255, 0.1); --text-muted: #94a3b8; }
        body { margin: 0; font-family: 'Inter', sans-serif; background: #0f172a; color: white; display: flex; background-image: radial-gradient(circle at top right, #1e1b4b, #0f172a); }
        main { flex: 1; padding: 3rem; margin-left: 260px; }

        .header-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .glass-card { background: var(--glass); backdrop-filter: blur(12px); border: 1px solid var(--border); border-radius: 1.5rem; padding: 2rem; }

        .btn-add { background: var(--primary); color: white; text-decoration: none; padding: 0.8rem 1.5rem; border-radius: 0.75rem; font-weight: 600; transition: 0.3s; }
        .btn-add:hover { box-shadow: 0 0 20px rgba(99, 102, 241, 0.4); }

        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; color: var(--text-muted); padding: 1.2rem; border-bottom: 1px solid var(--border); font-size: 0.85rem; text-transform: uppercase; }
        td { padding: 1.2rem; border-bottom: 1px solid rgba(255,255,255,0.05); }

        .action-links a { color: var(--text-muted); margin-right: 15px; transition: 0.3s; text-decoration: none; }
        .action-links a.del:hover { color: #f87171; }
        .action-links a.edit:hover { color: var(--primary); }
    </style>
</head>
<body>
    <?php include('../includes/navbar.php'); ?>

    <main>
        <div class="header-actions">
            <div>
                <h2 style="margin:0">Student Directory</h2>
                <p style="color:var(--text-muted); margin:5px 0 0;">Manage and view all registered students.</p>
            </div>
            <a href="add_student.php" class="btn-add">
                <i class="fa-solid fa-plus me-2"></i> Register New Student
            </a>
        </div>

        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
            <div style="background:rgba(248,113,113,0.1); color:#f87171; padding:1rem; border-radius:0.75rem; margin-bottom:2rem; border:1px solid rgba(248,113,113,0.2);">
                Student record has been removed successfully.
            </div>
        <?php endif; ?>

        <div class="glass-card">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Email Address</th>
                        <th>Phone</th>
                        <th>Vehicle</th>
                        <th>Driver</th>
                        <th>Assigned At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($students)): ?>
                        <tr>
                            <td colspan="8" style="text-align:center; padding:3rem; color:var(--text-muted);">
                                No students found.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($students as $s): ?>
                        <tr>
                            <td style="color:var(--text-muted)">#<?= $s['student_id'] ?></td>
                            <td style="font-weight:600"><?= htmlspecialchars($s['fullname']) ?></td>
                            <td><?= htmlspecialchars($s['email']) ?></td>
                            <td><?= htmlspecialchars($s['phonenumber'] ?: 'N/A') ?></td>

                           <td>
    <?= $s['vehicle_no']
        ? htmlspecialchars($s['vehicle_no'] . ' — ' . $s['vehicle_model'])
        : 'Unassigned' ?>
</td>

<td>
    <?= $s['driver_name']
        ? htmlspecialchars($s['driver_name'])
        : 'Unassigned' ?>
</td>
 <td><?= $s['assigned_at'] ? date('d M Y, H:i', strtotime($s['assigned_at'])) : '—' ?></td>

                            <td class="action-links">
                                <a href="edit_student.php?id=<?= $s['student_id'] ?>" class="edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                              <a href="?delete_id=<?= $s['student_id'] ?>" class="del"
   onclick="return confirm('Delete this student? This cannot be undone.')">
    <i class="fa-solid fa-trash"></i>
</a>

                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
    <!-- Delete Student Modal -->
<div id="deleteModal" style="
    display:none;
    position:fixed;
    inset:0;
    background:rgba(0,0,0,0.6);
    backdrop-filter: blur(4px);
    justify-content:center;
    align-items:center;
    z-index:9999;
">
    <div style="
        background: rgba(15,23,42,0.95);
        border:1px solid rgba(255,255,255,0.1);
        border-radius:1.5rem;
        padding:2rem;
        width:100%;
        max-width:420px;
        box-shadow:0 0 40px rgba(0,0,0,0.4);
    ">
        <h3 style="margin-top:0; color:#f87171;">Delete Student</h3>
        <p style="color:#94a3b8; margin-bottom:1.5rem;">
            Are you sure you want to delete
            <strong id="studentName"></strong>?
            <br>This action cannot be undone.
        </p>

        <div style="display:flex; gap:1rem; justify-content:flex-end;">
            <button onclick="closeDeleteModal()"
                style="
                    background:transparent;
                    border:1px solid rgba(255,255,255,0.15);
                    color:white;
                    padding:0.6rem 1.2rem;
                    border-radius:0.75rem;
                    cursor:pointer;
                ">
                Cancel
            </button>

            <a id="confirmDeleteBtn"
               style="
                    background:#f87171;
                    color:#0f172a;
                    text-decoration:none;
                    padding:0.6rem 1.2rem;
                    border-radius:0.75rem;
                    font-weight:600;
               ">
                Delete
            </a>
        </div>
    </div>
</div>

</body>
</html>
