<?php 
require_once '../config/db.php';
require_once '../includes/header.php';


$dbObject = new Config\Database(); 
$pdo = $dbObject->connect(); 
$driverCount = $pdo->query("SELECT count(*) FROM drivers")->fetchColumn();
?>

<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold text-secondary">Dashboard Overview</h2>
            <p class="text-muted">Welcome back! Here is what's happening today.</p>
        </div>
    </div>

    <div class="row g-4">
        <!-- Card 1: Total Drivers -->
        <div class="col-md-4">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="icon-box bg-primary bg-opacity-10 text-primary me-3">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Total Drivers</h6>
                        <h2 class="mb-0 fw-bold"><?= $driverCount ?></h2>
                    </div>
                </div>
                <div class="card-footer bg-white border-0">
                    <a href="driver_management.php" class="text-primary text-decoration-none small fw-bold">Manage Drivers &rarr;</a>
                </div>
            </div>
        </div>

        <!-- Card 2: Total Students (Placeholder) -->
        <div class="col-md-4">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="icon-box bg-success bg-opacity-10 text-success me-3">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Active Students</h6>
                        <h2 class="mb-0 fw-bold">0</h2> <!-- Placeholder -->
                    </div>
                </div>
                <div class="card-footer bg-white border-0">
                    <a href="#" class="text-success text-decoration-none small fw-bold">View Students &rarr;</a>
                </div>
            </div>
        </div>

        <!-- Card 3: Vehicles (Placeholder) -->
        <div class="col-md-4">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="icon-box bg-warning bg-opacity-10 text-warning me-3">
                        <i class="fa-solid fa-truck"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Fleet Size</h6>
                        <h2 class="mb-0 fw-bold">0</h2> <!-- Placeholder -->
                    </div>
                </div>
                <div class="card-footer bg-white border-0">
                    <a href="#" class="text-warning text-decoration-none small fw-bold">Manage Fleet &rarr;</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Section -->
    <div class="row mt-5">
        <div class="col-12">
            <h4 class="fw-bold text-secondary mb-3">Quick Actions</h4>
        </div>
        <div class="col-md-3">
            <a href="add_driver.php" class="btn btn-outline-primary w-100 p-3 shadow-sm border-2">
                <i class="fa-solid fa-plus fa-lg mb-2 d-block"></i>
                Add Driver
            </a>
        </div>
        <div class="col-md-3">
            <a href="add_student.php" class="btn btn-outline-success w-100 p-3 shadow-sm border-2">
                <i class="fa-solid fa-user-plus fa-lg mb-2 d-block"></i>
                Register Student
            </a>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
