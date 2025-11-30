<?php
session_start();
require_once '../app/Controllers/AuthController.php';
$user = AuthController::requireAuth();

require_once '../app/Models/Order.php';
$orderModel = new Order();
$orders = $orderModel->getUserOrders($user['id']);

include '../app/Views/layouts/header.php';
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css" />
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

<?php if (isset($_SESSION['order_success'])): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Toastify({
            text: "<?= addslashes($_SESSION['order_success']) ?>",
            duration: 5000,
            gravity: "top",
            position: "center",
            backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
            stopOnFocus: true
        }).showToast();
    });
</script>
<?php unset($_SESSION['order_success']); ?>
<?php endif; ?>

<div class="container py-5">
    <h2 class="fw-bold mb-4">Lịch sử đơn hàng</h2>

    <?php if (empty($orders)): ?>
        <div class="text-center py-5">
            <i class="bi bi-receipt fs-1 text-muted"></i>
            <p class="mt-3 text-muted">Bạn chưa có đơn hàng nào.</p>
            <a href="index.php" class="btn btn-primary">Mua sắm ngay</a>
        </div>
    <?php else: ?>
        <div class="row">
            <?php
            $currentOrder = null;
            foreach ($orders as $order):
                if ($currentOrder !== $order['order_number']):
                    if ($currentOrder !== null) echo '</div></div></div>'; // Close previous order
                    $currentOrder = $order['order_number'];
            ?>
                <div class="col-12 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-1">Đơn hàng #<?= $order['order_number'] ?></h5>
                                    <small class="text-muted">
                                        lúc <?= date('H:i d/m/Y', strtotime($order['created_at'])) ?>
                                    </small>
                                </div>
                                <span class="badge bg-success">Đã đặt hàng</span>
                            </div>
                        </div>
                        <div class="card-body">
            <?php endif; ?>

                            <div class="row align-items-center mb-3">
                                <div class="col-md-2">
                                    <img src="<?= $order['image'] ?? 'https://via.placeholder.com/80' ?>" alt="" style="width: 80px; height: 80px; object-fit: cover;" class="rounded">
                                </div>
                                <div class="col-md-6">
                                    <h6 class="mb-1"><?= $order['product_name'] ?></h6>
                                    <small class="text-muted">Số lượng: <?= $order['quantity'] ?></small>
                                </div>
                                <div class="col-md-4 text-end">
                                    <div class="fw-bold text-danger">
                                        <?= number_format($order['price'], 0, ',', '.') ?> ₫ x <?= $order['quantity'] ?>
                                    </div>
                                    <div class="fw-bold">
                                        <?= number_format($order['price'] * $order['quantity'], 0, ',', '.') ?> ₫
                                    </div>
                                </div>
                            </div>

            <?php endforeach; ?>
                        </div>
                        <div class="card-footer bg-light">
                            <div class="text-end">
                                <strong>Tổng cộng: <?= number_format($order['total_amount'], 0, ',', '.') ?> ₫</strong>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../app/Views/layouts/footer.php'; ?>