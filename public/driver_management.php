<?php 
require_once '../config/db.php';
require_once '../controllers/DriverController.php';

// Database Connection
$dbObject = new Config\Database(); 
$pdo = $dbObject->connect(); 

$controller = new DriverController($pdo);
$drivers = $controller->index();

// INCLUDE HEADER
require_once '../includes/header.php';
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <div class="card p-4">
                
                <!-- Title & Add Button -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0 fw-bold text-secondary">Driver Directory</h4>
                    <a href="add_driver.php" class="btn btn-add text-decoration-none shadow-sm">
                        <i class="fa-solid fa-plus me-1"></i> Add New Driver
                    </a>
                </div>

                <!-- Search Bar -->
                <div class="position-relative mb-4">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="text" id="liveSearch" class="form-control search-input" placeholder="Search drivers by name, license, or vehicle...">
                </div>
                
                <!-- Search Results -->
                <div id="searchResults" class="list-group mb-3 shadow-sm"></div>

                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>License Number</th>
                                <th>Vehicle Type</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($drivers)): ?>
                                <?php foreach($drivers as $d): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded-circle d-flex justify-content-center align-items-center me-3" style="width: 40px; height: 40px;">
                                                <i class="fa-solid fa-user text-secondary"></i>
                                            </div>
                                            <span class="fw-bold text-dark"><?= htmlspecialchars($d['name']) ?></span>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($d['license_number']) ?></span></td>
                                    <td>
                                        <?php 
                                            $icon = 'fa-car';
                                            $v = strtolower($d['vehicle_type']);
                                            if(str_contains($v, 'truck')) $icon = 'fa-truck';
                                            if(str_contains($v, 'bus')) $icon = 'fa-bus';
                                            if(str_contains($v, 'bike') || str_contains($v, 'cycle')) $icon = 'fa-motorcycle';
                                        ?>
                                        <i class="fa-solid <?= $icon ?> text-muted me-1"></i> <?= htmlspecialchars($d['vehicle_type']) ?>
                                    </td>
                                    <td class="text-end">
                                        <a href="delete_driver.php?id=<?= $d['id'] ?>" 
                                            class="btn btn-outline-danger btn-sm action-btn rounded-circle" 
                                            onclick="return confirm('Delete this driver?')"
                                            title="Delete">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center p-4">No drivers found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
