<?php
session_start();
require_once '../config/db.php';
require_once '../controllers/DriverController.php';

$pdo = (new \Config\Database())->connect();
$driverCtrl = new DriverController($pdo);

// Get the specific driver data
$driverData = $driverCtrl->getDriver($_GET['id']);
$driverCtrl->handleUpdateDriver();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Tutor | Driving System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #6366f1; --glass: rgba(255, 255, 255, 0.03); --border: rgba(255, 255, 255, 0.1); }
        body { margin: 0; font-family: 'Inter', sans-serif; background: #0f172a; color: white; display: flex; background-image: radial-gradient(circle at top right, #1e1b4b, #0f172a); }
        main { flex: 1; padding: 3rem; margin-left: 260px; display: flex; justify-content: center; align-items: center; }
        .glass-card { background: var(--glass); backdrop-filter: blur(12px); border: 1px solid var(--border); border-radius: 1.5rem; padding: 2.5rem; width: 100%; max-width: 500px; }
        input { width: 100%; padding: 0.8rem; background: rgba(0,0,0,0.2); border: 1px solid var(--border); border-radius: 0.75rem; color: white; margin-bottom: 1.2rem; box-sizing: border-box; }
        .btn { width: 100%; background: #10b981; color: white; border: none; padding: 1rem; border-radius: 0.75rem; font-weight: 600; cursor: pointer; }
    </style>
</head>
<body>
    <?php include('../includes/navbar.php'); ?>
    <main>
        <div class="glass-card">
            <h2 style="margin-top:0">Edit Tutor Profile</h2>

            <?php if (!$driverData): ?>
                <div style="color:#f87171;">Tutor not found.</div>
            <?php else: ?>
                <form method="POST">
                    <input type="hidden" name="driver_id" value="<?= $driverData['driver_id'] ?>">
                    <input type="hidden" name="user_id" value="<?= $driverData['user_id'] ?>">

                    <label style="font-size: 0.8rem; color: #94a3b8;">Full Name</label>
                    <input type="text" name="fullname"
                           value="<?= htmlspecialchars($driverData['fullname']) ?>" required>

                    <label style="font-size: 0.8rem; color: #94a3b8;">Email Address</label>
                    <input type="email" name="email"
                           value="<?= htmlspecialchars($driverData['email']) ?>" required>

                    <label style="font-size: 0.8rem; color: #94a3b8;">Phone Number</label>
                    <input type="text" name="phonenumber"
                           value="<?= htmlspecialchars($driverData['phonenumber']) ?>">

                    <button type="submit" name="update_driver" class="btn">
                        Update Information
                    </button>

                    <a href="manage_drivers.php"
                       style="display:block; text-align:center; margin-top:1.5rem; color:#94a3b8; text-decoration:none; font-size:0.9rem;">
                        Cancel Changes
                    </a>
                </form>
            <?php endif; ?>

        </div>
    </main>
</body>
</html>
