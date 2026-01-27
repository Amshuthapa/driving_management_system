<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { header("Location: ../index.php"); exit(); }

require_once '../config/db.php';
require_once '../controllers/DriverController.php';

$pdo = (new \Config\Database())->connect();
$driverCtrl = new DriverController($pdo);

// Handle Delete Action
$driverCtrl->handleDelete();

$drivers = $driverCtrl->listDrivers();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tutor Directory | Driving System</title>
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
                <h2 style="margin:0">Tutor Directory</h2>
                <p style="color:var(--text-muted); margin:5px 0 0;">Manage and view all registered driving instructors.</p>
            </div>
            <a href="add_driver.php" class="btn-add"><i class="fa-solid fa-plus me-2"></i> Register New Tutor</a>
        </div>

        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
            <div style="background:rgba(248,113,113,0.1); color:#f87171; padding:1rem; border-radius:0.75rem; margin-bottom:2rem; border:1px solid rgba(248,113,113,0.2);">
                Tutor record has been removed successfully.
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
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($drivers)): ?>
                        <tr><td colspan="5" style="text-align:center; padding:3rem; color:var(--text-muted);">No tutors found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($drivers as $d): ?>
                        <tr>
                            <td style="color:var(--text-muted)">#<?= $d['driver_id'] ?></td>
                            <td style="font-weight:600"><?= htmlspecialchars($d['fullname']) ?></td>
                            <td><?= htmlspecialchars($d['email']) ?></td>
                            <td><?= htmlspecialchars($d['phonenumber'] ?: 'N/A') ?></td>
                            <td class="action-links">
                                <a href="edit_driver.php?id=<?= $d['driver_id'] ?>" class="edit"><i class="fa-solid fa-pen-to-square"></i></a>
                                <a href="?delete_id=<?= $d['driver_id'] ?>" class="del" onclick="return confirm('Delete this tutor? This cannot be undone.')"><i class="fa-solid fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>