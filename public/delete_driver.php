<?php
// 1. Include necessary files
require_once '../config/db.php';
require_once '../controllers/DriverController.php';

// 2. Connect to the Database
$dbObject = new \Config\Database();
$pdo = $dbObject->connect();

// 3. Initialize Controller
$controller = new DriverController($pdo);

// 4. Get the ID from the URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $controller->delete($id);
} else {
    // If no ID is provided, just go back home
    header("Location: index.php");
    exit();
}
?>
