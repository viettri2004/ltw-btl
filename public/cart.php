<?php
session_start();

// Xử lý Xóa hoặc Cập nhật (Logic đơn giản ngay tại file view để tiện demo)
if (isset($_GET['action']) && $_GET['action'] == 'remove' && isset($_GET['id'])) {
    unset($_SESSION['cart'][$_GET['id']]);
    $_SESSION['cart_message'] = 'Đã xóa sản phẩm khỏi giỏ hàng';
    header('Location: cart.php');
    exit;
}

// Xử lý thanh toán
if (isset($_GET['action']) && $_GET['action'] == 'checkout') {
    require_once '../app/Controllers/AuthController.php';
    $user = AuthController::requireAuth();

    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        $_SESSION['error'] = 'Giỏ hàng trống!';
        header('Location: cart.php');
        exit;
    }

    require_once '../app/Models/Order.php';
    $orderModel = new Order();

    $totalMoney = 0;
    foreach ($_SESSION['cart'] as $item) {
        $totalMoney += $item['price'] * $item['quantity'];
    }

    $orderNumber = $orderModel->create($user['id'], $_SESSION['cart'], $totalMoney);

    // Xóa giỏ hàng
    unset($_SESSION['cart']);

    $_SESSION['order_success'] = 'Đặt hàng thành công! Mã đơn hàng: ' . $orderNumber;
    header('Location: history.php');
    exit;
}

include '../app/Views/layouts/header.php';
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css" />
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

<?php if (isset($_SESSION['cart_message'])): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Toastify({
            text: "<?= addslashes($_SESSION['cart_message']) ?>",
            duration: 3000,
            gravity: "top",
            position: "right",
            backgroundColor: "linear-gradient(to right, #ff5f6d, #ffc371)",
            stopOnFocus: true
        }).showToast();
    });
</script>
<?php unset($_SESSION['cart_message']); ?>
<?php endif; ?>

<div class="container py-5">
    <h2 class="fw-bold mb-4">Giỏ hàng của bạn</h2>

    <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
        <div class="row">
            <div class="col-md-8">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Giá</th>
                                <th>Số lượng</th>
                                <th>Tạm tính</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $total_money = 0;
                            foreach ($_SESSION['cart'] as $item): 
                                $line_total = $item['price'] * $item['quantity'];
                                $total_money += $line_total;
                            ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="<?= $item['image'] ?? 'https://via.placeholder.com/50' ?>" alt="" style="width: 50px; height: 50px; object-fit: cover;" class="rounded me-3">
                                        <div>
                                            <h6 class="mb-0"><?= $item['name'] ?></h6>
                                        </div>
                                    </div>
                                </td>
                                <td><?= number_format($item['price'], 0, ',', '.') ?>đ</td>
                                <td>
                                    <input type="number" class="form-control form-control-sm text-center" value="<?= $item['quantity'] ?>" min="1" style="width: 60px;" readonly>
                                    </td>
                                <td class="fw-bold text-danger"><?= number_format($line_total, 0, ',', '.') ?>đ</td>
                                <td>
                                    <a href="cart.php?action=remove&id=<?= $item['id'] ?>" class="text-danger" onclick="return confirm('Xóa sản phẩm này?')"><i class="bi bi-trash"></i></a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-light">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Tổng đơn hàng</h5>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Tạm tính:</span>
                            <span class="fw-bold"><?= number_format($total_money, 0, ',', '.') ?>đ</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <span class="fs-5 fw-bold">Tổng cộng:</span>
                            <span class="fs-5 fw-bold text-danger"><?= number_format($total_money, 0, ',', '.') ?>đ</span>
                        </div>
                        <a href="#" onclick="confirmCheckout()" class="btn btn-dark w-100 py-2">Tiến hành thanh toán</a>
                        <a href="index.php" class="btn btn-outline-secondary w-100 mt-2">Tiếp tục mua sắm</a>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <i class="bi bi-cart-x fs-1 text-muted"></i>
            <p class="mt-3 text-muted">Giỏ hàng của bạn đang trống.</p>
            <a href="index.php" class="btn btn-primary">Mua sắm ngay</a>
        </div>
    <?php endif; ?>
</div>

<script>
function confirmCheckout() {
    if (confirm('Bạn có chắc chắn muốn thanh toán đơn hàng này?')) {
        window.location.href = 'cart.php?action=checkout';
    }
}
</script>

<?php include '../app/Views/layouts/footer.php'; ?>