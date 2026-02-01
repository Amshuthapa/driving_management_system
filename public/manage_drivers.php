<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: ../index.php");
    exit();
}

require_once '../config/db.php';
require_once '../controllers/DriverController.php';

$pdo = (new \Config\Database())->connect();
$driverCtrl = new DriverController($pdo);

// Delete
$driverCtrl->handleDelete();

// List
$drivers = $driverCtrl->listDrivers();

function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Driver Directory | Driving System</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
:root{
  --primary:#2563eb;
  --glass:rgba(255,255,255,0.06);
  --border:rgba(255,255,255,0.12);
  --text-muted:#94a3b8;
  --success:#4ade80;
  --error:#f87171;
}

*{ box-sizing:border-box; }

body{
  margin:0;
  font-family:'Inter',sans-serif;
  background:#0f172a;
  color:white;
  min-height:100vh;
  display:flex;
  background-image:radial-gradient(circle at top right,#1e1b4b,#0f172a);
}

main{
  flex:1;
  padding:2.5rem;
  margin-left:260px;
}

@media(max-width:900px){
  main{ margin-left:0; padding:1.25rem; }
}

.header-actions{
  display:flex;
  justify-content:space-between;
  align-items:flex-end;
  gap:1rem;
  margin-bottom:1.25rem;
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
  background:linear-gradient(135deg,var(--primary),#1e40af);
  color:white;
  text-decoration:none;
  padding:.85rem 1.1rem;
  border-radius:.9rem;
  font-weight:800;
  display:inline-flex;
  align-items:center;
  gap:.55rem;
  box-shadow:0 16px 28px -18px rgba(37,99,235,.7);
}

.btn-add:hover{ opacity:.95; transform:translateY(-1px); }

.glass-card{
  background:linear-gradient(135deg,var(--glass),rgba(255,255,255,.03));
  backdrop-filter:blur(14px);
  border:1px solid var(--border);
  border-radius:1.25rem;
  padding:1.5rem;
  box-shadow:0 18px 45px -30px rgba(0,0,0,.7);
  margin-bottom:1rem;
}

/* SEARCH */
.search-row{
  display:flex;
  gap:.75rem;
  flex-wrap:wrap;
  align-items:center;
}

.search-row input{
  flex:1;
  min-width:240px;
  padding:.85rem .9rem;
  background:rgba(0,0,0,.22);
  border:1px solid rgba(255,255,255,.14);
  border-radius:.9rem;
  color:white;
}

.search-row input:focus{
  outline:none;
  border-color:rgba(37,99,235,.7);
  box-shadow:0 0 0 3px rgba(37,99,235,.18);
}

.match-count{
  color:var(--text-muted);
  font-weight:700;
}

/* TABLE */
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
}

td{
  padding:1rem;
  border-bottom:1px solid rgba(255,255,255,.06);
}

tr:hover td{ background:rgba(255,255,255,.03); }

.id-muted{ color:var(--text-muted); }
.name-strong{ font-weight:700; }

.badge-role{
  display:inline-flex;
  align-items:center;
  gap:.35rem;
  padding:.28rem .65rem;
  border-radius:999px;
  font-size:.78rem;
  font-weight:800;
  border:1px solid rgba(37,99,235,.25);
  background:rgba(37,99,235,.12);
  color:#bfdbfe;
}

.action-links{
  display:flex;
  gap:.5rem;
}

.btn-icon{
  width:40px;
  height:40px;
  display:inline-flex;
  align-items:center;
  justify-content:center;
  border-radius:.9rem;
  border:1px solid rgba(255,255,255,.12);
  background:rgba(255,255,255,.06);
  color:#e2e8f0;
  text-decoration:none;
}

.btn-icon:hover{ border-color:rgba(37,99,235,.35); }
.btn-icon.del:hover{ border-color:rgba(248,113,113,.35); color:#fecaca; }

.empty-row{
  text-align:center;
  padding:2.5rem;
  color:var(--text-muted);
}
</style>
</head>

<body>
<?php include('../includes/navbar.php'); ?>

<main>

<div class="header-actions">
  <div>
    <h2 class="page-title">Driver Directory</h2>
    <p class="page-sub">Manage and view all registered drivers.</p>
  </div>
  <a href="add_driver.php" class="btn-add">
    <i class="fa-solid fa-plus"></i> Register New Driver
  </a>
</div>

<!-- SEARCH -->
<div class="glass-card">
  <div class="search-row">
    <input id="driverSearch" type="text" placeholder="Search by name, email, or phone…">
    <div id="matchCount" class="match-count"></div>
  </div>
</div>

<!-- TABLE -->
<div class="glass-card">
  <div class="table-container">
    <table id="driversTable">
      <thead>
        <tr>
          <th>ID</th>
          <th>Full Name</th>
          <th>Email</th>
          <th>Phone</th>
          <th>Role</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="driversTbody">
      <?php if(empty($drivers)): ?>
        <tr><td colspan="6" class="empty-row">No drivers found.</td></tr>
      <?php else: foreach($drivers as $d): ?>
        <tr class="driver-row"
            data-search="<?= h(strtolower($d['fullname'].' '.$d['email'].' '.($d['phonenumber'] ?? ''))) ?>">
          <td class="id-muted">#<?= (int)$d['driver_id'] ?></td>
          <td class="name-strong"><?= h($d['fullname']) ?></td>
          <td><?= h($d['email']) ?></td>
          <td><?= h($d['phonenumber'] ?: 'N/A') ?></td>
          <td>
            <span class="badge-role"><i class="fa-solid fa-user-tie"></i> Driver</span>
          </td>
          <td>
            <div class="action-links">
              <a class="btn-icon" href="edit_driver.php?id=<?= (int)$d['driver_id'] ?>">
                <i class="fa-solid fa-pen-to-square"></i>
              </a>
              <a class="btn-icon del" href="?delete_id=<?= (int)$d['driver_id'] ?>"
                 onclick="return confirm('Delete this driver?')">
                <i class="fa-solid fa-trash"></i>
              </a>
            </div>
          </td>
        </tr>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

</main>

<script>
const input = document.getElementById('driverSearch');
const rows = document.querySelectorAll('.driver-row');
const matchCount = document.getElementById('matchCount');

function filterDrivers(){
  const q = input.value.toLowerCase().trim();
  let visible = 0;

  rows.forEach(r => {
    const text = r.dataset.search || '';
    const show = text.includes(q);
    r.style.display = show ? '' : 'none';
    if(show) visible++;
  });

  matchCount.textContent = rows.length ? `${visible} of ${rows.length}` : '';
}

input.addEventListener('input', filterDrivers);
filterDrivers();
</script>

</body>
</html>
