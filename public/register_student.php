<?php
require_once '../config/db.php';
require_once '../controllers/StudentController.php';

$pdo = (new \Config\Database())->connect();
$studentController = new StudentController($pdo);
$result = $studentController->handlePublicStudentRegister();

function h($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Student Registration | Ayush Piyush Driving System</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

  <style>
    :root{
      --primary-blue:#2563eb;
      --primary-blue-dark:#1e40af;
      --glass-border: rgba(255,255,255,0.40);
    }

    body{
      font-family: 'Inter', system-ui, sans-serif;
      background:
        radial-gradient(1200px 600px at 10% 10%, #c7d2fe, transparent 60%),
        radial-gradient(1000px 500px at 90% 20%, #e0e7ff, transparent 55%),
        linear-gradient(135deg, #eef2ff, #f8fafc);
      min-height: 100vh;
    }

    .glass-card{
      background: linear-gradient(135deg, rgba(255,255,255,0.78), rgba(255,255,255,0.50));
      backdrop-filter: blur(22px) saturate(160%);
      -webkit-backdrop-filter: blur(22px) saturate(160%);
      border: 1px solid var(--glass-border);
      border-radius: 20px;
      box-shadow: 0 30px 60px -20px rgba(0,0,0,0.22), inset 0 1px 0 rgba(255,255,255,0.7);
      position: relative;
    }

    .glass-card::before{
      content:"";
      position:absolute;
      inset:-1px;
      border-radius: inherit;
      background: linear-gradient(120deg, transparent, rgba(37,99,235,0.30), transparent);
      opacity: 0.7;
      pointer-events:none;
    }

    .form-control{
      border-radius: 14px;
      padding: 0.75rem 1rem;
    }

    .form-control:focus{
      border-color: var(--primary-blue);
      box-shadow: 0 0 0 0.15rem rgba(37,99,235,0.25);
    }

    .btn-primary{
      background: linear-gradient(135deg, var(--primary-blue), var(--primary-blue-dark));
      border: none;
      border-radius: 14px;
      font-weight: 600;
      padding: 0.75rem 1rem;
      box-shadow: 0 10px 20px rgba(37,99,235,0.30);
    }

    .btn-primary:hover{
      transform: translateY(-1px);
      box-shadow: 0 16px 28px rgba(37,99,235,0.38);
    }

    .btn-outline-primary{
      border-radius: 14px;
      font-weight: 600;
    }

    .page-title{ color:#0f172a; }
    .page-subtitle{ color:#475569; }

    html{ scroll-padding-top: 80px; }
  </style>
</head>
<body>

<!-- Blue Glass Sticky Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark sticky-top"
     style="background: linear-gradient(135deg, rgba(37,99,235,0.75), rgba(30,64,175,0.65));
            backdrop-filter: blur(18px) saturate(160%);
            -webkit-backdrop-filter: blur(18px) saturate(160%);
            border-bottom: 1px solid rgba(255,255,255,0.35);">
  <div class="container">
    <a class="navbar-brand fw-semibold" href="index.php">
      <i class="fa-solid fa-car-side me-2"></i> Ayush Piyush Driving System
    </a>
    <div class="ms-auto">
      <a class="btn btn-outline-light btn-sm" href="login.php">Login</a>
    </div>
  </div>
</nav>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-6">

      <div class="text-center mb-4">
        <h1 class="h3 fw-semibold page-title mb-1">Student Registration</h1>
        <p class="page-subtitle mb-0">Register to enroll in driving classes.</p>
      </div>

      <?php if (!empty($result['message'])): ?>
        <div class="alert <?= $result['success'] ? 'alert-success' : 'alert-danger' ?> rounded-4">
          <?= h($result['message']) ?>
        </div>
      <?php endif; ?>

      <div class="glass-card p-4 p-md-5">
        <form method="POST">

          <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="fullname" class="form-control" placeholder="Your full name" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" placeholder="name@example.com" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Phone Number</label>
            <input type="text" name="phonenumber" class="form-control" placeholder="98XXXXXXXX" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" placeholder="Create a password" required>
          </div>

          <button type="submit" name="register_student" class="btn btn-primary w-100">
            Register
          </button>

          <div class="d-grid gap-2 mt-3">
            <a href="login.php" class="btn btn-outline-primary">
              Already have an account? Login
            </a>
          </div>

        </form>
      </div>

      <p class="text-center text-muted small mt-4 mb-0">
        © <?= date('Y') ?> Ayush Piyush Driving System
      </p>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
