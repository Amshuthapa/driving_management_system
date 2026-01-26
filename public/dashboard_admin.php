<?php 
session_start(); 

// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php"); 
    exit();
}

require_once '../config/db.php'; 

// Database Connection
$dbObject = new \Config\Database();
$pdo = $dbObject->connect(); 

// Fetch Counts
$driverCount  = $pdo->query("SELECT count(*) FROM Users WHERE role = 'driver'")->fetchColumn();
$studentCount = $pdo->query("SELECT count(*) FROM Users WHERE role = 'student'")->fetchColumn();
$vehicleCount = $pdo->query("SELECT count(*) FROM Vehicles")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Driving System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #6366f1;
            --glass: rgba(255, 255, 255, 0.03);
            --glass-hover: rgba(255, 255, 255, 0.08);
            --border: rgba(255, 255, 255, 0.1);
            --text-muted: #94a3b8;
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

        /* Sidebar Glass Effect */
        nav {
            width: 260px;
            background: rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            padding: 2rem 1.5rem;
        }

        nav h1 { font-size: 1.2rem; margin-bottom: 3rem; letter-spacing: -0.5px; }

        .nav-link {
            color: var(--text-muted);
            text-decoration: none;
            padding: 0.8rem 1rem;
            border-radius: 0.75rem;
            margin-bottom: 0.5rem;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .nav-link:hover, .nav-link.active {
            background: var(--glass-hover);
            color: white;
        }

        /* Main Content Area */
        main {
            flex: 1;
            padding: 3rem;
            overflow-y: auto;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 3rem;
        }

        .welcome h2 { margin: 0; font-size: 1.8rem; }
        .welcome p { color: var(--text-muted); margin: 5px 0 0; }

        /* Dashboard Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
        }

        .stat-card {
            background: var(--glass);
            backdrop-filter: blur(12px);
            border: 1px solid var(--border);
            padding: 1.5rem;
            border-radius: 1.5rem;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            background: var(--glass-hover);
        }

        .stat-card i {
            font-size: 1.5rem;
            color: var(--primary);
            margin-bottom: 1rem;
            display: block;
        }

        .stat-card h3 { font-size: 2.2rem; margin: 0.5rem 0; }
        .stat-card p { color: var(--text-muted); font-size: 0.9rem; margin: 0; }

        .logout-btn {
            margin-top: auto;
            color: #f87171;
            text-decoration: none;
            font-weight: 600;
            padding: 0.8rem 1rem;
        }

    </style>
</head>
<body>

    <?php include('../includes/navbar.php'); ?>

    <main style="margin-left: 300px; padding: 3rem; flex: 1;">
        <header>
            <div class="welcome">
                <h2>Overview</h2>
                <p>Hello, <?= htmlspecialchars($_SESSION['fullname']) ?>. Here is what's happening.</p>
            </div>
        </header>

        <div class="stats-grid">
            <div class="stat-card">
                <i class="fa-solid fa-user-tie"></i>
                <h3><?= $driverCount ?></h3>
                <p>Total Drivers</p>
            </div>

            <div class="stat-card">
                <i class="fa-solid fa-graduation-cap"></i>
                <h3><?= $studentCount ?></h3>
                <p>Active Students</p>
            </div>

            <div class="stat-card">
                <i class="fa-solid fa-car"></i>
                <h3><?= $vehicleCount ?></h3>
                <p>Vehicles in Fleet</p>
            </div>
        </div>
    </main>

</body>
</html>