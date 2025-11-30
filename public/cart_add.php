<?php
session_start();

// Kiểm tra đăng nhập
require_once '../app/Controllers/AuthController.php';
$user = AuthController::getCurrentUser();
if (!$user) {
    $_SESSION['error'] = 'Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng';
    header('Location: login.php');
    exit();
}

// Kiểm tra request method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Lấy dữ liệu từ form gửi sang
    $id = $_POST['product_id'] ?? null;
    $name = $_POST['product_name'] ?? '';
    $price = $_POST['product_price'] ?? 0;
    $image = $_POST['product_image'] ?? '';
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    if ($id) {
        // Khởi tạo giỏ hàng
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Logic thêm/cộng dồn
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

        // Thông báo thành công
        $_SESSION['cart_message'] = 'Đã thêm sản phẩm vào giỏ!';
        header('Location: index.php');
        exit();
    } else {
        // Lỗi thiếu ID
        $_SESSION['error'] = 'Lỗi dữ liệu sản phẩm';
        header('Location: index.php');
        exit();
    }
}
?>