<?php
require_once '../controllers/AuthController.php';
// You don't need a DB connection just to logout
$auth = new AuthController(null); 
$auth->logout();
?>
