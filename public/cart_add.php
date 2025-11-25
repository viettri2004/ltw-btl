<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ form
    $id = $_POST['product_id'];
    $name = $_POST['product_name']; // Lưu ý: Form ở index.php cần gửi thêm hidden field này hoặc phải query DB để lấy tên
    $price = $_POST['product_price'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    $image = isset($_POST['product_image']) ? $_POST['product_image'] : ''; // Cần thêm hidden field image ở form

    // Khởi tạo giỏ hàng
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Logic thêm hoặc cộng dồn
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['quantity'] += $quantity;
    } else {
        $_SESSION['cart'][$id] = [
            'id' => $id,
            'name' => $name,
            'price' => $price,
            'quantity' => $quantity,
            'image' => $image
        ];
    }

    // Redirect trở lại trang trước đó
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}
?>