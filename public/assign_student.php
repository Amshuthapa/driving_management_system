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

// ✅ Handle assignment first
$response = $studentCtrl->handleAssignStudent();

// ✅ Handle delete (optional)
$studentCtrl->handleDelete();

// ✅ Pull only requested students (better UX + safe)
$students = $pdo->query("
    SELECT 
        s.student_id,
        s.user_id,
        u.fullname,
        u.email,
        u.phonenumber,
        s.requested_date,
        s.requested_time,
        s.booking_status
    FROM Students s
    JOIN Users u ON s.user_id = u.user_id
    WHERE s.booking_status = 'requested'
    ORDER BY s.student_id DESC
")->fetchAll(PDO::FETCH_ASSOC);

// ✅ For each requested student, build slot-specific available drivers & vehicles
$availableDriversByStudent = [];
$availableVehiclesByStudent = [];

foreach ($students as $s) {
    $date = $s['requested_date'];
    $time = $s['requested_time'];

    // Drivers available for this slot:
    $stmtD = $pdo->prepare("
        SELECT d.driver_id, u.fullname
        FROM Drivers d
        JOIN Users u ON d.user_id = u.user_id
        WHERE d.is_active = 1
          AND d.driver_id NOT IN (
              SELECT driver_assigned_id
              FROM Students
              WHERE requested_date = ? AND requested_time = ?
                AND booking_status = 'assigned'
                AND driver_assigned_id IS NOT NULL
          )
        ORDER BY u.fullname ASC
    ");
    $stmtD->execute([$date, $time]);
    $availableDriversByStudent[$s['student_id']] = $stmtD->fetchAll(PDO::FETCH_ASSOC);

    // Vehicles available for this slot:
    $stmtV = $pdo->prepare("
        SELECT vehicle_id, vehicle_no, vehicle_model
        FROM Vehicles
        WHERE is_active = 1
          AND vehicle_id NOT IN (
              SELECT vehicle_assigned_id
              FROM Students
              WHERE requested_date = ? AND requested_time = ?
                AND booking_status = 'assigned'
                AND vehicle_assigned_id IS NOT NULL
          )
        ORDER BY vehicle_id DESC
    ");
    $stmtV->execute([$date, $time]);
    $availableVehiclesByStudent[$s['student_id']] = $stmtV->fetchAll(PDO::FETCH_ASSOC);
}

function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Assign Students | Driving System</title>
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
  font-weight:800;
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
}

.table-wrap{ overflow:auto; }

table{
  width:100%;
  border-collapse:collapse;
  min-width: 900px;
}

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
  vertical-align:middle;
}

.badge{
  display:inline-flex;
  align-items:center;
  gap:.4rem;
  padding:.35rem .7rem;
  border-radius:999px;
  font-weight:800;
  font-size:.78rem;
  border:1px solid rgba(255,255,255,0.12);
  background:rgba(255,255,255,0.06);
}

.badge-primary{
  color:#bfdbfe;
  border-color:rgba(37,99,235,0.35);
  background:rgba(37,99,235,0.12);
}

.badge-muted{
  color:var(--text-muted);
}

.actions{
  display:flex;
  gap:.7rem;
  align-items:center;
}

.icon-btn{
  color:var(--text-muted);
  text-decoration:none;
  display:inline-flex;
  align-items:center;
  justify-content:center;
  width:40px;
  height:40px;
  border-radius:12px;
  border:1px solid rgba(255,255,255,0.10);
  background:rgba(255,255,255,0.04);
  transition:.18s ease;
}

.icon-btn:hover{
  color:white;
  border-color:rgba(37,99,235,0.35);
  box-shadow:0 0 0 3px rgba(37,99,235,0.14);
}

.icon-btn.danger:hover{
  border-color:rgba(248,113,113,0.4);
  box-shadow:0 0 0 3px rgba(248,113,113,0.12);
  color:#fecaca;
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

/* Modal */
.modal{
  display:none;
  position:fixed;
  inset:0;
  background:rgba(0,0,0,0.65);
  backdrop-filter:blur(6px);
  -webkit-backdrop-filter:blur(6px);
  justify-content:center;
  align-items:center;
  z-index:9999;
  padding:1.25rem;
}

.modal-box{
  width:100%;
  max-width:520px;
  background:rgba(15,23,42,0.95);
  border:1px solid rgba(255,255,255,0.14);
  border-radius:1.25rem;
  padding:1.35rem;
  box-shadow:0 30px 70px -45px rgba(0,0,0,0.95);
}

.modal-title{
  margin:0;
  font-weight:900;
  letter-spacing:-.3px;
}

.modal-sub{
  margin:.35rem 0 1rem;
  color:var(--text-muted);
}

label{
  display:block;
  font-size:.78rem;
  color:var(--text-muted);
  text-transform:uppercase;
  letter-spacing:.08em;
  margin:.85rem 0 .4rem;
}

select{
  width:100%;
  padding:.85rem .9rem;
  background:rgba(0,0,0,0.22);
  border:1px solid rgba(255,255,255,0.14);
  border-radius:.9rem;
  color:white;
  outline:none;
}

select:focus{
  border-color:rgba(37,99,235,0.7);
  box-shadow:0 0 0 3px rgba(37,99,235,0.18);
}

.btn-row{
  display:flex;
  gap:.8rem;
  justify-content:flex-end;
  margin-top:1.2rem;
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

.small-note{
  margin-top:.7rem;
  color:var(--text-muted);
  font-size:.9rem;
}
</style>
</head>

<body>
<?php include('../includes/navbar.php'); ?>

<main>

  <div class="header">
    <div>
      <h2>Assign Students</h2>
      <p>Only students who requested a booking are shown here. Assign driver & vehicle for their requested slot.</p>
    </div>
  </div>

  <?php if (!empty($response['message'])): ?>
    <div class="alert <?= !empty($response['success']) ? 'alert-success' : 'alert-error' ?>">
      <i class="fa-solid <?= !empty($response['success']) ? 'fa-circle-check' : 'fa-triangle-exclamation' ?>"></i>
      <?= h($response['message']) ?>
    </div>
  <?php endif; ?>

  <div class="glass-card">
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Student</th>
            <th>Contact</th>
            <th>Requested Slot</th>
            <th>Status</th>
            <th>Available</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>

        <?php if (empty($students)): ?>
          <tr>
            <td colspan="6" style="text-align:center; padding:2.5rem; color:var(--text-muted);">
              No booking requests right now.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($students as $s): 
            $sid = (int)$s['student_id'];
            $slot = ($s['requested_date'] && $s['requested_time']) ? ($s['requested_date'].' '.$s['requested_time']) : '—';
            $dCount = isset($availableDriversByStudent[$sid]) ? count($availableDriversByStudent[$sid]) : 0;
            $vCount = isset($availableVehiclesByStudent[$sid]) ? count($availableVehiclesByStudent[$sid]) : 0;
          ?>
            <tr>
              <td>
                <div style="font-weight:900;"><?= h($s['fullname']) ?></div>
                <div style="color:var(--text-muted); font-size:.9rem;">#<?= $sid ?></div>
              </td>

              <td>
                <div><?= h($s['email']) ?></div>
                <div style="color:var(--text-muted); font-size:.9rem;"><?= h($s['phonenumber'] ?: 'N/A') ?></div>
              </td>

              <td>
                <span class="badge badge-primary">
                  <i class="fa-solid fa-calendar"></i> <?= h($slot) ?>
                </span>
              </td>

              <td>
                <span class="badge badge-muted">
                  <i class="fa-solid fa-clock"></i> REQUESTED
                </span>
              </td>

              <td>
                <div style="color:var(--text-muted); font-size:.92rem;">
                  Drivers: <strong style="color:white;"><?= (int)$dCount ?></strong> |
                  Vehicles: <strong style="color:white;"><?= (int)$vCount ?></strong>
                </div>
              </td>

              <td>
                <div class="actions">
                  <a class="icon-btn" href="edit_student.php?id=<?= $sid ?>" title="Edit student">
                    <i class="fa-solid fa-pen-to-square"></i>
                  </a>

                  <a class="icon-btn"
                     href="javascript:void(0)"
                     title="Assign driver & vehicle"
                     onclick="openAssignModal(<?= $sid ?>, '<?= h(addslashes($s['fullname'])) ?>')">
                    <i class="fa-solid fa-car-side"></i>
                  </a>

                  <a class="icon-btn danger"
                     href="?delete_id=<?= $sid ?>"
                     title="Delete student"
                     onclick="return confirm('Delete this student? This cannot be undone.')">
                    <i class="fa-solid fa-trash"></i>
                  </a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>

        </tbody>
      </table>
    </div>
  </div>

</main>

<!-- ASSIGN MODAL -->
<div id="assignModal" class="modal">
  <div class="modal-box">
    <h3 class="modal-title">Assign Driver & Vehicle</h3>
    <p class="modal-sub" id="modalStudentName">Assign for requested slot.</p>

    <form method="POST">
      <input type="hidden" name="student_id" id="modalStudentId">

      <label>Vehicle (available for this slot)</label>
      <select name="vehicle_assigned_id" id="modalVehicleSelect" required>
        <option value="">Select Vehicle</option>
      </select>

      <label>Driver (available for this slot)</label>
      <select name="driver_assigned_id" id="modalDriverSelect" required>
        <option value="">Select Driver</option>
      </select>

      <div class="btn-row">
        <button type="button" class="btn btn-ghost" onclick="closeAssignModal()">
          Cancel
        </button>

        <button type="submit" name="assign_student" class="btn btn-primary">
          <i class="fa-solid fa-check"></i> Assign
        </button>
      </div>

      <div class="small-note">
        Note: options shown here are already filtered by availability for the student’s requested date/time.
      </div>
    </form>
  </div>
</div>

<script>
// slot-specific options from PHP
const availableDriversByStudent = <?= json_encode($availableDriversByStudent, JSON_UNESCAPED_UNICODE) ?>;
const availableVehiclesByStudent = <?= json_encode($availableVehiclesByStudent, JSON_UNESCAPED_UNICODE) ?>;

const modal = document.getElementById('assignModal');
const modalStudentId = document.getElementById('modalStudentId');
const modalStudentName = document.getElementById('modalStudentName');

const modalDriverSelect = document.getElementById('modalDriverSelect');
const modalVehicleSelect = document.getElementById('modalVehicleSelect');

function openAssignModal(studentId, name){
  modalStudentId.value = studentId;
  modalStudentName.textContent = "Assign for: " + name;

  // reset selects
  modalDriverSelect.innerHTML = '<option value="">Select Driver</option>';
  modalVehicleSelect.innerHTML = '<option value="">Select Vehicle</option>';

  const drivers = availableDriversByStudent[studentId] || [];
  const vehicles = availableVehiclesByStudent[studentId] || [];

  drivers.forEach(d => {
    const opt = document.createElement('option');
    opt.value = d.driver_id;
    opt.textContent = d.fullname;
    modalDriverSelect.appendChild(opt);
  });

  vehicles.forEach(v => {
    const opt = document.createElement('option');
    opt.value = v.vehicle_id;
    opt.textContent = `${v.vehicle_no} — ${v.vehicle_model}`;
    modalVehicleSelect.appendChild(opt);
  });

  // If nothing available, block submit (but controller also blocks)
  if (drivers.length === 0 || vehicles.length === 0){
    // add a disabled notice option
    if (drivers.length === 0) {
      const opt = document.createElement('option');
      opt.value = "";
      opt.textContent = "No drivers available for this slot";
      opt.disabled = true;
      opt.selected = true;
      modalDriverSelect.appendChild(opt);
    }
    if (vehicles.length === 0) {
      const opt = document.createElement('option');
      opt.value = "";
      opt.textContent = "No vehicles available for this slot";
      opt.disabled = true;
      opt.selected = true;
      modalVehicleSelect.appendChild(opt);
    }
  }

  modal.style.display = 'flex';
}

function closeAssignModal(){
  modal.style.display = 'none';
}

modal.addEventListener('click', (e) => {
  if (e.target === modal) closeAssignModal();
});
</script>

</body>
</html>
