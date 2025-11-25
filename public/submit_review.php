<?php
// public/submit_review.php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pid = $_POST['product_id'];
    $name = $_POST['user_name'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    try {
        $stmt = $conn->prepare("INSERT INTO reviews (product_id, user_name, rating, comment) VALUES (?, ?, ?, ?)");
        $stmt->execute([$pid, $name, $rating, $comment]);
    } catch(Exception $e) {
        // Handle error
    }
    // Quay lại trang detail
    header("Location: detail.php?id=$pid");
    exit();
}
?>