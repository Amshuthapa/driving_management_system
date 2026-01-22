<?php
require_once '../config/db.php';
require_once '../models/DriverModel.php';
$model = new DriverModel($pdo);
$query = $_GET['q'] ?? '';
$results = $model->search($query);

foreach ($results as $r) {
    echo "<div>" . htmlspecialchars($r['name']) . " - " . htmlspecialchars($r['vehicle_type']) . "</div>";
}
?>