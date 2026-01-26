<?php
// Get the current page name to set the active class
$current_page = basename($_SERVER['PHP_SELF']);
?>
<style>
    :root {
        --primary: #6366f1;
        --glass-sidebar: rgba(0, 0, 0, 0.2);
        --glass-hover: rgba(255, 255, 255, 0.08);
        --border: rgba(255, 255, 255, 0.1);
        --text-muted: #94a3b8;
    }

    nav.sidebar {
        width: 260px;
        background: var(--glass-sidebar);
        backdrop-filter: blur(15px);
        border-right: 1px solid var(--border);
        display: flex;
        flex-direction: column;
        padding: 2rem 1.5rem;
        position: fixed;
        height: 100vh;
        left: 0;
        top: 0;
        z-index: 1000;
    }

    .nav-brand {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 3rem;
        letter-spacing: -0.5px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .nav-link {
        color: var(--text-muted);
        text-decoration: none;
        padding: 0.8rem 1rem;
        border-radius: 0.75rem;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 12px;
        transition: all 0.3s ease;
    }

    .nav-link:hover {
        background: var(--glass-hover);
        color: white;
    }

    .nav-link.active {
        background: var(--primary);
        color: white;
        box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
    }

    .logout-link {
        margin-top: auto;
        color: #f87171;
        text-decoration: none;
        padding: 0.8rem 1rem;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 600;
        transition: 0.3s;
    }

    .logout-link:hover {
        background: rgba(248, 113, 113, 0.1);
        border-radius: 0.75rem;
    }
</style>

<nav class="sidebar">
    <div class="nav-brand">
        <i class="fa-solid fa-steering-wheel" style="color: var(--primary);"></i>
        <span>Driving System</span>
    </div>

    <a href="dashboard_admin.php" class="nav-link <?= $current_page == 'dashboard_admin.php' ? 'active' : '' ?>">
        <i class="fa-solid fa-gauge"></i> Dashboard
    </a>
    
    <a href="manage_drivers.php" class="nav-link <?= $current_page == 'manage_drivers.php' ? 'active' : '' ?>">
        <i class="fa-solid fa-user-tie"></i> Drivers
    </a>
    
    <a href="manage_students.php" class="nav-link <?= $current_page == 'manage_students.php' ? 'active' : '' ?>">
        <i class="fa-solid fa-graduation-cap"></i> Students
    </a>
    
    <a href="manage_vehicles.php" class="nav-link <?= $current_page == 'manage_vehicles.php' ? 'active' : '' ?>">
        <i class="fa-solid fa-car"></i> Vehicles
    </a>
    <a href="assign_student.php" class="nav-link <?= $current_page == 'assign_student.php' ? 'active' : '' ?>">
        <i class="fa-solid fa-car"></i> Assign Student
    </a>

    <a href="logout.php" class="logout-link">
        <i class="fa-solid fa-right-from-bracket"></i> Sign Out
    </a>
</nav>