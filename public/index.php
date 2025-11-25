<?php
session_start();
require_once '../config/database.php';

// Helper: Map Icon
function getCategoryIcon($name) {
    $name = mb_strtolower($name, 'UTF-8');
    if (strpos($name, 'laptop') !== false) return 'bi-laptop';
    if (strpos($name, 'điện thoại') !== false) return 'bi-phone';
    if (strpos($name, 'tai nghe') !== false) return 'bi-headphones';
    if (strpos($name, 'đồng hồ') !== false) return 'bi-smartwatch';
    if (strpos($name, 'tablet') !== false) return 'bi-tablet';
    if (strpos($name, 'camera') !== false) return 'bi-camera';
    return 'bi-box-seam';
}

try {
    // 1. Lấy sản phẩm nổi bật
    $stmt = $conn->prepare("SELECT * FROM products WHERE is_featured = 1 ORDER BY created_at DESC LIMIT 16");
    $stmt->execute();
    $featured_products = $stmt->fetchAll();
    
    // 2. Lấy sản phẩm Block (Điện thoại + Tablet)
    $stmtBlock = $conn->prepare("SELECT * FROM products WHERE category_id IN (2, 5) ORDER BY created_at DESC LIMIT 16");
    $stmtBlock->execute();
    $block_products = $stmtBlock->fetchAll();

    // 3. Lấy danh mục
    $stmtCat = $conn->query("SELECT * FROM categories LIMIT 6");
    $categories = $stmtCat->fetchAll();

} catch(Exception $e) { echo "Lỗi: " . $e->getMessage(); }

include '../app/Views/layouts/header.php';
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

<div class="side-banner side-left">
    <a href="#"><img src="https://via.placeholder.com/130x400?text=Sale+Left" class="img-fluid rounded shadow"></a>
</div>
<div class="side-banner side-right">
    <a href="#"><img src="https://via.placeholder.com/130x400?text=Sale+Right" class="img-fluid rounded shadow"></a>
</div>

<div id="homeCarousel" class="carousel slide mb-5" data-bs-ride="carousel">
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="banner-main.jpg" class="d-block w-100 object-fit-cover" style="max-height: 1050px;">
        </div>
        <div class="carousel-item">
            <img src="banner-main.jpg" class="d-block w-100 object-fit-cover" style="max-height: 1050px;">
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#homeCarousel" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span></button>
    <button class="carousel-control-next" type="button" data-bs-target="#homeCarousel" data-bs-slide="next"><span class="carousel-control-next-icon"></span></button>
</div>

<div class="container pb-5 position-relative">
    
    <div class="mb-5">
        <h4 class="fw-bold mb-4 border-start border-4 border-primary ps-3">Danh mục nổi bật</h4>
        <div class="row g-3">
            <?php foreach($categories as $cat): ?>
            <div class="col-6 col-md-2 text-center">
                <a href="products.php?cat_id=<?= $cat['id'] ?>" class="text-decoration-none text-dark card h-100 p-3 border-0 shadow-sm hover-shadow transition-card">
                    <i class="<?= getCategoryIcon($cat['name']) ?> fs-1 text-primary mb-2"></i>
                    <div class="fw-semibold small"><?= $cat['name'] ?></div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold border-start border-4 border-danger ps-3">Sản phẩm nổi bật</h4>
        <a href="products.php" class="btn btn-outline-dark btn-sm rounded-pill">Xem tất cả <i class="bi bi-arrow-right"></i></a>
    </div>

    <div class="position-relative mb-5">
        <div class="swiper product-slider">
            <div class="swiper-wrapper">
                <?php foreach($featured_products as $prod): ?>
                <div class="swiper-slide">
                    <?php include 'product_card_template.php'; ?> 
                </div>
                <?php endforeach; ?>
            </div>
            <div class="swiper-pagination"></div>
        </div>
        <div class="swiper-button-next" id="btn-next-featured"></div>
        <div class="swiper-button-prev" id="btn-prev-featured"></div>
    </div>

    <div class="mb-5">
        <div class="row g-3">
            <div class="col-12 col-xl-3 d-none d-xl-block">
                <div class="d-flex flex-column gap-3 h-100">
                    <a href="#" class="flex-grow-1 overflow-hidden rounded shadow-sm">
                        <img src="https://cdn2.cellphones.com.vn/insecure/rs:fill:690:300/q:10/plain/https://dashboard.cellphones.com.vn/storage/samsung-galaxy-s24-ultra-cate-08-04.jpg" class="w-100 h-100 object-fit-cover hover-zoom">
                    </a>
                    <a href="#" class="flex-grow-1 overflow-hidden rounded shadow-sm">
                        <img src="https://cdn2.cellphones.com.vn/insecure/rs:fill:690:300/q:10/plain/https://dashboard.cellphones.com.vn/storage/ipad-pro-m4-13-inch-cate-p-03-05.png" class="w-100 h-100 object-fit-cover hover-zoom">
                    </a>
                </div>
            </div>
            <div class="col-12 col-xl-9">
                <div class="bg-white rounded shadow-sm border p-3">
                    <div class="d-flex justify-content-between align-items-center category-block-header pb-2 mb-2">
                        <div class="d-flex gap-3">
                            <span class="cat-tab-btn active fs-5">ĐIỆN THOẠI</span>
                            <span class="cat-tab-btn fs-5 text-muted">MÁY TÍNH BẢNG</span>
                        </div>
                    </div>
                    <div class="position-relative">
                        <div class="swiper category-slider" style="height: 780px;">
                            <div class="swiper-wrapper">
                                <?php foreach($block_products as $prod): ?>
                                <div class="swiper-slide">
                                    <?php include 'product_card_template.php'; ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="swiper-button-next" id="btn-next-cat"></div>
                        <div class="swiper-button-prev" id="btn-prev-cat"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../app/Views/layouts/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    var featuredSwiper = new Swiper(".product-slider", {
        slidesPerView: 2, grid: { rows: 2, fill: 'row' }, spaceBetween: 15, slidesPerGroup: 1,
        navigation: { nextEl: "#btn-next-featured", prevEl: "#btn-prev-featured" },
        pagination: { el: ".swiper-pagination", clickable: true },
        breakpoints: { 768: { slidesPerView: 3, grid: { rows: 2 } }, 1200: { slidesPerView: 4, grid: { rows: 2 }, spaceBetween: 20 } }
    });
    var catSwiper = new Swiper(".category-slider", {
        slidesPerView: 2, grid: { rows: 2, fill: 'row' }, spaceBetween: 10, slidesPerGroup: 1,
        navigation: { nextEl: "#btn-next-cat", prevEl: "#btn-prev-cat" },
        breakpoints: { 768: { slidesPerView: 3, grid: { rows: 2 } }, 1200: { slidesPerView: 4, grid: { rows: 2 }, spaceBetween: 15 } }
    });
</script>