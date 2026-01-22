<?php
// 1. Include necessary files
require_once '../config/db.php';
require_once '../controllers/DriverController.php';

// 2. Connect to the Database
// Fixed the backslashes here (use single backslash for namespace in PHP)
$dbObject = new \Config\Database();
$pdo = $dbObject->connect();

// 3. Initialize Controller
$controller = new DriverController($pdo);

// 4. Handle Submission
$controller->add();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Driver</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card { border: none; border-radius: 15px; box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
        .form-control { border-radius: 10px; padding: 12px; border: 1px solid #e2e8f0; }
        .form-control:focus { box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); border-color: #4f46e5; }
        .input-group-text { background-color: #f8f9fa; border: 1px solid #e2e8f0; border-radius: 10px 0 0 10px; border-right: none; color: #6c757d; }
        .btn-primary { background-color: #4f46e5; border: none; padding: 12px; border-radius: 10px; font-weight: 600; }
        .btn-primary:hover { background-color: #4338ca; }
        .btn-link { color: #6c757d; text-decoration: none; }
        .btn-link:hover { color: #495057; }
        .form-header { background: #4f46e5; color: white; border-radius: 15px 15px 0 0; padding: 25px; text-align: center; }
    </style>
</head>
<body>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                
                <div class="card">
                    <!-- Decorative Header -->
                    <div class="form-header">
                        <i class="fa-solid fa-user-plus fa-3x mb-2"></i>
                        <h4 class="mb-0">Register New Driver</h4>
                        <p class="small text-white-50 mb-0">Enter the driver's details below</p>
                    </div>

                    <div class="card-body p-4">
                        <form method="POST" action="add.php">
                            
                            <!-- Name Field -->
                            <div class="mb-3">
                                <label class="form-label fw-bold text-secondary small">Driver Name</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                                    <input type="text" name="name" class="form-control" placeholder="John Doe" required>
                                </div>
                            </div>

                            <!-- License Field -->
                            <div class="mb-3">
                                <label class="form-label fw-bold text-secondary small">License Number</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-solid fa-id-card"></i></span>
                                    <input type="text" name="license" class="form-control" placeholder="B-12345678" required>
                                </div>
                            </div>

                            <!-- Vehicle Type Field -->
                            <div class="mb-4">
                                <label class="form-label fw-bold text-secondary small">Vehicle Type</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-solid fa-truck-pickup"></i></span>
                                    <input type="text" name="v_type" class="form-control" placeholder="e.g. Truck, Car, Bus" required>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary shadow-sm">
                                    Register Driver
                                </button>
                                <a href="index.php" class="btn btn-light text-muted mt-2">
                                    Cancel
                                </a>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap Script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
