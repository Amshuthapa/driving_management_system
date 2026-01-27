<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<style>
  :root {
    --primary: #2563eb; /* blue */
    --glass-sidebar: rgba(0, 0, 0, 0.2);
    --glass-hover: rgba(255, 255, 255, 0.08);
    --border: rgba(255, 255, 255, 0.12);
    --text-muted: #cbd5e1;
  }

  nav.sidebar {
    width: 260px;
    background: var(--glass-sidebar);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border-right: 1px solid var(--border);
    display: flex;
    flex-direction: column;
    padding: 2rem 1.5rem;
    position: fixed;
    height: 100vh;
    left: 0;
    top: 0;
    z-index: 2000;
    overflow-y: auto; /* ✅ ensures logout visible */
  }

  .nav-brand {
    font-size: 1.15rem;
    font-weight: 700;
    margin-bottom: 2rem;
    letter-spacing: -0.5px;
    display: flex;
    align-items: center;
    gap: 10px;
    color: white;
  }

  /* ✅ renamed to avoid Bootstrap conflict */
  .side-link {
    color: var(--text-muted);
    text-decoration: none;
    padding: 0.85rem 1rem;
    border-radius: 0.9rem;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 12px;
    transition: all 0.25s ease;
    font-weight: 500;
  }

  .side-link:hover {
    background: var(--glass-hover);
    color: white;
  }

  .side-link.active {
    background: var(--primary);
    color: white;
    box-shadow: 0 10px 25px rgba(37, 99, 235, 0.35);
  }

  .logout-link {
    margin-top: auto;
    color: #f87171;
    text-decoration: none;
    padding: 0.85rem 1rem;
    display: flex;
    align-items: center;
    gap: 12px;
    font-weight: 700;
    border-radius: 0.9rem;
    transition: 0.25s ease;
  }

  .logout-link:hover {
    background: rgba(248, 113, 113, 0.12);
  }
</style>

<nav class="sidebar">
  <div class="nav-brand">
    <i class="fa-solid fa-steering-wheel" style="color: var(--primary);"></i>
    <span>Driving System</span>
  </div>

  <a href="dashboard_admin.php" class="side-link <?= $current_page == 'dashboard_admin.php' ? 'active' : '' ?>">
    <i class="fa-solid fa-gauge"></i> Dashboard
  </a>

  <a href="manage_drivers.php" class="side-link <?= $current_page == 'manage_drivers.php' ? 'active' : '' ?>">
    <i class="fa-solid fa-user-tie"></i> Drivers
  </a>

  <a href="manage_students.php" class="side-link <?= $current_page == 'manage_students.php' ? 'active' : '' ?>">
    <i class="fa-solid fa-graduation-cap"></i> Students
  </a>

  <a href="manage_vehicles.php" class="side-link <?= $current_page == 'manage_vehicles.php' ? 'active' : '' ?>">
    <i class="fa-solid fa-car"></i> Vehicles
  </a>
  <a href="admin_booking.php" class="side-link <?= $current_page == 'admin_booking.php' ? 'active' : '' ?>">
    <i class="fa-solid fa-calendar-check"></i> Book Student
  </a>

  <a href="assign_student.php" class="side-link <?= $current_page == 'assign_student.php' ? 'active' : '' ?>">
    <i class="fa-solid fa-user-check"></i> Assign Student
  </a>

  <a href="logout.php" class="side-link">
    <i class="fa-solid fa-right-from-bracket"></i> Sign Out
  </a>
</nav>
