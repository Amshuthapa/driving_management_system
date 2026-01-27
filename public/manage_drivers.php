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

// ✅ Delete (redirects with msg)
$driverCtrl->handleDelete();

// ✅ List
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
    :root {
      --primary: #2563eb;
      --glass: rgba(255, 255, 255, 0.06);
      --border: rgba(255, 255, 255, 0.12);
      --text-muted: #94a3b8;
      --success: #4ade80;
      --error: #f87171;
    }

    * { box-sizing: border-box; }

    body {
      margin: 0;
      font-family: 'Inter', sans-serif;
      background: #0f172a;
      color: white;
      min-height: 100vh;
      display: flex;
      background-image: radial-gradient(circle at top right, #1e1b4b, #0f172a);
    }

    main {
      flex: 1;
      padding: 2.5rem;
      margin-left: 260px; /* your sidebar width */
    }

    @media (max-width: 900px){
      main { margin-left: 0; padding: 1.25rem; }
    }

    .header-actions {
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      gap: 1rem;
      margin-bottom: 1.25rem;
    }

    .page-title {
      margin: 0;
      font-weight: 800;
      letter-spacing: -0.4px;
      font-size: 1.6rem;
    }

    .page-sub {
      margin: .35rem 0 0;
      color: var(--text-muted);
      font-size: .95rem;
    }

    .glass-card {
      background: linear-gradient(135deg, var(--glass), rgba(255,255,255,0.03));
      backdrop-filter: blur(14px);
      -webkit-backdrop-filter: blur(14px);
      border: 1px solid var(--border);
      border-radius: 1.25rem;
      padding: 1.5rem;
      box-shadow: 0 18px 45px -30px rgba(0,0,0,0.7);
    }

    .btn-add {
      background: linear-gradient(135deg, var(--primary), #1e40af);
      color: white;
      text-decoration: none;
      padding: 0.85rem 1.1rem;
      border-radius: 0.9rem;
      font-weight: 800;
      display: inline-flex;
      align-items: center;
      gap: .55rem;
      transition: transform .15s ease, opacity .15s ease;
      box-shadow: 0 16px 28px -18px rgba(37,99,235,0.7);
      white-space: nowrap;
    }

    .btn-add:hover { opacity: .95; transform: translateY(-1px); }

    .alert {
      padding: 1rem 1.1rem;
      border-radius: 0.9rem;
      margin: 0 0 1.25rem;
      font-size: 0.95rem;
      border: 1px solid transparent;
      display: flex;
      align-items: center;
      gap: .65rem;
      background: rgba(255,255,255,0.04);
    }

    .alert-success { color: var(--success); border-color: rgba(74, 222, 128, 0.25); }
    .alert-error { color: var(--error); border-color: rgba(248, 113, 113, 0.25); }

    /* Table */
    .table-container { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; }

    th {
      text-align: left;
      color: var(--text-muted);
      padding: 1rem;
      border-bottom: 1px solid var(--border);
      font-size: 0.82rem;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      font-weight: 800;
    }

    td {
      padding: 1rem;
      border-bottom: 1px solid rgba(255,255,255,0.06);
      vertical-align: middle;
    }

    tr:hover td { background: rgba(255,255,255,0.03); }

    .id-muted { color: var(--text-muted); }
    .name-strong { font-weight: 700; }

    .badge-role {
      display: inline-flex;
      align-items: center;
      gap: .35rem;
      padding: .28rem .65rem;
      border-radius: 999px;
      font-size: .78rem;
      font-weight: 800;
      border: 1px solid rgba(37,99,235,0.25);
      background: rgba(37,99,235,0.12);
      color: #bfdbfe;
    }

    .action-links {
      display: flex;
      gap: .5rem;
      flex-wrap: wrap;
    }

    .btn-icon {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 40px;
      height: 40px;
      border-radius: .9rem;
      text-decoration: none;
      border: 1px solid rgba(255,255,255,0.12);
      background: rgba(255,255,255,0.06);
      color: #e2e8f0;
      transition: transform .15s ease, opacity .15s ease, border-color .15s ease;
    }

    .btn-icon:hover { opacity: .95; transform: translateY(-1px); border-color: rgba(37,99,235,0.35); }
    .btn-icon.del:hover { border-color: rgba(248,113,113,0.35); color: #fecaca; }

    .empty-row {
      text-align: center;
      padding: 2.5rem;
      color: var(--text-muted);
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
        <i class="fa-solid fa-plus"></i>
        Register New Driver
      </a>
    </div>

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
      <div class="alert alert-success">
        <i class="fa-solid fa-circle-check"></i>
        Driver record has been removed successfully.
      </div>
    <?php endif; ?>

    <div class="glass-card">
      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th style="width:100px;">ID</th>
              <th>Full Name</th>
              <th>Email Address</th>
              <th style="width:160px;">Phone</th>
              <th style="width:140px;">Role</th>
              <th style="width:140px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($drivers)): ?>
              <tr><td colspan="6" class="empty-row">No drivers found.</td></tr>
            <?php else: ?>
              <?php foreach ($drivers as $d): ?>
                <tr>
                  <td class="id-muted">#<?= (int)$d['driver_id'] ?></td>
                  <td class="name-strong"><?= h($d['fullname']) ?></td>
                  <td><?= h($d['email']) ?></td>
                  <td><?= h(($d['phonenumber'] ?? '') !== '' ? $d['phonenumber'] : 'N/A') ?></td>
                  <td>
                    <span class="badge-role">
                      <i class="fa-solid fa-user-tie"></i> Driver
                    </span>
                  </td>
                  <td>
                    <div class="action-links">
                      <a class="btn-icon" href="edit_driver.php?id=<?= (int)$d['driver_id'] ?>" title="Edit">
                        <i class="fa-solid fa-pen-to-square"></i>
                      </a>
                      <a class="btn-icon del" href="?delete_id=<?= (int)$d['driver_id'] ?>"
                         onclick="return confirm('Delete this driver? This cannot be undone.')" title="Delete">
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
</body>
</html>
