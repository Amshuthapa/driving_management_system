<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>

<!-- inside navbar -->
<?php if (!isset($_SESSION['user_id'])): ?>

  <a class="btn btn-outline-light btn-sm" href="login.php">Login</a>

<?php else: ?>

  <div class="dropdown">
    <button class="btn btn-outline-light btn-sm dropdown-toggle" type="button"
            data-bs-toggle="dropdown" aria-expanded="false">
      <i class="fa-solid fa-user me-1"></i>
      <?= htmlspecialchars($_SESSION['fullname']) ?>
    </button>

    <ul class="dropdown-menu dropdown-menu-end">
      <li><h6 class="dropdown-header text-capitalize"><?= htmlspecialchars($_SESSION['role']) ?></h6></li>

      <?php if ($_SESSION['role'] === 'student'): ?>
        <li><a class="dropdown-item" href="dashboard_student.php">My Dashboard</a></li>
      <?php elseif ($_SESSION['role'] === 'driver'): ?>
        <li><a class="dropdown-item" href="dashboard_driver.php">Driver Dashboard</a></li>
      <?php else: ?>
        <li><a class="dropdown-item" href="dashboard_admin.php">Admin Dashboard</a></li>
      <?php endif; ?>

      <li><hr class="dropdown-divider"></li>
      <li>
        <a class="dropdown-item text-danger" href="logout.php" id="logoutBtn">
          <i class="fa-solid fa-right-from-bracket me-1"></i> Logout
        </a>
      </li>
    </ul>
  </div>

<?php endif; ?>
