<?php
session_start();

// ✅ Admin-only (important)
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: ../index.php");
    exit();
}

require_once '../config/db.php';
require_once '../controllers/DriverController.php';

$pdo = (new \Config\Database())->connect();
$driverCtrl = new DriverController($pdo);

// ✅ Handle add
$response = $driverCtrl->handleAddDriver();

function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register Driver | Driving System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    :root{
      --primary:#2563eb;
      --glass: rgba(255,255,255,0.06);
      --border: rgba(255,255,255,0.12);
      --text-muted:#94a3b8;
      --success:#4ade80;
      --error:#f87171;
    }

    *{ box-sizing:border-box; }

    body{
      margin:0;
      font-family:'Inter', sans-serif;
      background:#0f172a;
      color:white;
      min-height:100vh;
      display:flex;
      background-image: radial-gradient(circle at top right, #1e1b4b, #0f172a);
    }

    main{
      flex:1;
      padding:2.5rem;
      margin-left:260px;
      display:flex;
      justify-content:center;
      align-items:center;
    }

    @media (max-width:900px){
      main{ margin-left:0; padding:1.25rem; }
    }

    .glass-card{
      width:100%;
      max-width:520px;
      background: linear-gradient(135deg, var(--glass), rgba(255,255,255,0.03));
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      border:1px solid var(--border);
      border-radius:1.25rem;
      padding:1.75rem;
      box-shadow: 0 22px 55px -40px rgba(0,0,0,0.85);
    }

    .title{
      margin:0 0 .25rem;
      font-weight:800;
      letter-spacing:-.4px;
      font-size:1.45rem;
    }
    .sub{
      margin:0 0 1.25rem;
      color:var(--text-muted);
      font-size:.95rem;
    }

    .alert{
      padding: .9rem 1rem;
      border-radius: .9rem;
      border: 1px solid transparent;
      background: rgba(255,255,255,0.04);
      margin-bottom: 1rem;
      display:flex;
      align-items:center;
      gap:.6rem;
      font-size:.95rem;
    }
    .alert-success{ color:var(--success); border-color: rgba(74,222,128,0.25); }
    .alert-error{ color:var(--error); border-color: rgba(248,113,113,0.25); }

    label{
      display:block;
      font-size:0.78rem;
      color:var(--text-muted);
      text-transform:uppercase;
      letter-spacing:.06em;
      margin-bottom:.5rem;
    }

    .form-control{
      width:100%;
      padding:0.85rem 0.95rem;
      background: rgba(0,0,0,0.22);
      border: 1px solid var(--border);
      border-radius:0.85rem;
      color:white;
      outline:none;
      transition: box-shadow .2s, border-color .2s;
      margin-bottom: 1rem;
    }
    .form-control::placeholder{ color: rgba(148,163,184,0.65); }
    .form-control:focus{
      border-color: rgba(37,99,235,0.75);
      box-shadow: 0 0 0 3px rgba(37,99,235,0.18);
    }

    .btn{
      width:100%;
      border:none;
      padding:.95rem 1rem;
      border-radius:0.9rem;
      font-weight:800;
      cursor:pointer;
      transition: transform .15s ease, opacity .15s ease;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      gap:.55rem;
      text-decoration:none;
    }

    .btn-primary{
      background: linear-gradient(135deg, var(--primary), #1e40af);
      color:#fff;
      box-shadow: 0 16px 28px -18px rgba(37,99,235,0.7);
      border: 1px solid rgba(255,255,255,0.08);
    }
    .btn-primary:hover{ opacity:.95; transform: translateY(-1px); }

    .back{
      display:block;
      text-align:center;
      margin-top:1rem;
      color:var(--text-muted);
      text-decoration:none;
      font-size:.92rem;
    }
    .back:hover{ color:#e2e8f0; }
  </style>
</head>

<body>
  <?php include('../includes/navbar.php'); ?>

  <main>
    <div class="glass-card">
      <h2 class="title">Register New Driver</h2>
      <p class="sub">Create a new driver account (role will be set to <b>driver</b>).</p>

      <?php if (!empty($response['message'])): ?>
        <div class="alert <?= $response['success'] ? 'alert-success' : 'alert-error' ?>">
          <i class="fa-solid <?= $response['success'] ? 'fa-circle-check' : 'fa-triangle-exclamation' ?>"></i>
          <?= h($response['message']) ?>
        </div>
      <?php endif; ?>

      <form method="POST" autocomplete="off">
        <div>
          <label>Full Name</label>
          <input class="form-control" type="text" name="fullname" placeholder="Full Name" required>
        </div>

        <div>
          <label>Email Address</label>
          <input class="form-control" type="email" name="email" placeholder="Email Address" required>
        </div>

        <div>
          <label>Phone Number (optional)</label>
          <input class="form-control" type="text" name="phonenumber" placeholder="Phone Number">
        </div>

        <div>
          <label>Temporary Password</label>
          <input class="form-control" type="password" name="password" placeholder="Temporary Password" required>
        </div>

        <button type="submit" name="add_new_person" class="btn btn-primary">
          <i class="fa-solid fa-plus"></i> Register Driver
        </button>

        <a href="manage_drivers.php" class="back">← Back to Directory</a>
      </form>
    </div>
  </main>
</body>
</html>
