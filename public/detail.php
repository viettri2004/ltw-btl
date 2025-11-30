<?php
session_start();
require_once '../config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

try {
    // 1. Lấy thông tin sản phẩm
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $product = $stmt->fetch();

    if (!$product) die("Sản phẩm không tồn tại");

    // 2. Xử lý Album ảnh
    $gallery = [];
    if (!empty($product['image'])) $gallery[] = $product['image'];
    if (!empty($product['image1'])) $gallery[] = $product['image1'];
    if (!empty($product['image2'])) $gallery[] = $product['image2'];
    if (!empty($product['image3'])) $gallery[] = $product['image3'];

    // 3. Lấy bình luận
    $stmtRev = $conn->prepare("SELECT * FROM reviews WHERE product_id = :pid ORDER BY created_at DESC");
    $stmtRev->execute(['pid' => $id]);
    $reviews = $stmtRev->fetchAll();

} catch(Exception $e) { die("Lỗi: " . $e->getMessage()); }

include '../app/Views/layouts/header.php';
?>

<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Trang chủ</a></li>
            <li class="breadcrumb-item active"><?= $product['name'] ?></li>
        </ol>
    </nav>

    <div class="row g-5">
        <div class="col-md-5">
            <div class="border rounded p-3 mb-3 text-center bg-white">
                <img id="mainImage" src="<?= $product['image'] ?>" class="img-fluid" style="max-height: 400px; object-fit: contain;">
            </div>
            <div class="d-flex gap-2 overflow-auto">
                <?php foreach($gallery as $imgUrl): ?>
                <img src="<?= $imgUrl ?>" class="detail-gallery-thumb border rounded p-1" style="width: 70px; height: 70px; object-fit: cover; cursor: pointer;" onclick="document.getElementById('mainImage').src=this.src">
                <?php endforeach; ?>
            </div>
        </div>

        <div class="col-md-7">
            <h3 class="fw-bold mb-3"><?= $product['name'] ?></h3>
            
            <div class="d-flex gap-3 mb-4">
                <span class="fs-3 fw-bold text-danger"><?= number_format($product['price'], 0, ',', '.') ?>đ</span>
                <?php if($product['old_price']): ?>
                <span class="fs-5 text-muted text-decoration-line-through align-self-center"><?= number_format($product['old_price'], 0, ',', '.') ?>đ</span>
                <?php endif; ?>
            </div>

            <div class="bg-light p-3 rounded mb-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-cpu"></i> Thông số kỹ thuật</h6>
                <table class="table table-sm table-borderless mb-0 small">
                    <tr><td class="text-muted" width="100">Chip xử lý:</td><td class="fw-bold"><?= $product['chip'] ?? 'N/A' ?></td></tr>
                    <tr><td class="text-muted">RAM:</td><td class="fw-bold"><?= $product['ram'] ?? 'N/A' ?></td></tr>
                    <tr><td class="text-muted">Màn hình:</td><td class="fw-bold"><?= $product['screen'] ?? 'N/A' ?></td></tr>
                    <tr><td class="text-muted">Pin:</td><td class="fw-bold"><?= $product['battery'] ?? 'N/A' ?></td></tr>
                    <tr><td class="text-muted">Bảo hành:</td><td class="fw-bold"><?= $product['guarantee'] ?></td></tr>
                </table>
            </div>

            <div class="d-flex gap-3 align-items-center mb-4">
                <div class="input-group" style="width: 120px;">
                    <button type="button" class="btn btn-outline-secondary" onclick="updateQty(-1)">-</button>
                    <input type="number" id="qtyInput" class="form-control text-center" value="1" min="1">
                    <button type="button" class="btn btn-outline-secondary" onclick="updateQty(1)">+</button>
                </div>
                
                <button type="button" 
                        class="btn btn-danger btn-lg flex-grow-1 btn-add-to-cart-detail"
                        data-id="<?= $product['id'] ?>"
                        data-name="<?= $product['name'] ?>"
                        data-price="<?= $product['price'] ?>"
                        data-image="<?= $product['image'] ?>">
                    <i class="bi bi-cart-plus"></i> THÊM VÀO GIỎ
                </button>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-md-8">
            <div class="mb-5">
                <h5 class="fw-bold border-bottom pb-2 mb-3">Đặc điểm nổi bật</h5>
                <div class="content-body text-secondary">
                    <?= nl2br($product['outstanding']) ?>
                </div>
            </div>

            <div class="bg-light p-4 rounded">
                <h5 class="fw-bold mb-4">Đánh giá & Bình luận (<?= count($reviews) ?>)</h5>
                
                <form action="submit_review.php" method="POST" class="mb-4">
                    <input type="hidden" name="product_id" value="<?= $id ?>">
                    <div class="mb-3">
                        <textarea name="comment" class="form-control" rows="3" required placeholder="Sản phẩm này thế nào?"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <input type="text" name="user_name" class="form-control" placeholder="Tên của bạn" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <select name="rating" class="form-select">
                                <option value="5">⭐⭐⭐⭐⭐ (Tuyệt vời)</option>
                                <option value="4">⭐⭐⭐⭐ (Tốt)</option>
                                <option value="3">⭐⭐⭐ (Bình thường)</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-2">Gửi đánh giá</button>
                </form>

                <div class="review-list">
                    <?php foreach($reviews as $rv): ?>
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <h6 class="fw-bold mb-1"><?= htmlspecialchars($rv['user_name']) ?></h6>
                                <span class="text-warning small">
                                    <?php for($i=0; $i<$rv['rating']; $i++) echo '<i class="bi bi-star-fill"></i>'; ?>
                                </span>
                            </div>
                            <small class="text-muted d-block mb-2"><?= date('d/m/Y', strtotime($rv['created_at'])) ?></small>
                            <p class="mb-0"><?= nl2br(htmlspecialchars($rv['comment'])) ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 11">
  <div id="liveToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body">
        <i class="bi bi-check-circle-fill me-2"></i> Đã thêm sản phẩm vào giỏ!
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>

<?php include '../app/Views/layouts/footer.php'; ?>

<script>
    // Hàm tăng giảm số lượng
    function updateQty(change) {
        let input = document.getElementById('qtyInput');
        let newVal = parseInt(input.value) + change;
        if (newVal >= 1) input.value = newVal;
    }

    // Xử lý sự kiện click nút Mua Ngay
    document.querySelector('.btn-add-to-cart-detail').addEventListener('click', function() {
        const btn = this;
        const qty = document.getElementById('qtyInput').value;
        const originalText = btn.innerHTML;

        // Hiệu ứng Loading
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Đang xử lý...';
        btn.disabled = true;

        // Chuẩn bị dữ liệu gửi đi
        const formData = new FormData();
        formData.append('product_id', btn.getAttribute('data-id'));
        formData.append('product_name', btn.getAttribute('data-name'));
        formData.append('product_price', btn.getAttribute('data-price'));
        formData.append('product_image', btn.getAttribute('data-image'));
        formData.append('quantity', qty); // Lấy số lượng từ input

        // Gửi AJAX
        fetch('cart_add.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Cập nhật Badge trên Header
                const badge = document.getElementById('cart-badge');
                if (badge) badge.innerText = data.total_items;

                // Hiện Toast thông báo
                const toastEl = document.getElementById('liveToast');
                const toast = new bootstrap.Toast(toastEl);
                toast.show();
            } else {
                alert('Lỗi: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Lỗi kết nối server');
        })
        .finally(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    });
</script>