<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driving System Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; min-height: 100vh; display: flex; flex-direction: column; }
        .navbar { box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .nav-link { font-weight: 500; margin-right: 15px; }
        .nav-link:hover { color: #fff !important; }
        .nav-link.active { color: #4f46e5 !important; background: rgba(255,255,255,0.1); border-radius: 8px; }
        .stat-card { border: none; border-radius: 15px; transition: transform 0.3s; }
        .stat-card:hover { transform: translateY(-5px); }
        .icon-box { width: 60px; height: 60px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
        footer { margin-top: auto; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fa-solid fa-car-side me-2"></i> DMS Pro
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <!-- 1. Dashboard Link -->
                    <li class="nav-item">
                        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>" href="index.php">
                            <i class="fa-solid fa-gauge-high me-1"></i> Dashboard
                        </a>
                    </li>
                    <!-- 2. Drivers Link -->
                    <li class="nav-item">
                        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'driver_management.php' ? 'active' : '' ?>" href="driver_management.php">
                            <i class="fa-solid fa-id-card me-1"></i> Drivers
                        </a>
                    </li>
                    <!-- 3. Students Link -->
                    <li class="nav-item">
                        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'students.php' ? 'active' : '' ?>" href="students.php">
                            <i class="fa-solid fa-graduation-cap me-1"></i> Students
                        </a>
                    </li>
                    <!-- 4. Vehicles Link -->
                    <li class="nav-item">
                        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'vehicles.php' ? 'active' : '' ?>" href="vehicles.php">
                            <i class="fa-solid fa-truck me-1"></i> Vehicles
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
