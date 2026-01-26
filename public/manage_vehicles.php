<?php
session_start();

// 1. Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// 2. Dependencies
require_once '../config/db.php';
require_once '../controllers/VehicleController.php';

// 3. Initialize Database and Controller
$dbObject = new \Config\Database();
$pdo = $dbObject->connect();
$vehicleCtrl = new VehicleController($pdo);

// 4. Handle Actions (Add/Delete)
$response = $vehicleCtrl->handleAddVehicle();
$vehicles = $vehicleCtrl->listVehicles();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Management | Driving System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #6366f1;
            --glass: rgba(255, 255, 255, 0.03);
            --glass-hover: rgba(255, 255, 255, 0.08);
            --border: rgba(255, 255, 255, 0.1);
            --text-muted: #94a3b8;
            --success: #4ade80;
            --error: #f87171;
        }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: #0f172a;
            color: white;
            min-height: 100vh;
            background-image: radial-gradient(circle at top right, #1e1b4b, #0f172a);
            display: flex;
        }

        main {
            flex: 1;
            padding: 3rem;
            margin-left: 300px; /* Space for the fixed sidebar */
        }

        .glass-card {
            background: var(--glass);
            backdrop-filter: blur(12px);
            border: 1px solid var(--border);
            border-radius: 1.5rem;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        h2 { font-weight: 600; margin-bottom: 2rem; }

        /* Form Grid */
        .grid-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            align-items: flex-end;
        }

        .form-group { display: flex; flex-direction: column; gap: 8px; }
        
        label { font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }

        input {
            width: 100%;
            padding: 0.8rem;
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid var(--border);
            border-radius: 0.75rem;
            color: white;
            outline: none;
            transition: border 0.3s;
        }

        input:focus { border-color: var(--primary); }

        .btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 0.85rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, opacity 0.2s;
        }

        .btn:hover { opacity: 0.9; transform: translateY(-1px); }

        /* Table Design */
        .table-container { overflow-x: auto; }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        th { text-align: left; color: var(--text-muted); padding: 1.2rem; border-bottom: 1px solid var(--border); font-size: 0.9rem; }
        td { padding: 1.2rem; border-bottom: 1px solid rgba(255, 255, 255, 0.05); }

        .plate-badge {
            background: rgba(99, 102, 241, 0.1);
            color: var(--primary);
            padding: 4px 12px;
            border-radius: 6px;
            font-family: monospace;
            font-weight: 600;
            border: 1px solid rgba(99, 102, 241, 0.2);
        }

        .alert {
            padding: 1rem;
            border-radius: 0.75rem;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            border: 1px solid transparent;
        }
        .alert-success { background: rgba(74, 222, 128, 0.1); color: var(--success); border-color: rgba(74, 222, 128, 0.2); }
        .alert-error { background: rgba(248, 113, 113, 0.1); color: var(--error); border-color: rgba(248, 113, 113, 0.2); }
    </style>
</head>
<body>

    <?php include('../includes/navbar.php'); ?>

    <main>
        <header>
            <h2>Fleet Management</h2>
        </header>

        <?php if ($response['message']): ?>
            <div class="alert <?= $response['success'] ? 'alert-success' : 'alert-error' ?>">
                <i class="fa-solid <?= $response['success'] ? 'fa-check-circle' : 'fa-exclamation-triangle' ?> me-2"></i>
                <?= htmlspecialchars($response['message']) ?>
            </div>
        <?php endif; ?>

        <div class="glass-card">
            <form method="POST" class="grid-form">
                <div class="form-group">
                    <label>Vehicle Type</label>
                    <input type="text" name="vehicle_type" placeholder="e.g., SUV, Sedan, Truck" required>
                </div>
                <div class="form-group">
                    <label>Model Name</label>
                    <input type="text" name="vehicle_model" placeholder="e.g., Toyota RAV4" required>
                </div>
                <div class="form-group">
                    <label>License Plate</label>
                    <input type="text" name="vehicle_no" placeholder="e.g., NY-9982" required>
                </div>
                <button type="submit" name="add_vehicle" class="btn">
                    <i class="fa-solid fa-plus me-2"></i>Add Vehicle
                </button>
            </form>
        </div>

        <div class="glass-card">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th># ID</th>
                            <th>Category</th>
                            <th>Vehicle Model</th>
                            <th>Plate Number</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($vehicles)): ?>
                            <tr>
                                <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 3rem;">
                                    No vehicles found in the system.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($vehicles as $vehicle): ?>
                                <tr>
                                    <td style="color: var(--text-muted);">#<?= $vehicle['vehicle_id'] ?></td>
                                    <td><?= htmlspecialchars($vehicle['vehicle_type']) ?></td>
                                    <td><?= htmlspecialchars($vehicle['vehicle_model']) ?></td>
                                    <td>
                                        <span class="plate-badge"><?= htmlspecialchars($vehicle['vehicle_no']) ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

</body>
</html>