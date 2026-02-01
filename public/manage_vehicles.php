<?php
session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: ../index.php");
    exit();
}

require_once '../config/db.php';
require_once '../controllers/VehicleController.php';

$pdo = (new \Config\Database())->connect();
$vehicleCtrl = new VehicleController($pdo);

// ✅ Handle delete first (redirects)
$vehicleCtrl->handleDeleteVehicle();

// ✅ Handle add/update
$responseAdd = $vehicleCtrl->handleAddVehicle();
$responseUpd = $vehicleCtrl->handleUpdateVehicle();

// Choose which message to show
$response = !empty($responseUpd['message']) ? $responseUpd : $responseAdd;

// Fetch list
$vehicles = $vehicleCtrl->listVehicles();

function h($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Vehicle Management | Driving System</title>

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
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
      color: white;
      min-height: 100vh;
      background: radial-gradient(circle at top right, #1e1b4b, #0f172a);
      display: flex;
    }

    main {
      flex: 1;
      padding: 2.5rem;
      margin-left: 300px;
    }

    .glass-card {
      background: linear-gradient(135deg, var(--glass), rgba(255,255,255,0.03));
      backdrop-filter: blur(14px);
      border: 1px solid var(--border);
      border-radius: 1.25rem;
      padding: 1.5rem;
      box-shadow: 0 18px 45px -30px rgba(0,0,0,0.7);
      margin-bottom: 1.25rem;
    }

    .page-title{ margin:0 0 .25rem; font-weight:800; letter-spacing:-.4px; }
    .page-sub{ margin:0 0 1rem; color:var(--text-muted); }

    .alert {
      padding: 1rem 1.1rem;
      border-radius: 0.9rem;
      margin-bottom: 1.25rem;
      font-size: 0.95rem;
      border: 1px solid transparent;
      display:flex;
      align-items:center;
      gap:.65rem;
      background: rgba(255,255,255,0.04);
    }
    .alert-success { color: var(--success); border-color: rgba(74, 222, 128, 0.25); }
    .alert-error { color: var(--error); border-color: rgba(248, 113, 113, 0.25); }

    .grid-form {
      display: grid;
      grid-template-columns: 1.2fr 1.2fr auto;
      gap: 1rem;
      align-items: end;
    }
    @media (max-width: 900px){
      main{ margin-left: 0; padding: 1.25rem; }
      .grid-form{ grid-template-columns: 1fr; }
    }

    label {
      font-size: 0.78rem;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 0.06em;
      display:block;
      margin-bottom: .5rem;
    }

    input {
      width: 100%;
      padding: 0.85rem 0.95rem;
      background: rgba(0, 0, 0, 0.22);
      border: 1px solid var(--border);
      border-radius: 0.85rem;
      color: white;
      outline: none;
    }

    input:focus{
      border-color: rgba(37,99,235,0.7);
      box-shadow: 0 0 0 3px rgba(37,99,235,0.18);
    }

    /* ✅ SEARCH */
    .search-row{
      display:flex;
      gap:.75rem;
      flex-wrap:wrap;
      align-items:center;
      justify-content:space-between;
    }
    .search-row input{
      flex:1;
      min-width:240px;
    }
    .match-count{
      color:var(--text-muted);
      font-weight:700;
      white-space:nowrap;
    }

    .btn {
      border: none;
      padding: 0.85rem 1.1rem;
      border-radius: 0.9rem;
      font-weight: 800;
      cursor: pointer;
      transition: transform .15s ease, opacity .15s ease;
      white-space: nowrap;
      display:inline-flex;
      align-items:center;
      gap:.5rem;
      text-decoration:none;
    }
    .btn:hover{ opacity:.95; transform: translateY(-1px); }

    .btn-primary{
      background: linear-gradient(135deg, var(--primary), #1e40af);
      color:#fff;
      box-shadow: 0 16px 28px -18px rgba(37,99,235,0.7);
    }
    .btn-ghost{
      background: rgba(255,255,255,0.06);
      border: 1px solid rgba(255,255,255,0.12);
      color:#fff;
    }
    .btn-danger{
      background: rgba(248,113,113,0.14);
      border: 1px solid rgba(248,113,113,0.28);
      color:#fecaca;
    }

    table { width:100%; border-collapse: collapse; }
    th {
      text-align:left;
      color: var(--text-muted);
      padding: 1rem;
      border-bottom: 1px solid var(--border);
      font-size: .82rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing:.06em;
      white-space:nowrap;
    }
    td { padding: 1rem; border-bottom: 1px solid rgba(255,255,255,0.06); vertical-align:middle; }
    tr:hover td{ background: rgba(255,255,255,0.03); }

    .plate-badge{
      background: rgba(37, 99, 235, 0.12);
      color: #bfdbfe;
      padding: 5px 12px;
      border-radius: 999px;
      border: 1px solid rgba(37, 99, 235, 0.25);
      font-weight:800;
      font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
      display:inline-block;
    }

    .actions{ display:flex; gap:.5rem; flex-wrap:wrap; }

    /* Edit modal */
    .modal-backdrop{
      position:fixed; inset:0;
      background: rgba(0,0,0,0.55);
      display:none;
      align-items:center;
      justify-content:center;
      padding: 1rem;
      z-index: 5000;
    }
    .modal-backdrop.show{ display:flex; }
    .modal-card{
      width: 100%;
      max-width: 520px;
      background: linear-gradient(135deg, rgba(255,255,255,0.08), rgba(255,255,255,0.03));
      border: 1px solid rgba(255,255,255,0.14);
      border-radius: 1.25rem;
      backdrop-filter: blur(16px);
      padding: 1.25rem;
      box-shadow: 0 30px 70px -40px rgba(0,0,0,0.85);
    }
    .modal-head{ display:flex; align-items:center; justify-content:space-between; gap:1rem; margin-bottom: .75rem;}
    .modal-title{ margin:0; font-weight:900; letter-spacing:-.3px; }
    .icon-btn{
      background: rgba(255,255,255,0.06);
      border: 1px solid rgba(255,255,255,0.14);
      color:#fff;
      border-radius: .9rem;
      padding:.55rem .75rem;
      cursor:pointer;
    }
    .modal-grid{ display:grid; grid-template-columns:1fr; gap:.9rem; margin-top: .75rem;}
  </style>
</head>

<body>
  <?php include('../includes/navbar.php'); ?>

  <main>
    <h2 class="page-title">Vehicle Management</h2>
    <p class="page-sub">Add, edit and delete vehicles (Vehicle No must be unique).</p>

    <?php if (!empty($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
      <div class="alert alert-success">
        <i class="fa-solid fa-circle-check"></i> Vehicle deleted successfully!
      </div>
    <?php endif; ?>

    <?php if (!empty($response['message'])): ?>
      <div class="alert <?= $response['success'] ? 'alert-success' : 'alert-error' ?>">
        <i class="fa-solid <?= $response['success'] ? 'fa-circle-check' : 'fa-triangle-exclamation' ?>"></i>
        <?= h($response['message']) ?>
      </div>
    <?php endif; ?>

    <!-- Add Vehicle -->
    <div class="glass-card">
      <form method="POST" class="grid-form">
        <div>
          <label>Vehicle Model</label>
          <input type="text" name="vehicle_model" placeholder="e.g., Suzuki Alto" required>
        </div>

        <div>
          <label>Vehicle Number</label>
          <input type="text" name="vehicle_no" placeholder="e.g., BA 2 CHA 1234" required>
        </div>

        <button type="submit" name="add_vehicle" class="btn btn-primary">
          <i class="fa-solid fa-plus"></i> Add Vehicle
        </button>
      </form>
    </div>

    <!-- ✅ SEARCH -->
    <div class="glass-card">
      <div class="search-row">
        <div style="width:100%;">
          <label style="margin:0 0 .5rem;">Search Vehicles</label>
          <input id="vehicleSearch" type="text" placeholder="Search by ID, model, or vehicle number…">
        </div>
        <div id="matchCount" class="match-count"></div>
      </div>
    </div>

    <!-- Vehicle List -->
    <div class="glass-card">
      <div style="overflow-x:auto;">
        <table id="vehiclesTable">
          <thead>
            <tr>
              <th style="width:90px;">ID</th>
              <th>Vehicle Model</th>
              <th style="width:220px;">Vehicle No</th>
              <th style="width:210px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($vehicles)): ?>
              <tr>
                <td colspan="4" style="text-align:center; color:var(--text-muted); padding:2.5rem;">
                  No vehicles found.
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($vehicles as $v): ?>
                <?php
                  $blob = strtolower(
                    '#'.$v['vehicle_id'].' '.
                    ($v['vehicle_model'] ?? '').' '.
                    ($v['vehicle_no'] ?? '')
                  );
                ?>
                <tr class="vehicle-row" data-search="<?= h($blob) ?>">
                  <td style="color:var(--text-muted);">#<?= (int)$v['vehicle_id'] ?></td>
                  <td><?= h($v['vehicle_model']) ?></td>
                  <td><span class="plate-badge"><?= h($v['vehicle_no']) ?></span></td>
                  <td>
                    <div class="actions">
                      <button
                        type="button"
                        class="btn btn-ghost"
                        onclick="openEditModal('<?= (int)$v['vehicle_id'] ?>','<?= h($v['vehicle_model']) ?>','<?= h($v['vehicle_no']) ?>')">
                        <i class="fa-solid fa-pen"></i> Edit
                      </button>

                      <a
                        class="btn btn-danger"
                        href="manage_vehicles.php?delete_id=<?= (int)$v['vehicle_id'] ?>"
                        onclick="return confirm('Delete this vehicle?');">
                        <i class="fa-solid fa-trash"></i> Delete
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

  <!-- ✅ Edit Modal (custom) -->
  <div class="modal-backdrop" id="editModal">
    <div class="modal-card">
      <div class="modal-head">
        <h3 class="modal-title">Edit Vehicle</h3>
        <button class="icon-btn" type="button" onclick="closeEditModal()">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>

      <form method="POST">
        <input type="hidden" name="vehicle_id" id="edit_vehicle_id">

        <div class="modal-grid">
          <div>
            <label>Vehicle Model</label>
            <input type="text" name="vehicle_model" id="edit_vehicle_model" required>
          </div>

          <div>
            <label>Vehicle No</label>
            <input type="text" name="vehicle_no" id="edit_vehicle_no" required>
          </div>

          <button type="submit" name="update_vehicle" class="btn btn-primary" style="width:100%; justify-content:center;">
            <i class="fa-solid fa-check"></i> Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
    const editModal = document.getElementById('editModal');

    function openEditModal(id, model, no){
      document.getElementById('edit_vehicle_id').value = id;
      document.getElementById('edit_vehicle_model').value = model;
      document.getElementById('edit_vehicle_no').value = no;
      editModal.classList.add('show');
    }

    function closeEditModal(){
      editModal.classList.remove('show');
    }

    // close on backdrop click
    editModal.addEventListener('click', (e) => {
      if (e.target === editModal) closeEditModal();
    });

    // close on ESC
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') closeEditModal();
    });

    // ✅ Search
    const input = document.getElementById('vehicleSearch');
    const rows = document.querySelectorAll('.vehicle-row');
    const matchCount = document.getElementById('matchCount');

    function filterVehicles(){
      const q = (input.value || '').toLowerCase().trim();
      let visible = 0;

      rows.forEach(r => {
        const text = r.dataset.search || '';
        const show = text.includes(q);
        r.style.display = show ? '' : 'none';
        if (show) visible++;
      });

      matchCount.textContent = rows.length ? `${visible} of ${rows.length}` : '';
    }

    input.addEventListener('input', filterVehicles);
    filterVehicles();
  </script>
</body>
</html>
