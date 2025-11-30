<?php
// config/database.php

$host = 'localhost';
$dbname = 'techstore_db';
$username = 'root'; // Mặc định của XAMPP là root
$password = '';     // Mặc định của XAMPP là rỗng

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Thiết lập chế độ lỗi để dễ debug
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Thiết lập chế độ lấy dữ liệu mặc định là mảng kết hợp (Associative Array)
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Lỗi kết nối Database: " . $e->getMessage());
}
?>

