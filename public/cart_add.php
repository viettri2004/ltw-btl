<?php
session_start();

// Kiểm tra request method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Lấy dữ liệu từ Ajax gửi sang
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

        // Tính tổng số lượng sản phẩm trong giỏ để trả về client
        $total_items = 0;
        foreach ($_SESSION['cart'] as $item) {
            $total_items += $item['quantity'];
        }

        // Trả về JSON thành công
        echo json_encode([
            'status' => 'success',
            'message' => 'Đã thêm sản phẩm vào giỏ!',
            'total_items' => $total_items
        ]);
    } else {
        // Lỗi thiếu ID
        echo json_encode(['status' => 'error', 'message' => 'Lỗi dữ liệu sản phẩm']);
    }
    exit();
}
?>