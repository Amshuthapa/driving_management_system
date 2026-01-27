<?php
session_start();
require_once('../config/db.php');
require_once('../controllers/AuthController.php');

$dbObject = new Config\Database();
$pdo = $dbObject->connect();

$auth = new AuthController($pdo);
$error = $auth->login(); // ✅ must run before any output

function h($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login | Ayush Piyush Driving System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

<style>
:root {
  --primary-blue: #2563eb;
  --primary-blue-dark: #1e40af;
  --glass-border: rgba(255, 255, 255, 0.4);
  --glass-highlight: rgba(255, 255, 255, 0.85);
}
body {
  background:
    radial-gradient(1200px 600px at 10% 10%, #c7d2fe, transparent 60%),
    radial-gradient(1000px 500px at 90% 20%, #e0e7ff, transparent 55%),
    linear-gradient(135deg, #e0e7ff, #f8fafc);
  min-height: 100vh;
  font-family: 'Inter', system-ui, sans-serif;
}
.glass-card {
  position: relative;
  background: linear-gradient(135deg, rgba(255,255,255,0.75), rgba(255,255,255,0.45));
  backdrop-filter: blur(22px) saturate(160%);
  -webkit-backdrop-filter: blur(22px) saturate(160%);
  border-radius: 20px;
  border: 1px solid var(--glass-border);
  box-shadow: 0 30px 60px -20px rgba(0,0,0,0.25), inset 0 1px 0 var(--glass-highlight);
}
.glass-card::before {
  content: "";
  position: absolute;
  inset: -1px;
  border-radius: inherit;
  background: linear-gradient(120deg, transparent, rgba(37,99,235,0.35), transparent);
  opacity: 0.6;
  pointer-events: none;
}
.form-control {
  border-radius: 14px;
  background: rgba(255,255,255,0.75);
  border: 1px solid rgba(203,213,225,0.7);
  padding: 0.75rem 1rem;
}
.form-control:focus {
  background: rgba(255,255,255,0.9);
  border-color: var(--primary-blue);
  box-shadow: 0 0 0 0.15rem rgba(37,99,235,0.25), inset 0 1px 2px rgba(0,0,0,0.05);
}
.btn-primary {
  background: linear-gradient(135deg, var(--primary-blue), var(--primary-blue-dark));
  border: none;
  border-radius: 14px;
  font-weight: 600;
  padding: 0.7rem;
  box-shadow: 0 10px 20px rgba(37,99,235,0.35);
}
.btn-primary:hover {
  transform: translateY(-1px);
  box-shadow: 0 14px 26px rgba(37,99,235,0.45);
}
.btn-outline-primary {
  border-radius: 14px;
  font-weight: 600;
  border-color: var(--primary-blue);
  color: var(--primary-blue);
  background: rgba(255,255,255,0.6);
}
.btn-outline-primary:hover {
  background: rgba(37,99,235,0.1);
  color: var(--primary-blue-dark);
}
.page-title { color:#0f172a; }
.page-subtitle { color:#475569; }
hr { opacity: 0.15; }
</style>
</head>
<body>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-5">

      <div class="text-center mb-4">
        <h1 class="h3 fw-semibold page-title">Login</h1>
        <p class="page-subtitle mb-0">Sign in to your account</p>
      </div>

      <?php if ($error): ?>
        <div class="alert alert-danger rounded-4">
          <?= h($error) ?>
        </div>
      <?php endif; ?>

      <div class="glass-card p-4 p-md-5">
        <form method="POST">
          <div class="mb-3">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control" required autofocus>
          </div>

          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>

          <button type="submit" class="btn btn-primary w-100">Sign In</button>
        </form>

        <hr class="my-4">

        <div class="d-grid gap-2">
          <a href="register_student.php" class="btn btn-outline-primary">
            Register as Student
          </a>
          <a href="index.php" class="btn btn-light">
            ← Back to Home
          </a>
        </div>
      </div>

      <p class="text-center text-muted small mt-4">
        © <?= date('Y') ?> Ayush Piyush Driving System
      </p>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
