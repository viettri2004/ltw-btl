<?php
session_start();
require_once '../app/Controllers/AuthController.php';

$auth = new AuthController();
$auth->logout();
?>