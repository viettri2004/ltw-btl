<?php
require_once __DIR__ . '/../Controllers/AuthController.php';
require_once __DIR__ . '/../Models/User.php';

$user = AuthController::requireAuth();
$userModel = new User();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'full_name' => trim($_POST['full_name']),
        'phone' => trim($_POST['phone'] ?? ''),
        'address' => trim($_POST['address'] ?? '')
    ];

    if (empty($data['full_name'])) {
        $_SESSION['error'] = 'Họ và tên không được để trống.';
    } else {
        if ($userModel->update($user['id'], $data)) {
            $_SESSION['success'] = 'Cập nhật thông tin thành công!';
            $user = $userModel->findById($user['id']); // Refresh user data
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra. Vui lòng thử lại.';
        }
    }
    header('Location: profile.php');
    exit;
}
?>

<?php include 'layouts/header.php'; ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css" />
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

<?php if (isset($_SESSION['success'])): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Toastify({
            text: "<?= addslashes($_SESSION['success']) ?>",
            duration: 3000,
            gravity: "top",
            position: "right",
            backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
            stopOnFocus: true
        }).showToast();
    });
</script>
<?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Toastify({
            text: "<?= addslashes($_SESSION['error']) ?>",
            duration: 3000,
            gravity: "top",
            position: "right",
            backgroundColor: "linear-gradient(to right, #ff5f6d, #ffc371)",
            stopOnFocus: true
        }).showToast();
    });
</script>
<?php unset($_SESSION['error']); ?>
<?php endif; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow">
                <div class="card-body p-4">
                    <h2 class="card-title text-center mb-4">Thông tin cá nhân</h2>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Họ và tên *</label>
                            <input type="text" class="form-control" id="full_name" name="full_name"
                                   value="<?= htmlspecialchars($user['full_name']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" value="<?= htmlspecialchars($user['email']) ?>" readonly>
                            <div class="form-text">Email không thể thay đổi</div>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Số điện thoại</label>
                            <input type="tel" class="form-control" id="phone" name="phone"
                                   value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Địa chỉ</label>
                            <textarea class="form-control" id="address" name="address" rows="3"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ngày tạo tài khoản</label>
                            <input type="text" class="form-control" value="<?= date('d/m/Y H:i', strtotime($user['created_at'])) ?>" readonly>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Cập nhật thông tin</button>
                    </form>

                    <div class="text-center mt-3">
                        <a href="history.php" class="btn btn-outline-primary">Xem lịch sử đơn hàng</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'layouts/footer.php'; ?>