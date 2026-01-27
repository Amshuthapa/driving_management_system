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

// ✅ Handle admin booking request
$response = $studentCtrl->handleAdminBookingRequest();

// ✅ Get all students (so admin can book for any student)
$students = $pdo->query("
    SELECT 
        s.student_id,
        u.fullname,
        u.email,
        u.phonenumber,
        s.booking_status,
        s.requested_date,
        s.requested_time
    FROM Students s
    JOIN Users u ON s.user_id = u.user_id
    ORDER BY s.student_id DESC
")->fetchAll(PDO::FETCH_ASSOC);

function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Booking | Driving System</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
:root{
  --primary:#2563eb;
  --glass:rgba(255,255,255,0.04);
  --glass2:rgba(255,255,255,0.02);
  --border:rgba(255,255,255,0.12);
  --text-muted:#94a3b8;
  --success:#4ade80;
  --error:#f87171;
}

*{ box-sizing:border-box; }

body{
  margin:0;
  font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
  background:#0f172a;
  color:white;
  display:flex;
  min-height:100vh;
  background-image:radial-gradient(circle at top right,#1e1b4b,#0f172a);
}

main{
  flex:1;
  padding:2.5rem;
  margin-left:260px;
}

@media (max-width:900px){
  main{ margin-left:0; padding:1.25rem; }
}

.header{
  display:flex;
  justify-content:space-between;
  align-items:flex-end;
  gap:1rem;
  margin-bottom:1.5rem;
}

.header h2{
  margin:0;
  font-weight:900;
  letter-spacing:-.3px;
}

.header p{
  margin:.35rem 0 0;
  color:var(--text-muted);
}

.glass-card{
  background:linear-gradient(135deg, var(--glass), var(--glass2));
  backdrop-filter:blur(14px);
  -webkit-backdrop-filter:blur(14px);
  border:1px solid var(--border);
  border-radius:1.25rem;
  padding:1.5rem;
  box-shadow:0 20px 50px -35px rgba(0,0,0,0.9);
  margin-bottom:1rem;
}

.alert{
  margin-bottom:1rem;
  padding:.9rem 1rem;
  border-radius:1rem;
  border:1px solid transparent;
  background:rgba(255,255,255,0.04);
  display:flex;
  align-items:center;
  gap:.6rem;
}
.alert-success{ color:var(--success); border-color:rgba(74,222,128,0.2); }
.alert-error{ color:var(--error); border-color:rgba(248,113,113,0.2); }

label{
  display:block;
  font-size:.78rem;
  color:var(--text-muted);
  text-transform:uppercase;
  letter-spacing:.08em;
  margin:.85rem 0 .4rem;
}

select, input{
  width:100%;
  padding:.85rem .9rem;
  background:rgba(0,0,0,0.22);
  border:1px solid rgba(255,255,255,0.14);
  border-radius:.9rem;
  color:white;
  outline:none;
}

select:focus, input:focus{
  border-color:rgba(37,99,235,0.7);
  box-shadow:0 0 0 3px rgba(37,99,235,0.18);
}

.grid{
  display:grid;
  grid-template-columns: 1.2fr 1fr 1fr;
  gap:1rem;
}

@media (max-width:900px){
  .grid{ grid-template-columns: 1fr; }
}

.btn-row{
  display:flex;
  gap:.8rem;
  justify-content:flex-end;
  margin-top:1.2rem;
  flex-wrap:wrap;
}

.btn{
  border:none;
  padding:.85rem 1rem;
  border-radius:.95rem;
  font-weight:900;
  cursor:pointer;
  display:inline-flex;
  align-items:center;
  justify-content:center;
  gap:.55rem;
  transition:.15s ease;
  text-decoration:none;
}

.btn-primary{
  background:linear-gradient(135deg, var(--primary), #1e40af);
  color:white;
  border:1px solid rgba(255,255,255,0.10);
  box-shadow:0 16px 28px -18px rgba(37,99,235,0.7);
}
.btn-primary:hover{ transform:translateY(-1px); opacity:.96; }

.btn-ghost{
  background:rgba(255,255,255,0.06);
  border:1px solid rgba(255,255,255,0.14);
  color:white;
}
.btn-ghost:hover{ transform:translateY(-1px); opacity:.96; }

.table-wrap{ overflow:auto; }
table{ width:100%; border-collapse:collapse; min-width: 900px; }
th{
  text-align:left;
  color:var(--text-muted);
  padding:1rem;
  border-bottom:1px solid var(--border);
  font-size:0.78rem;
  text-transform:uppercase;
  letter-spacing:.08em;
  white-space:nowrap;
}
td{
  padding:1rem;
  border-bottom:1px solid rgba(255,255,255,0.06);
}
.badge{
  display:inline-flex;
  align-items:center;
  gap:.4rem;
  padding:.35rem .7rem;
  border-radius:999px;
  font-weight:900;
  font-size:.78rem;
  border:1px solid rgba(255,255,255,0.12);
  background:rgba(255,255,255,0.06);
}
.badge-primary{
  color:#bfdbfe;
  border-color:rgba(37,99,235,0.35);
  background:rgba(37,99,235,0.12);
}
.badge-muted{ color:var(--text-muted); }

.small-note{
  margin-top:.8rem;
  color:var(--text-muted);
  font-size:.92rem;
}
</style>
</head>

<body>
<?php include('../includes/navbar.php'); ?>

<main>

  <div class="header">
    <div>
      <h2>Admin Booking</h2>
      <p>Create booking requests for students (availability is checked before saving).</p>
    </div>

    <a class="btn btn-ghost" href="assign_student.php">
      <i class="fa-solid fa-car-side"></i> Go to Assign Students
    </a>
  </div>

  <?php if (!empty($response['message'])): ?>
    <div class="alert <?= !empty($response['success']) ? 'alert-success' : 'alert-error' ?>">
      <i class="fa-solid <?= !empty($response['success']) ? 'fa-circle-check' : 'fa-triangle-exclamation' ?>"></i>
      <?= h($response['message']) ?>
    </div>
  <?php endif; ?>

  <!-- CREATE BOOKING -->
  <div class="glass-card">
    <h3 style="margin:0; font-weight:900;">Create Booking Request</h3>
    <p style="margin:.35rem 0 0; color:var(--text-muted);">
      After requesting, assign driver & vehicle from “Assign Students”.
    </p>

    <form method="POST" class="grid" style="margin-top:1rem;">
      <div>
        <label>Select Student</label>
        <select name="student_id" required>
          <option value="">Choose student...</option>
          <?php foreach($students as $s): ?>
            <option value="<?= (int)$s['student_id'] ?>">
              #<?= (int)$s['student_id'] ?> — <?= h($s['fullname']) ?> (<?= h($s['email']) ?>)
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div>
        <label>Requested Date</label>
        <input type="date" name="requested_date" required>
      </div>

      <div>
        <label>Requested Time</label>
        <input type="time" name="requested_time" required>
      </div>

      <div class="btn-row" style="grid-column: 1 / -1;">
        <button type="submit" name="admin_request_booking" class="btn btn-primary">
          <i class="fa-solid fa-calendar-check"></i> Create Request
        </button>
      </div>

      <div class="small-note" style="grid-column: 1 / -1;">
        The request will be blocked automatically if no driver or vehicle is available for the selected slot.
      </div>
    </form>
  </div>

  <!-- STUDENT BOOKING OVERVIEW -->
  <div class="glass-card">
    <h3 style="margin:0; font-weight:900;">Current Student Booking Status</h3>
    <p style="margin:.35rem 0 0; color:var(--text-muted);">
      Quick overview of students’ current booking state.
    </p>

    <div class="table-wrap" style="margin-top:1rem;">
      <table>
        <thead>
          <tr>
            <th>Student</th>
            <th>Contact</th>
            <th>Status</th>
            <th>Requested Slot</th>
          </tr>
        </thead>
        <tbody>
          <?php if(empty($students)): ?>
            <tr>
              <td colspan="4" style="text-align:center; padding:2.5rem; color:var(--text-muted);">
                No students found.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach($students as $s): 
              $slot = ($s['requested_date'] && $s['requested_time']) ? ($s['requested_date'].' '.$s['requested_time']) : '—';
              $status = $s['booking_status'] ?: 'none';
            ?>
              <tr>
                <td>
                  <div style="font-weight:900;"><?= h($s['fullname']) ?></div>
                  <div style="color:var(--text-muted); font-size:.9rem;">#<?= (int)$s['student_id'] ?></div>
                </td>
                <td>
                  <div><?= h($s['email']) ?></div>
                  <div style="color:var(--text-muted); font-size:.9rem;"><?= h($s['phonenumber'] ?: 'N/A') ?></div>
                </td>
                <td>
                  <span class="badge <?= $status === 'requested' ? 'badge-primary' : 'badge-muted' ?>">
                    <i class="fa-solid fa-circle-info"></i> <?= strtoupper(h($status)) ?>
                  </span>
                </td>
                <td>
                  <span class="badge badge-muted">
                    <i class="fa-solid fa-calendar"></i> <?= h($slot) ?>
                  </span>
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
