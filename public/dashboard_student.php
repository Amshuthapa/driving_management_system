<?php
session_start();

// ✅ Student only
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'student') {
  header("Location: login.php");
  exit();
}

require_once '../config/db.php';
require_once '../controllers/StudentController.php';

$pdo = (new \Config\Database())->connect();
$studentCtrl = new StudentController($pdo);

// ✅ Handle booking request (POST)
$response = $studentCtrl->handleStudentBookingRequest();

// ✅ Get dashboard info
$me = $studentCtrl->getDashboardStudent();

function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

// UI badge mapping
$status = $me['booking_status'] ?? 'none';
$statusLabel = strtoupper($status ?: 'NONE');

$statusClass = 'badge-muted';
if ($status === 'requested') $statusClass = 'badge-primary';
if ($status === 'assigned')  $statusClass = 'badge-success';
if ($status === 'cancelled') $statusClass = 'badge-danger';

$slotText = '—';
if (!empty($me['requested_date']) && !empty($me['requested_time'])) {
  $slotText = $me['requested_date'] . ' ' . $me['requested_time'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Student Dashboard | Ayush Piyush Driving System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- ✅ Flatpickr (date+time picker) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

  <style>
    :root{
      --primary-blue:#2563eb;
      --primary-blue-dark:#1e40af;
      --glass-border: rgba(255,255,255,0.40);
      --glass-hi: rgba(255,255,255,0.75);
      --text-dark:#0f172a;
      --text-muted:#475569;
    }

    /* Enhanced background with animated gradient */
    body{
      background:
        radial-gradient(1200px 600px at 10% 10%, #c7d2fe, transparent 60%),
        radial-gradient(1000px 500px at 90% 20%, #e0e7ff, transparent 55%),
        radial-gradient(800px 400px at 50% 100%, #ddd6fe, transparent 50%),
        linear-gradient(135deg, #eef2ff, #f8fafc);
      min-height: 100vh;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
      position: relative;
      overflow-x: hidden;
    }

    /* Floating orbs animation */
    body::before {
      content: '';
      position: fixed;
      width: 600px;
      height: 600px;
      border-radius: 50%;
      background: radial-gradient(circle, rgba(147, 197, 253, 0.3), transparent 70%);
      top: -300px;
      right: -300px;
      animation: float 20s infinite ease-in-out;
      pointer-events: none;
      z-index: 0;
    }

    body::after {
      content: '';
      position: fixed;
      width: 400px;
      height: 400px;
      border-radius: 50%;
      background: radial-gradient(circle, rgba(199, 210, 254, 0.4), transparent 70%);
      bottom: -200px;
      left: -200px;
      animation: float 15s infinite ease-in-out reverse;
      pointer-events: none;
      z-index: 0;
    }

    @keyframes float {
      0%, 100% { transform: translate(0, 0) rotate(0deg); }
      33% { transform: translate(30px, -30px) rotate(5deg); }
      66% { transform: translate(-20px, 20px) rotate(-5deg); }
    }

    /* Enhanced glass card with shimmer effect */
    .glass-card{
      background: linear-gradient(135deg, rgba(255,255,255,0.85), rgba(255,255,255,0.55));
      backdrop-filter: blur(22px) saturate(160%);
      -webkit-backdrop-filter: blur(22px) saturate(160%);
      border: 1px solid var(--glass-border);
      border-radius: 24px;
      box-shadow: 0 30px 60px -20px rgba(0,0,0,0.18), 
                  inset 0 1px 0 var(--glass-hi),
                  0 0 0 1px rgba(255,255,255,0.5) inset;
      position: relative;
      transition: all 0.3s ease;
    }

    .glass-card:hover{
      transform: translateY(-2px);
      box-shadow: 0 40px 80px -25px rgba(0,0,0,0.25), 
                  inset 0 1px 0 var(--glass-hi),
                  0 0 0 1px rgba(255,255,255,0.5) inset;
    }

    /* Shimmer glow effect */
    .glass-card::before{
      content:"";
      position:absolute;
      inset:-1px;
      border-radius: inherit;
      background: linear-gradient(120deg, transparent, rgba(37,99,235,0.3), transparent);
      opacity: 0;
      transition: opacity 0.3s ease;
      pointer-events:none;
    }

    .glass-card:hover::before{
      opacity: 1;
    }

    .section-title{ 
      color: var(--text-dark);
      font-weight: 700;
      letter-spacing: -0.02em;
    }
    .section-subtitle{ 
      color: var(--text-muted);
      line-height: 1.6;
    }

    /* Enhanced buttons with gradient and shadows */
    .btn-primary{
      background: linear-gradient(135deg, var(--primary-blue), var(--primary-blue-dark));
      border: none;
      border-radius: 14px;
      font-weight: 600;
      transition: all 0.3s ease;
      color: white;
      box-shadow: 0 8px 20px -8px rgba(37,99,235,0.4);
      position: relative;
      overflow: hidden;
    }

    .btn-primary::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
      transition: left 0.5s ease;
    }

    .btn-primary:hover::before {
      left: 100%;
    }

    .btn-primary:hover{
      transform: translateY(-2px);
      box-shadow: 0 14px 28px rgba(37,99,235,0.35);
      background: linear-gradient(135deg, var(--primary-blue-dark), var(--primary-blue));
      color: white;
    }

    .btn-outline-primary{
      border-radius: 14px;
      font-weight: 600;
      border: 2px solid var(--primary-blue);
      color: var(--primary-blue);
      transition: all 0.3s ease;
      background: rgba(255,255,255,0.5);
    }
    .btn-outline-primary:hover{
      background-color: var(--primary-blue);
      border-color: var(--primary-blue);
      transform: translateY(-2px);
      color: white;
      box-shadow: 0 8px 20px -8px rgba(37,99,235,0.4);
    }

    .btn-danger{
      background: linear-gradient(135deg, #ef4444, #dc2626);
      border: none;
      border-radius: 14px;
      font-weight: 600;
      transition: all 0.3s ease;
      color: white;
      box-shadow: 0 8px 20px -8px rgba(239,68,68,0.4);
    }

    .btn-danger:hover{
      transform: translateY(-2px);
      box-shadow: 0 14px 28px rgba(239,68,68,0.35);
      background: linear-gradient(135deg, #dc2626, #ef4444);
      color: white;
    }

    /* form controls */
    .form-control, .form-select{
      border-radius: 14px;
      border: 2px solid rgba(0,0,0,0.1);
      padding: 0.85rem 1.1rem;
      background: rgba(255,255,255,0.7);
      transition: all 0.3s ease;
    }
    .form-control:focus, .form-select:focus{
      border-color: var(--primary-blue);
      box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.15);
      background: rgba(255,255,255,0.9);
    }

    .form-label{
      font-weight: 600;
      color: var(--text-dark);
      margin-bottom: 0.6rem;
      font-size: 0.95rem;
    }

    /* Enhanced badges with glow */
    .badge-soft{
      background: rgba(37,99,235,0.15);
      color: var(--primary-blue);
      border: 1px solid rgba(37,99,235,0.3);
      padding: 0.6rem 1.2rem;
      border-radius: 999px;
      font-weight: 600;
      box-shadow: 0 4px 12px rgba(37,99,235,0.15);
    }

    .badge-muted{
      background: rgba(100,116,139,0.15);
      color: #64748b;
      border: 1px solid rgba(100,116,139,0.25);
      padding: 0.6rem 1.2rem;
      border-radius: 999px;
      font-weight: 600;
    }

    .badge-success{
      background: rgba(34,197,94,0.15);
      color: #16a34a;
      border: 1px solid rgba(34,197,94,0.3);
      padding: 0.6rem 1.2rem;
      border-radius: 999px;
      font-weight: 600;
      box-shadow: 0 4px 12px rgba(34,197,94,0.15);
      animation: pulse 2s infinite;
    }

    .badge-danger{
      background: rgba(239,68,68,0.15);
      color: #dc2626;
      border: 1px solid rgba(239,68,68,0.3);
      padding: 0.6rem 1.2rem;
      border-radius: 999px;
      font-weight: 600;
    }

    .badge-primary{
      background: rgba(37,99,235,0.15);
      color: var(--primary-blue);
      border: 1px solid rgba(37,99,235,0.3);
      padding: 0.6rem 1.2rem;
      border-radius: 999px;
      font-weight: 600;
      box-shadow: 0 4px 12px rgba(37,99,235,0.15);
    }

    @keyframes pulse {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.8; }
    }

    /* alert boxes with icons */
    .alert{
      border-radius: 16px;
      border: 2px solid;
      padding: 1.2rem 1.5rem;
      font-weight: 500;
    }
    .alert-success{
      background: rgba(34,197,94,0.12);
      border-color: rgba(34,197,94,0.3);
      color: #166534;
    }
    .alert-danger{
      background: rgba(239,68,68,0.12);
      border-color: rgba(239,68,68,0.3);
      color: #991b1b;
    }
    .alert-info{
      background: rgba(59,130,246,0.12);
      border-color: rgba(59,130,246,0.3);
      color: #1e40af;
    }

    /* Enhanced info boxes with gradients */
    .info-box{
      background: linear-gradient(135deg, rgba(255,255,255,0.8), rgba(255,255,255,0.5));
      border-radius: 18px;
      padding: 1.5rem;
      border: 1px solid rgba(0,0,0,0.06);
      margin-bottom: 1rem;
      transition: all 0.3s ease;
      backdrop-filter: blur(10px);
    }

    .info-box:hover{
      transform: translateX(4px);
      box-shadow: 0 8px 24px rgba(0,0,0,0.08);
    }

    .info-box .icon-wrap{
      width: 56px;
      height: 56px;
      border-radius: 16px;
      background: linear-gradient(135deg, var(--primary-blue), var(--primary-blue-dark));
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 1.4rem;
      flex-shrink: 0;
      box-shadow: 0 8px 16px rgba(37,99,235,0.3);
    }

    .info-box h5{
      margin: 0 0 0.35rem 0;
      font-size: 1.15rem;
      font-weight: 700;
      color: var(--text-dark);
      letter-spacing: -0.01em;
    }

    .info-box p{
      margin: 0;
      color: var(--text-muted);
      font-size: 0.95rem;
      line-height: 1.5;
    }

    /* Main container */
    .dashboard-container{
      padding: 2.5rem 0;
      margin-left: 20px;
      position: relative;
      z-index: 1;
    }

    @media (max-width: 992px){
      .dashboard-container{
        margin-left: 0;
        padding: 1.5rem 0;
      }
    }

    /* Enhanced page header */
    .page-header{
      margin-bottom: 2.5rem;
    }

    .page-header h1{
      font-size: 2.5rem;
      font-weight: 800;
      color: var(--text-dark);
      margin: 0 0 0.5rem 0;
      letter-spacing: -0.03em;
      background: linear-gradient(135deg, var(--text-dark), var(--primary-blue));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .page-header p{
      color: var(--text-muted);
      margin: 0;
      font-size: 1.1rem;
    }

    /* Welcome banner */
    .welcome-banner{
      background: linear-gradient(135deg, var(--primary-blue), var(--primary-blue-dark));
      border-radius: 20px;
      padding: 2rem;
      color: white;
      margin-bottom: 2rem;
      box-shadow: 0 20px 40px -15px rgba(37,99,235,0.4);
      position: relative;
      overflow: hidden;
    }

    .welcome-banner::before{
      content: '';
      position: absolute;
      top: -50%;
      right: -10%;
      width: 300px;
      height: 300px;
      background: radial-gradient(circle, rgba(255,255,255,0.1), transparent 70%);
      border-radius: 50%;
    }

    .welcome-banner h3{
      font-weight: 700;
      margin: 0 0 0.5rem 0;
      font-size: 1.5rem;
    }

    .welcome-banner p{
      margin: 0;
      opacity: 0.9;
    }

    /* Stats row */
    .stats-row{
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1rem;
      margin-bottom: 2rem;
    }

    .stat-card{
      background: linear-gradient(135deg, rgba(255,255,255,0.8), rgba(255,255,255,0.5));
      border-radius: 18px;
      padding: 1.5rem;
      border: 1px solid rgba(255,255,255,0.5);
      box-shadow: 0 10px 30px -10px rgba(0,0,0,0.1);
      transition: all 0.3s ease;
    }

    .stat-card:hover{
      transform: translateY(-4px);
      box-shadow: 0 15px 40px -12px rgba(0,0,0,0.15);
    }

    .stat-card .stat-icon{
      width: 48px;
      height: 48px;
      border-radius: 14px;
      background: linear-gradient(135deg, rgba(37,99,235,0.15), rgba(37,99,235,0.05));
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--primary-blue);
      font-size: 1.5rem;
      margin-bottom: 1rem;
    }

    .stat-card h6{
      color: var(--text-muted);
      font-size: 0.85rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      margin: 0 0 0.5rem 0;
    }

    .stat-card .stat-value{
      font-size: 1.75rem;
      font-weight: 800;
      color: var(--text-dark);
      margin: 0;
      letter-spacing: -0.02em;
    }

    /* Flatpickr styling */
    .flatpickr-calendar{
      border-radius: 16px !important;
      box-shadow: 0 20px 50px -20px rgba(0,0,0,0.25) !important;
      border: 1px solid rgba(0,0,0,0.1) !important;
    }

    /* Logout button in header */
    .btn-logout{
      background: linear-gradient(135deg, rgba(239,68,68,0.1), rgba(220,38,38,0.05));
      border: 2px solid rgba(239,68,68,0.3);
      color: #dc2626;
      border-radius: 14px;
      font-weight: 600;
      padding: 0.6rem 1.2rem;
      transition: all 0.3s ease;
    }

    .btn-logout:hover{
      background: linear-gradient(135deg, #ef4444, #dc2626);
      border-color: #dc2626;
      color: white;
      transform: translateY(-2px);
      box-shadow: 0 8px 20px -8px rgba(239,68,68,0.4);
    }
  </style>
</head>

<body>

  <div class="dashboard-container">
    <div class="container">
      
      <!-- Welcome Banner -->
      <div class="welcome-banner">
        <div class="row align-items-center">
          <div class="col-lg-8">
            <h3><i class="fa-solid fa-sparkles me-2"></i>Welcome back, <?= h($me['fullname'] ?? 'Student') ?>!</h3>
            <p>Ready to continue your driving journey? Book your next lesson below.</p>
          </div>
          <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
            <a href="logout.php" class="btn btn-logout">
              <i class="fa-solid fa-right-from-bracket me-2"></i>Logout
            </a>
          </div>
        </div>
      </div>

      <!-- Page Header -->
      <div class="page-header">
        <div class="row align-items-center">
          <div class="col-lg-8">
            <h1><i class="fa-solid fa-gauge-high me-3"></i>Student Dashboard</h1>
            <p>Manage your lessons, track progress, and book new sessions</p>
          </div>
          <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
            <span class="badge <?= $statusClass ?>">
              <i class="fa-solid fa-circle-info me-1"></i> Status: <?= h($statusLabel) ?>
            </span>
          </div>
        </div>
      </div>

      <!-- Alert Messages -->
      <?php if (!empty($response['message'])): ?>
        <div class="alert <?= !empty($response['success']) ? 'alert-success' : 'alert-danger' ?>" role="alert">
          <i class="fa-solid <?= !empty($response['success']) ? 'fa-circle-check' : 'fa-triangle-exclamation' ?> me-2"></i>
          <?= h($response['message']) ?>
        </div>
      <?php endif; ?>

      <!-- Stats Row -->
      <div class="stats-row">
        <div class="stat-card">
          <div class="stat-icon">
            <i class="fa-solid fa-clock"></i>
          </div>
          <h6>Booking Status</h6>
          <p class="stat-value"><?= h($statusLabel) ?></p>
        </div>
        <div class="stat-card">
          <div class="stat-icon">
            <i class="fa-solid fa-calendar-check"></i>
          </div>
          <h6>Next Lesson</h6>
          <p class="stat-value" style="font-size: 1.1rem;">
            <?= !empty($me['requested_date']) ? date('M j', strtotime($me['requested_date'])) : 'Not Set' ?>
          </p>
        </div>
        <div class="stat-card">
          <div class="stat-icon">
            <i class="fa-solid fa-car"></i>
          </div>
          <h6>Vehicle</h6>
          <p class="stat-value" style="font-size: 1.1rem;">
            <?= !empty($me['vehicle_no']) ? h($me['vehicle_no']) : 'Pending' ?>
          </p>
        </div>
      </div>

      <div class="row g-4">
        
        <!-- Left Column - Student Information -->
        <div class="col-lg-6">
          <div class="glass-card p-4">
            <h4 class="section-title mb-4">
              <i class="fa-solid fa-id-card me-2"></i>Your Information
            </h4>

            <!-- Student Details -->
            <div class="info-box">
              <div class="d-flex align-items-center gap-3">
                <div class="icon-wrap">
                  <i class="fa-solid fa-user"></i>
                </div>
                <div class="flex-grow-1">
                  <h5><?= h($me['fullname'] ?? 'Student') ?></h5>
                  <p><?= h($me['email'] ?? '') ?> • <?= h($me['phonenumber'] ?? '—') ?></p>
                </div>
              </div>
            </div>

            <!-- Requested Slot -->
            <div class="info-box">
              <div class="d-flex align-items-center gap-3">
                <div class="icon-wrap">
                  <i class="fa-solid fa-calendar"></i>
                </div>
                <div class="flex-grow-1">
                  <h5>Requested Slot</h5>
                  <p><?= h($slotText) ?></p>
                </div>
              </div>
            </div>

            <!-- Assigned Vehicle -->
            <div class="info-box">
              <div class="d-flex align-items-center gap-3">
                <div class="icon-wrap">
                  <i class="fa-solid fa-car-side"></i>
                </div>
                <div class="flex-grow-1">
                  <h5>Assigned Vehicle</h5>
                  <p>
                    <?= !empty($me['vehicle_no'])
                          ? h($me['vehicle_no'].' — '.$me['vehicle_model'])
                          : '<span class="text-muted">Not assigned yet</span>' ?>
                  </p>
                </div>
              </div>
            </div>

            <!-- Assigned Driver -->
            <div class="info-box mb-0">
              <div class="d-flex align-items-center gap-3">
                <div class="icon-wrap">
                  <i class="fa-solid fa-user-tie"></i>
                </div>
                <div class="flex-grow-1">
                  <h5>Assigned Driver</h5>
                  <p>
                    <?= !empty($me['driver_name'])
                          ? h($me['driver_name']).(!empty($me['driver_phone']) ? ' • '.h($me['driver_phone']) : '')
                          : '<span class="text-muted">Not assigned yet</span>' ?>
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column - Booking Form -->
        <div class="col-lg-6">
          <div class="glass-card p-4 mb-4">
            <h4 class="section-title mb-2">
              <i class="fa-solid fa-calendar-plus me-2"></i>Book a Lesson
            </h4>
            <p class="section-subtitle mb-4">
              Pick a date & time. Your request will be processed based on driver and vehicle availability.
            </p>

            <form method="POST">
              <div class="mb-3">
                <label for="dtPicker" class="form-label">
                  <i class="fa-solid fa-clock me-1"></i>Date & Time <span class="text-danger">*</span>
                </label>
                <input 
                  id="dtPicker" 
                  type="text" 
                  class="form-control" 
                  placeholder="Click to select date and time..." 
                  required
                >
              </div>

              <!-- Hidden fields your controller expects -->
              <input type="hidden" name="requested_date" id="requested_date">
              <input type="hidden" name="requested_time" id="requested_time">

              <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                <button type="submit" name="request_booking" class="btn btn-primary px-4">
                  <i class="fa-solid fa-calendar-check me-2"></i>Request Booking
                </button>
                <a href="index.php" class="btn btn-outline-primary px-4">
                  <i class="fa-solid fa-house me-2"></i>Home
                </a>
              </div>

              <div class="alert alert-info mt-3 mb-0">
                <i class="fa-solid fa-lightbulb me-2"></i>
                <small><strong>Tip:</strong> After requesting, wait for admin to assign driver & vehicle. You'll be notified once confirmed.</small>
              </div>
            </form>
          </div>

        </div>

      </div>
    </div>
  </div>

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // ✅ Flatpickr datetime picker with enhanced options
    const dtPicker = flatpickr("#dtPicker", {
      enableTime: true,
      dateFormat: "Y-m-d H:i",
      time_24hr: true,
      minDate: "today",
      minuteIncrement: 15,
      defaultHour: 9,
      onChange: function(selectedDates, dateStr) {
        if (!dateStr) return;

        // dateStr = "YYYY-MM-DD HH:MM"
        const parts = dateStr.split(" ");
        if (parts.length === 2) {
          document.getElementById("requested_date").value = parts[0];
          document.getElementById("requested_time").value = parts[1];
        }
      },
      onReady: function(selectedDates, dateStr, instance) {
        // Add custom styling to calendar
        instance.calendarContainer.classList.add('flatpickr-custom');
      }
    });

    // If user types manually, ensure hidden fields updated on blur
    document.getElementById("dtPicker").addEventListener("blur", function(){
      const v = this.value.trim();
      if(!v) return;
      const parts = v.split(" ");
      if (parts.length === 2) {
        document.getElementById("requested_date").value = parts[0];
        document.getElementById("requested_time").value = parts[1];
      }
    });

    // Auto-dismiss alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
      const alerts = document.querySelectorAll('.alert');
      alerts.forEach(alert => {
        setTimeout(() => {
          alert.style.transition = 'opacity 0.5s ease';
          alert.style.opacity = '0';
          setTimeout(() => alert.remove(), 500);
        }, 5000);
      });
    });
  </script>
</body>
</html>