<?php
session_start();

// ✅ Admin only
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: ../index.php");
    exit();
}

require_once '../config/db.php';
require_once '../controllers/StudentController.php';

$pdo = (new \Config\Database())->connect();
$studentCtrl = new StudentController($pdo);

// ✅ Handle update (POST) - correct method for your controller
$response = $studentCtrl->handleUpdateStudentUserInfo();

function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

// ✅ Get student id
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: manage_students.php");
    exit();
}

// ✅ Fetch student details for form
$student = $studentCtrl->getStudent($id);
if (!$student) {
    header("Location: manage_students.php");
    exit();
}

// reload student after update (so updated values show)
if (!empty($response['success'])) {
    $student = $studentCtrl->getStudent($id);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Student | Driving System</title>
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
      max-width:720px;
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
    }
    .form-control::placeholder{ color: rgba(148,163,184,0.65); }
    .form-control:focus{
      border-color: rgba(37,99,235,0.75);
      box-shadow: 0 0 0 3px rgba(37,99,235,0.18);
    }

    .grid{
      display:grid;
      grid-template-columns: 1fr 1fr;
      gap: 1rem;
    }
    @media (max-width: 720px){
      .grid{ grid-template-columns: 1fr; }
    }

    .meta{
      margin-top: 1rem;
      padding: 1rem;
      border-radius: 1rem;
      border: 1px solid rgba(255,255,255,0.10);
      background: rgba(255,255,255,0.04);
      color: var(--text-muted);
      font-size: .92rem;
      display: grid;
      gap: .45rem;
    }

    .badge-soft{
      display:inline-flex;
      align-items:center;
      gap:.4rem;
      padding:.3rem .65rem;
      border-radius: 999px;
      font-weight: 700;
      font-size: .78rem;
      border: 1px solid rgba(255,255,255,0.10);
      background: rgba(255,255,255,0.06);
      color: #e2e8f0;
      width: fit-content;
    }

    .btn-row{
      display:flex;
      gap:.75rem;
      flex-wrap:wrap;
      margin-top: 1rem;
    }

    .btn{
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
      flex:1;
      background: linear-gradient(135deg, var(--primary), #1e40af);
      color:#fff;
      box-shadow: 0 16px 28px -18px rgba(37,99,235,0.7);
      border: 1px solid rgba(255,255,255,0.08);
    }
    .btn-primary:hover{ opacity:.95; transform: translateY(-1px); }

    .btn-ghost{
      background: rgba(255,255,255,0.06);
      border:1px solid rgba(255,255,255,0.14);
      color:#fff;
    }
    .btn-ghost:hover{ opacity:.95; transform: translateY(-1px); }

    .note{
      color: var(--text-muted);
      font-size: .88rem;
      margin-top: .75rem;
    }
  </style>
</head>

<body>
  <?php include('../includes/navbar.php'); ?>

  <main>
    <div class="glass-card">
      <h2 class="title">Edit Student</h2>
      <p class="sub">Only basic student info is editable here. Booking and assignment are handled from the request/assign flow.</p>

      <?php if (!empty($response['message'])): ?>
        <div class="alert <?= $response['success'] ? 'alert-success' : 'alert-error' ?>">
          <i class="fa-solid <?= $response['success'] ? 'fa-circle-check' : 'fa-triangle-exclamation' ?>"></i>
          <?= h($response['message']) ?>
        </div>
      <?php endif; ?>

      <form method="POST" autocomplete="off">
        <input type="hidden" name="student_id" value="<?= (int)$student['student_id'] ?>">
        <input type="hidden" name="user_id" value="<?= (int)$student['user_id'] ?>">

        <div class="grid">
          <div>
            <label>Full Name</label>
            <input class="form-control" type="text" name="fullname" required value="<?= h($student['fullname']) ?>">
          </div>

          <div>
            <label>Email</label>
            <input class="form-control" type="email" name="email" required value="<?= h($student['email']) ?>">
          </div>

          <div>
            <label>Phone Number</label>
            <input class="form-control" type="text" name="phonenumber" value="<?= h($student['phonenumber'] ?? '') ?>">
          </div>

          <div>
            <label>Status</label>
            <?php
              $status = $student['booking_status'] ?? 'none';
              $label = strtoupper($status);
            ?>
            <div class="badge-soft">
              <i class="fa-solid fa-circle-info"></i> <?= h($label) ?>
            </div>
          </div>
        </div>

        <div class="meta">
          <div><strong>Requested Slot:</strong>
            <?= !empty($student['requested_date']) && !empty($student['requested_time'])
              ? h($student['requested_date'].' '.$student['requested_time'])
              : '—' ?>
          </div>

          <div><strong>Assigned At:</strong>
            <?= !empty($student['assigned_at']) ? h(date('d M Y, H:i', strtotime($student['assigned_at']))) : '—' ?>
          </div>

          <div><strong>Assigned Driver ID:</strong>
            <?= !empty($student['driver_assigned_id']) ? (int)$student['driver_assigned_id'] : '—' ?>
          </div>

          <div><strong>Assigned Vehicle ID:</strong>
            <?= !empty($student['vehicle_assigned_id']) ? (int)$student['vehicle_assigned_id'] : '—' ?>
          </div>
        </div>

        <div class="btn-row">
          <button type="submit" name="update_student" class="btn btn-primary">
            <i class="fa-solid fa-check"></i> Save Changes
          </button>

          <a href="manage_students.php" class="btn btn-ghost">
            <i class="fa-solid fa-arrow-left"></i> Back
          </a>
        </div>

        <div class="note">
          Tip: To assign driver/vehicle for a requested booking, use <strong>Assign Student</strong> page.
        </div>
      </form>
    </div>
  </main>
</body>
</html>
