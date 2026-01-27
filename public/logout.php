<?php
// logout.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Unset all session variables
$_SESSION = [];

// Destroy session
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Logging out...</title>
</head>
<body>

<script>
    // Clear any client-side state (safe even if unused)
    localStorage.clear();
    sessionStorage.clear();

    // Redirect to public home page
    window.location.href = "index.php";
</script>

</body>
</html>
