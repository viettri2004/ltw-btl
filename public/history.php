<?php
session_start();
require_once '../config/database.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    // Giả sử bảng orders có: id, user_id, total_money, status, created_at
    // Nếu chưa có bảng này, bạn cần tạo trong DB
    $stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = :uid ORDER BY created_at DESC");
    $stmt->execute([':uid' => $user_id]);
    $orders = $stmt->fetchAll();

} catch(Exception $e) {
    // Nếu lỗi (ví dụ chưa tạo bảng orders) thì gán mảng rỗng để không crash web
    $orders = [];
    // echo "Lỗi: " . $e->getMessage(); 
}

include '../app/Views/layouts/header.php';
?>

<div class="container py-5">
    <h3 class="fw-bold mb-4">Lịch sử đơn hàng</h3>

    <?php if(count($orders) > 0): ?>
        <div class="table-responsive">
            <table class="table table-hover border">
                <thead class="table-light">
                    <tr>
                        <th>Mã đơn</th>
                        <th>Ngày đặt</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Chi tiết</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($orders as $order): ?>
                    <tr>
                        <td>#<?= $order['id'] ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                        <td class="fw-bold text-danger"><?= number_format($order['total_money'], 0, ',', '.') ?>đ</td>
                        <td>
                            <?php 
                                // Badge trạng thái đơn giản
                                $statusClass = 'bg-secondary';
                                if($order['status'] == 'completed') $statusClass = 'bg-success';
                                if($order['status'] == 'pending') $statusClass = 'bg-warning text-dark';
                                if($order['status'] == 'cancelled') $statusClass = 'bg-danger';
                            ?>
                            <span class="badge <?= $statusClass ?>"><?= ucfirst($order['status']) ?></span>
                        </td>
                        <td><a href="order_detail.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-outline-primary">Xem</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="text-center p-5 bg-light rounded">
            <i class="bi bi-inbox fs-1 text-muted"></i>
            <p class="mt-3">Bạn chưa có đơn hàng nào.</p>
            <a href="index.php" class="btn btn-dark">Mua sắm ngay</a>
        </div>
    <?php endif; ?>
</div>

<?php include '../app/Views/layouts/footer.php'; ?>