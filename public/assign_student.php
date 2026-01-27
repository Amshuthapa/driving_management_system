<?php
session_start();
require_once '../config/db.php';
require_once '../controllers/StudentController.php';
require_once '../controllers/DriverController.php';
require_once '../controllers/VehicleController.php';

$pdo = (new \Config\Database())->connect();

$studentCtrl = new StudentController($pdo);
$driverCtrl  = new DriverController($pdo);
$vehicleCtrl = new VehicleController($pdo);

// Handle assignment
$response = $studentCtrl->handleAssignStudent();

// Handle delete
$studentCtrl->handleDelete();

$students = $studentCtrl->listStudents();
$drivers  = $driverCtrl->listDrivers();
$vehicles = $vehicleCtrl->listVehicles();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Assign Students | Driving System</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
:root { --primary:#6366f1; --glass:rgba(255,255,255,0.03); --border:rgba(255,255,255,0.1); --text-muted:#94a3b8; }
body { margin:0; font-family:Inter,sans-serif; background:#0f172a; color:white; display:flex;
       background-image:radial-gradient(circle at top right,#1e1b4b,#0f172a); }
main { flex:1; padding:3rem; margin-left:260px; }

.glass-card { background:var(--glass); backdrop-filter:blur(12px); border:1px solid var(--border);
              border-radius:1.5rem; padding:2rem; }

.header-actions { display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem; }

table { width:100%; border-collapse:collapse; }
th { text-align:left; color:var(--text-muted); padding:1.2rem; border-bottom:1px solid var(--border);
     font-size:0.85rem; text-transform:uppercase; }
td { padding:1.2rem; border-bottom:1px solid rgba(255,255,255,0.05); }

.action-links a { color:var(--text-muted); margin-right:15px; transition:0.3s; text-decoration:none; }
.action-links a:hover { color:var(--primary); }
.action-links a.del:hover { color:#f87171; }

.btn { background:var(--primary); color:white; border:none; padding:0.5rem 1rem;
       border-radius:0.75rem; cursor:pointer; font-weight:600; }
.btn:hover { box-shadow:0 0 15px rgba(99,102,241,0.4); }

select { width:100%; padding:0.7rem; background:rgba(0,0,0,0.2); border:1px solid var(--border);
         border-radius:0.75rem; color:white; margin-bottom:1rem; }

.modal { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6);
         backdrop-filter:blur(6px); justify-content:center; align-items:center; z-index:9999; }

.modal-box { background:rgba(15,23,42,0.95); border:1px solid var(--border); border-radius:1.5rem;
             padding:2rem; width:100%; max-width:420px; }

</style>
</head>

<body>
<?php include('../includes/navbar.php'); ?>

<main>

<div class="header-actions">
  <div>
    <h2 style="margin:0">Assign Students</h2>
    <p style="color:var(--text-muted); margin:5px 0 0;">Assign vehicles and instructors to students.</p>
  </div>
</div>

<?php if ($response): ?>
<div style="color:<?= $response['success'] ? '#4ade80' : '#f87171' ?>; margin-bottom:1.5rem;">
  <?= htmlspecialchars($response['message']) ?>
</div>
<?php endif; ?>

<div class="glass-card">
<table>
<thead>
<tr>
  <th>Student</th>
  <th>Email</th>
  <th>Phone</th>
  <th>Vehicle</th>
  <th>Driver</th>
  <th>Assigned At</th>
  <th>Actions</th>
</tr>
</thead>
<tbody>

<?php if (empty($students)): ?>
<tr><td colspan="7" style="text-align:center; padding:3rem; color:var(--text-muted);">
No students found.</td></tr>

<?php else: foreach ($students as $s): ?>
<tr>
  <td style="font-weight:600"><?= htmlspecialchars($s['fullname']) ?></td>
  <td><?= htmlspecialchars($s['email']) ?></td>
  <td><?= htmlspecialchars($s['phonenumber'] ?: 'N/A') ?></td>

  <td><?= htmlspecialchars($s['vehicle_no'] ?: 'Unassigned') ?></td>
  <td><?= htmlspecialchars($s['driver_name'] ?: 'Unassigned') ?></td>

  <td><?= $s['assigned_at'] ? date('d M Y, H:i', strtotime($s['assigned_at'])) : '—' ?></td>

  <td class="action-links">
    <a href="edit_student.php?id=<?= $s['student_id'] ?>"><i class="fa-solid fa-pen-to-square"></i></a>

    <a href="javascript:void(0)" onclick="openAssignModal(
        <?= $s['student_id'] ?>,
        '<?= htmlspecialchars(addslashes($s['fullname'])) ?>'
    )">
      <i class="fa-solid fa-car"></i>
    </a>

    <a href="?delete_id=<?= $s['student_id'] ?>" class="del"
       onclick="return confirm('Delete this student?')">
       <i class="fa-solid fa-trash"></i>
    </a>
  </td>
</tr>
<?php endforeach; endif; ?>

</tbody>
</table>
</div>

</main>

<!-- ASSIGN MODAL -->
<div id="assignModal" class="modal">
  <div class="modal-box">
    <h3 style="margin-top:0">Assign Student</h3>
    <p style="color:var(--text-muted)">Assign vehicle & instructor</p>

    <form method="POST">
      <input type="hidden" name="student_id" id="modalStudentId">

      <label style="font-size:0.8rem; color:#94a3b8;">Vehicle</label>
      <select name="vehicle_assigned_id" required>
        <option value="">Select Vehicle</option>
        <?php foreach ($vehicles as $v): ?>
        <option value="<?= $v['vehicle_id'] ?>">
          <?= htmlspecialchars($v['vehicle_no']) ?> — <?= htmlspecialchars($v['vehicle_model']) ?>
        </option>
        <?php endforeach; ?>
      </select>

      <label style="font-size:0.8rem; color:#94a3b8;">Driver</label>
      <select name="driver_assigned_id" required>
        <option value="">Select Driver</option>
        <?php foreach ($drivers as $d): ?>
        <option value="<?= $d['driver_id'] ?>">
          <?= htmlspecialchars($d['fullname']) ?>
        </option>
        <?php endforeach; ?>
      </select>

      <div style="display:flex; gap:1rem; justify-content:flex-end; margin-top:1.5rem;">
        <button type="button" onclick="closeAssignModal()" class="btn"
                style="background:transparent; border:1px solid rgba(255,255,255,0.15);">
          Cancel
        </button>

        <button type="submit" name="assign_student" class="btn">
          Assign
        </button>
      </div>
    </form>
  </div>
</div>

<script>
const modal = document.getElementById('assignModal');
const modalStudentId = document.getElementById('modalStudentId');

function openAssignModal(studentId, name) {
  modalStudentId.value = studentId;
  modal.style.display = 'flex';
}

function closeAssignModal() {
  modal.style.display = 'none';
}

modal.addEventListener('click', function(e) {
  if (e.target === modal) closeAssignModal();
});
</script>

</body>
</html>
