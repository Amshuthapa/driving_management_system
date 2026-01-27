<?php
session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: ../index.php");
    exit();
}

require_once '../config/db.php';
require_once '../controllers/StudentController.php';

$pdo = (new \Config\Database())->connect();
$studentCtrl = new StudentController($pdo);

// ✅ Delete first (redirect)
$studentCtrl->handleDelete();

// ✅ List
$students = $studentCtrl->listStudents();

function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function fmtDT($dt){
    if (!$dt) return '—';
    $ts = strtotime($dt);
    if (!$ts) return '—';
    return date('d M Y, H:i', $ts);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Student Directory | Driving System</title>
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
      display:flex;
      min-height:100vh;
      background-image: radial-gradient(circle at top right, #1e1b4b, #0f172a);
    }

    main{
      flex:1;
      padding:2.5rem;
      margin-left:260px; /* sidebar width */
    }

    @media (max-width:900px){
      main{ margin-left:0; padding:1.25rem; }
    }

    .header-actions{
      display:flex;
      justify-content:space-between;
      align-items:flex-end;
      gap: 1rem;
      margin-bottom: 1.25rem;
    }

    .page-title{
      margin:0;
      font-weight:800;
      letter-spacing:-.4px;
      font-size:1.6rem;
    }

    .page-sub{
      margin:.35rem 0 0;
      color:var(--text-muted);
      font-size:.95rem;
    }

    .btn-add{
      background: linear-gradient(135deg, var(--primary), #1e40af);
      color:white;
      text-decoration:none;
      padding:0.85rem 1.1rem;
      border-radius:0.9rem;
      font-weight:800;
      display:inline-flex;
      align-items:center;
      gap:.55rem;
      transition: transform .15s ease, opacity .15s ease;
      box-shadow: 0 16px 28px -18px rgba(37,99,235,0.7);
      white-space: nowrap;
    }
    .btn-add:hover{ opacity:.95; transform: translateY(-1px); }

    .glass-card{
      background: linear-gradient(135deg, var(--glass), rgba(255,255,255,0.03));
      backdrop-filter: blur(14px);
      -webkit-backdrop-filter: blur(14px);
      border:1px solid var(--border);
      border-radius:1.25rem;
      padding:1.5rem;
      box-shadow: 0 18px 45px -30px rgba(0,0,0,0.7);
    }

    .alert{
      padding: 1rem 1.1rem;
      border-radius: .9rem;
      border: 1px solid transparent;
      background: rgba(255,255,255,0.04);
      margin: 0 0 1.25rem;
      display:flex;
      align-items:center;
      gap:.65rem;
      font-size:.95rem;
    }
    .alert-success{ color:var(--success); border-color: rgba(74,222,128,0.25); }
    .alert-error{ color:var(--error); border-color: rgba(248,113,113,0.25); }

    /* Table */
    .table-container{ overflow-x:auto; }
    table{ width:100%; border-collapse:collapse; }

    th{
      text-align:left;
      color:var(--text-muted);
      padding:1rem;
      border-bottom:1px solid var(--border);
      font-size:.82rem;
      text-transform:uppercase;
      letter-spacing:.06em;
      font-weight:800;
      white-space: nowrap;
    }

    td{
      padding:1rem;
      border-bottom:1px solid rgba(255,255,255,0.06);
      vertical-align:middle;
      white-space: nowrap;
    }

    tr:hover td{ background: rgba(255,255,255,0.03); }

    .id-muted{ color:var(--text-muted); }
    .name-strong{ font-weight:700; }

    .pill{
      display:inline-flex;
      align-items:center;
      gap:.35rem;
      padding:.28rem .65rem;
      border-radius:999px;
      font-size:.78rem;
      font-weight:800;
      border: 1px solid rgba(255,255,255,0.12);
      background: rgba(255,255,255,0.06);
      color:#e2e8f0;
    }
    .pill-unassigned{
      border-color: rgba(148,163,184,0.25);
      background: rgba(148,163,184,0.10);
      color: #cbd5e1;
    }

    .plate-badge{
      background: rgba(37,99,235,0.12);
      color:#bfdbfe;
      padding: 5px 12px;
      border-radius: 999px;
      border: 1px solid rgba(37,99,235,0.25);
      font-weight:800;
      font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
      display:inline-block;
    }

    .actions{
      display:flex;
      gap:.5rem;
      flex-wrap:wrap;
    }

    .btn-icon{
      display:inline-flex;
      align-items:center;
      justify-content:center;
      width:40px;
      height:40px;
      border-radius:.9rem;
      text-decoration:none;
      border:1px solid rgba(255,255,255,0.12);
      background: rgba(255,255,255,0.06);
      color:#e2e8f0;
      transition: transform .15s ease, opacity .15s ease, border-color .15s ease;
    }
    .btn-icon:hover{ opacity:.95; transform: translateY(-1px); border-color: rgba(37,99,235,0.35); }
    .btn-icon.del:hover{ border-color: rgba(248,113,113,0.35); color:#fecaca; }

    .empty-row{
      text-align:center;
      padding:2.5rem;
      color:var(--text-muted);
      white-space: normal;
    }

    /* Delete modal */
    #deleteModal{
      display:none;
      position:fixed;
      inset:0;
      background:rgba(0,0,0,0.6);
      backdrop-filter: blur(6px);
      -webkit-backdrop-filter: blur(6px);
      justify-content:center;
      align-items:center;
      z-index:9999;
      padding: 1rem;
    }
    #deleteModal.show{ display:flex; }

    .modal-card{
      width:100%;
      max-width:460px;
      background: linear-gradient(135deg, rgba(255,255,255,0.08), rgba(255,255,255,0.03));
      border:1px solid rgba(255,255,255,0.14);
      border-radius:1.25rem;
      padding:1.5rem;
      box-shadow: 0 30px 70px -40px rgba(0,0,0,0.85);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
    }
    .modal-title{
      margin:0;
      font-weight:900;
      letter-spacing:-.3px;
      color:#fecaca;
    }
    .modal-sub{
      margin:.6rem 0 1.2rem;
      color:var(--text-muted);
    }
    .modal-actions{
      display:flex;
      gap:.75rem;
      justify-content:flex-end;
      flex-wrap:wrap;
    }

    .btn-ghost{
      background: rgba(255,255,255,0.06);
      border:1px solid rgba(255,255,255,0.14);
      color:#fff;
      padding:.65rem 1rem;
      border-radius:.9rem;
      cursor:pointer;
      font-weight:800;
    }
    .btn-danger{
      background: rgba(248,113,113,0.14);
      border:1px solid rgba(248,113,113,0.28);
      color:#fecaca;
      padding:.65rem 1rem;
      border-radius:.9rem;
      text-decoration:none;
      font-weight:900;
      display:inline-flex;
      align-items:center;
      gap:.5rem;
    }
  </style>
</head>

<body>
  <?php include('../includes/navbar.php'); ?>

  <main>
    <div class="header-actions">
      <div>
        <h2 class="page-title">Student Directory</h2>
        <p class="page-sub">Manage and view all registered students.</p>
      </div>

      <a href="add_student.php" class="btn-add">
        <i class="fa-solid fa-plus"></i>
        Register New Student
      </a>
    </div>

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
      <div class="alert alert-success">
        <i class="fa-solid fa-circle-check"></i>
        Student record has been removed successfully.
      </div>
    <?php endif; ?>

    <div class="glass-card">
      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>Full Name</th>
              <th>Email</th>
              <th style="width:160px;">Phone</th>
              <th style="width:260px;">Vehicle</th>
              <th style="width:200px;">Driver</th>
              <th style="width:180px;">Assigned At</th>
              <th style="width:150px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($students)): ?>
              <tr><td colspan="8" class="empty-row">No students found.</td></tr>
            <?php else: ?>
              <?php foreach ($students as $s): ?>
                <?php
                  $vehicleText = $s['vehicle_no']
                    ? $s['vehicle_no'].' — '.$s['vehicle_model']
                    : '';
                  $driverText = $s['driver_name'] ?? '';
                ?>
                <tr>
                  <td class="name-strong"><?= h($s['fullname']) ?></td>
                  <td><?= h($s['email']) ?></td>
                  <td><?= h(($s['phonenumber'] ?? '') !== '' ? $s['phonenumber'] : 'N/A') ?></td>

                  <td>
                    <?php if (!empty($vehicleText)): ?>
                      <span class="plate-badge"><?= h($vehicleText) ?></span>
                    <?php else: ?>
                      <span class="pill pill-unassigned"><i class="fa-solid fa-link-slash"></i> Unassigned</span>
                    <?php endif; ?>
                  </td>

                  <td>
                    <?php if (!empty($driverText)): ?>
                      <span class="pill"><i class="fa-solid fa-user-tie"></i> <?= h($driverText) ?></span>
                    <?php else: ?>
                      <span class="pill pill-unassigned"><i class="fa-solid fa-link-slash"></i> Unassigned</span>
                    <?php endif; ?>
                  </td>

                  <td><?= fmtDT($s['assigned_at'] ?? null) ?></td>

                  <td>
                    <div class="actions">
                      <a class="btn-icon" href="edit_student.php?id=<?= (int)$s['student_id'] ?>" title="Edit">
                        <i class="fa-solid fa-pen-to-square"></i>
                      </a>

                      <button
                        type="button"
                        class="btn-icon del"
                        title="Delete"
                        onclick="openDeleteModal(<?= (int)$s['student_id'] ?>, '<?= h($s['fullname']) ?>')">
                        <i class="fa-solid fa-trash"></i>
                      </button>
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

  <!-- ✅ Delete Modal -->
  <div id="deleteModal">
    <div class="modal-card">
      <h3 class="modal-title">Delete Student</h3>
      <p class="modal-sub">
        Are you sure you want to delete <strong id="studentName"></strong>?
        <br>This action cannot be undone.
      </p>

      <div class="modal-actions">
        <button class="btn-ghost" type="button" onclick="closeDeleteModal()">Cancel</button>
        <a id="confirmDeleteBtn" class="btn-danger" href="#">
          <i class="fa-solid fa-trash"></i> Delete
        </a>
      </div>
    </div>
  </div>

  <script>
    const deleteModal = document.getElementById('deleteModal');
    const studentName = document.getElementById('studentName');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

    function openDeleteModal(id, name){
      studentName.textContent = name;
      confirmDeleteBtn.href = "?delete_id=" + id;
      deleteModal.classList.add('show');
    }

    function closeDeleteModal(){
      deleteModal.classList.remove('show');
    }

    deleteModal.addEventListener('click', (e) => {
      if (e.target === deleteModal) closeDeleteModal();
    });

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') closeDeleteModal();
    });
  </script>
</body>
</html>
