<?php
session_start();
require_once('../config/db.php');
require_once('../controllers/AuthController.php');

$dbObject = new Config\Database(); 
$pdo = $dbObject->connect(); 
$auth = new AuthController($pdo);
$error = $auth->login();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Driving System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
  <style>
    :root {
        --primary: #6366f1;
        --glass: rgba(255, 255, 255, 0.03);
        --border: rgba(255, 255, 255, 0.1);
    }

    body {
        margin: 0;
        font-family: 'Inter', sans-serif;
        background: #0f172a;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        background-image: radial-gradient(circle at top right, #1e1b4b, #0f172a);
    }

    .login-card {
        background: var(--glass);
        backdrop-filter: blur(16px); /* Increased blur for better depth */
        border: 1px solid var(--border);
        padding: 2.5rem;
        border-radius: 1.5rem;
        width: 100%;
        max-width: 400px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    }

    header { text-align: center; margin-bottom: 2rem; }
    h1 { font-size: 1.5rem; margin: 0; letter-spacing: -0.5px; }
    p { color: #94a3b8; font-size: 0.9rem; margin-top: 0.5rem; }

    .form-group { margin-bottom: 1.25rem; }
    label { display: block; font-size: 0.8rem; margin-bottom: 0.5rem; color: #cbd5e1; }
    
    input {
        width: 100%;
        padding: 0.8rem 1rem;
        background: rgba(0, 0, 0, 0.2);
        border: 1px solid var(--border);
        border-radius: 0.75rem;
        color: white;
        transition: all 0.2s;
        box-sizing: border-box;
    }

    input:focus {
        outline: none;
        border-color: var(--primary);
        background: rgba(0, 0, 0, 0.4);
    }

    /* Updated Transparent Button Style */
    .btn {
        width: 100%;
        padding: 0.8rem;
        background: rgba(255, 255, 255, 0.05); /* Transparent background */
        border: 1px solid var(--border); /* Matching border */
        border-radius: 0.75rem;
        color: white;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 1rem;
        backdrop-filter: blur(4px);
    }

    .btn:hover {
        background: var(--primary); /* Glow effect on hover */
        border-color: var(--primary);
        box-shadow: 0 0 20px rgba(99, 102, 241, 0.4);
        transform: translateY(-1px);
    }

    .error {
        background: rgba(239, 68, 68, 0.1);
        color: #f87171;
        padding: 0.75rem;
        border-radius: 0.5rem;
        font-size: 0.85rem;
        margin-bottom: 1.5rem;
        border: 1px solid rgba(239, 68, 68, 0.2);
    }
</style>
</head>
<body>

<div class="login-card">
    <header>
        <h1>Driving System</h1>
        <p>Sign in to your account</p>
    </header>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" placeholder="name@example.com" required autofocus>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn">Sign In</button>
    </form>
</div>

</body>
</html>