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
    // 1. Lấy sản phẩm nổi bật (Featured)
    $stmt = $conn->prepare("SELECT * FROM products WHERE is_featured = 1 ORDER BY created_at DESC LIMIT 16");
    $stmt->execute();
    $featured_products = $stmt->fetchAll();
    
    // 2. [UPDATE] Lấy riêng Điện thoại (Cat ID = 2)
    $stmtPhone = $conn->prepare("SELECT * FROM products WHERE category_id = 2 ORDER BY created_at DESC LIMIT 16");
    $stmtPhone->execute();
    $phone_products = $stmtPhone->fetchAll();

    // 3. [UPDATE] Lấy riêng Tablet (Cat ID = 5)
    $stmtTablet = $conn->prepare("SELECT * FROM products WHERE category_id = 5 ORDER BY created_at DESC LIMIT 16");
    $stmtTablet->execute();
    $tablet_products = $stmtTablet->fetchAll();

    // 4. Lấy danh mục
    $stmtCat = $conn->query("SELECT * FROM categories LIMIT 6");
    $categories = $stmtCat->fetchAll();


    // 4. [NEW] Lấy tin tức (Lấy 15 bài: 5 hiện + 10 ẩn)
    // Giả sử bạn đã tạo bảng news và thêm dữ liệu như hướng dẫn trước
    $stmtNews = $conn->query("SELECT * FROM news ORDER BY created_at DESC LIMIT 15");
    $news_list = $stmtNews->fetchAll();

} catch(Exception $e) { echo "Lỗi: " . $e->getMessage(); }





// // [NEW QUERY] Lấy tin tức mới nhất
// try {
//     // Nếu database chưa có đủ tin, hãy vào phpMyAdmin thêm dữ liệu nhé
//     $stmtNews = $conn->query("SELECT * FROM news ORDER BY created_at DESC LIMIT 15");
//     $news_list = $stmtNews->fetchAll();
// } catch(Exception $e) { $news_list = []; }

// // Danh sách Thương hiệu (Tĩnh - Không cần DB)
// $brands = [
//     ['img' => 'pic/brand1.webp', 'link' => '#'],
//     ['img' => 'pic/brand1.webp', 'link' => '#'],
//     ['img' => 'pic/brand1.webp', 'link' => '#'],
//     ['img' => 'pic/brand1.webp', 'link' => '#']
// ];

// [NEW QUERY] Lấy tin tức mới nhất
try {
    // Nếu database chưa có đủ tin, hãy vào phpMyAdmin thêm dữ liệu nhé
    $stmtNews = $conn->query("SELECT * FROM news ORDER BY created_at DESC LIMIT 16");
    $news_list = $stmtNews->fetchAll();
} catch(Exception $e) { $news_list = []; }
// Danh sách Thương hiệu (Tĩnh - Không cần DB)
$brands = [
    ['img' => 'https://www.apple.com/newsroom/images/2024/09/apple-debuts-iphone-16-pro-and-iphone-16-pro-max/tile/Apple-iPhone-16-Pro-hero-240909-lp.jpg.landing-big_2x.jpg', 'link' => 'https://www.apple.com/vn/'],
    ['img' => 'https://dlcdnwebimgs.asus.com/files/media/b750b172-b8c8-462b-9f28-92519392fce6/v1/img/kv/kv.jpg', 'link' => 'https://www.asus.com/vn/'],
    ['img' => 'https://www.sammobile.com/wp-content/uploads/2023/05/Samsung-x-Lazada-Super-Brand-Day-2023-Galaxy-Tab-S7-FE-Deal.png', 'link' => 'https://www.samsung.com/vn/'],
    ['img' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:690:300/q:90/plain/https://dashboard.cellphones.com.vn/storage/gian-hang-samsung-home.png', 'link' => 'https://www.mi.com/vn/']
];


include '../app/Views/layouts/header.php';
?>

<div class="cont-all">
    <div class="side-banner side-left d-none d-xxl-block">
        <a href="#">
            <img src="banner-side.jpg" class="img-fluid rounded shadow">
        </a>
    </div>

    <div class="all-body">

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

        <!-- <div class="side-banner side-left">
            <a href="#"><img src="banner-side.jpg" class="img-fluid rounded shadow"></a>
        </div>
        <div class="side-banner side-right">
            <a href="#"><img src="banner-side.jpg" class="img-fluid rounded shadow"></a>
        </div> -->

        <div class="container pb-5 position-relative">
            
            <div id="homeCarousel" class="carousel slide mb-5" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="banner-main.jpg" class="d-block w-100 object-fit-cover" style="max-height: 450px;">
                    </div>
                    <div class="carousel-item">
                        <img src="banner-main.jpg" class="d-block w-100 object-fit-cover" style="max-height: 450px;">
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#homeCarousel" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span></button>
                <button class="carousel-control-next" type="button" data-bs-target="#homeCarousel" data-bs-slide="next"><span class="carousel-control-next-icon"></span></button>
            </div>

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
                                <img src="banner-side.jpg" class="w-100 h-100 object-fit-cover hover-zoom">
                            </a>
                            <a href="#" class="flex-grow-1 overflow-hidden rounded shadow-sm">
                                <img src="banner-side.jpg" class="w-100 h-100 object-fit-cover hover-zoom">
                            </a>
                        </div>
                    </div>

                    <div class="col-12 col-xl-9">
                        <div class="bg-white rounded shadow-sm border p-3">
                            <div class="d-flex justify-content-between align-items-center category-block-header pb-2 mb-2">
                                <div class="d-flex gap-3">
                                    <span class="cat-tab-btn active fs-5" onclick="switchTab('phone')" id="tab-phone">ĐIỆN THOẠI</span>
                                    <span class="cat-tab-btn fs-5 text-muted" onclick="switchTab('tablet')" id="tab-tablet">MÁY TÍNH BẢNG</span>
                                </div>
                            </div>

                            <div id="block-phone" class="tab-content-block">
                                <div class="position-relative">
                                    <div class="swiper phone-slider" style="height: 780px;">
                                        <div class="swiper-wrapper">
                                            <?php foreach($phone_products as $prod): ?>
                                            <div class="swiper-slide">
                                                <?php include 'product_card_template.php'; ?>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <div class="swiper-button-next" id="btn-next-phone"></div>
                                    <div class="swiper-button-prev" id="btn-prev-phone"></div>
                                </div>
                            </div>

                            <div id="block-tablet" class="tab-content-block d-none">
                                <div class="position-relative">
                                    <div class="swiper tablet-slider" style="height: 780px;">
                                        <div class="swiper-wrapper">
                                            <?php foreach($tablet_products as $prod): ?>
                                            <div class="swiper-slide">
                                                <?php include 'product_card_template.php'; ?>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <div class="swiper-button-next" id="btn-next-tablet"></div>
                                    <div class="swiper-button-prev" id="btn-prev-tablet"></div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>



        

         <!-- Phần Chuyên trang thương hiệu -->
        <div class="container mb-5">
            <h4 class="fw-bold mb-4 border-start border-4 border-primary ps-3">CHUYÊN TRANG THƯƠNG HIỆU</h4>
            <div class="row g-3">
                <?php foreach($brands as $brand): ?>
                <div class="col-6 col-md-3">
                    <a href="<?= $brand['link'] ?>" target="_blank">
                        <img src="<?= $brand['img'] ?>" class="img-fluid rounded shadow card-brand-item" alt="Banner thương hiệu">
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Phần Tin tức -->
        <div class="container mb-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="fw-bold border-start border-4 border-info ps-3">TIN TỨC</h4>
                <a href="news.php" class="btn btn-outline-dark btn-sm rounded-pill">Xem tất cả <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="row row-cols-2 row-cols-md-5 g-3">
                <?php foreach($news_list as $index => $news): ?>
                <div class="col<?= ($index >= 5) ? ' news-hidden' : '' ?>">
                    <a href="<?= $news['link'] ?>" class="text-decoration-none">
                        <div class="card h-100 border-0 shadow-sm">
                            <img src="<?= $news['image'] ?>" class="card-img-top rounded" alt="<?= $news['title'] ?>">
                            <div class="card-body p-2">
                                <h6 class="card-title small text-dark"><?= $news['title'] ?></h6>
                            </div>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
            <?php if (count($news_list) > 5): ?>
            <div class="text-center mt-4">
                <button id="btnLoadMoreNews" class="btn btn-outline-primary rounded-pill">Xem thêm</button>
            </div>
            <?php endif; ?>
        </div>
                                                



    </div>

    <div class="side-banner side-right d-none d-xxl-block">
        <a href="#">
            <img src="banner-side.jpg" class="img-fluid rounded shadow">
        </a>
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

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    // 1. Slider Sản phẩm nổi bật
    var featuredSwiper = new Swiper(".product-slider", {
        slidesPerView: 2, grid: { rows: 2, fill: 'row' }, spaceBetween: 15, slidesPerGroup: 1,
        navigation: { nextEl: "#btn-next-featured", prevEl: "#btn-prev-featured" },
        pagination: { el: ".swiper-pagination", clickable: true },
        breakpoints: { 768: { slidesPerView: 3, grid: { rows: 2 } }, 1200: { slidesPerView: 4, grid: { rows: 2 }, spaceBetween: 20 } }
    });

    // 2. Slider Điện thoại
    var phoneSwiper = new Swiper(".phone-slider", {
        slidesPerView: 2, grid: { rows: 2, fill: 'row' }, spaceBetween: 10, slidesPerGroup: 1,
        navigation: { nextEl: "#btn-next-phone", prevEl: "#btn-prev-phone" },
        breakpoints: { 768: { slidesPerView: 3, grid: { rows: 2 } }, 1200: { slidesPerView: 4, grid: { rows: 2 }, spaceBetween: 15 } }
    });

    // 3. Slider Tablet
    var tabletSwiper = new Swiper(".tablet-slider", {
        slidesPerView: 2, grid: { rows: 2, fill: 'row' }, spaceBetween: 10, slidesPerGroup: 1,
        navigation: { nextEl: "#btn-next-tablet", prevEl: "#btn-prev-tablet" },
        breakpoints: { 768: { slidesPerView: 3, grid: { rows: 2 } }, 1200: { slidesPerView: 4, grid: { rows: 2 }, spaceBetween: 15 } }
    });

    // --- Hàm xử lý chuyển Tab ---
    function switchTab(type) {
        // 1. Xử lý style cho nút Tab
        document.querySelectorAll('.cat-tab-btn').forEach(btn => {
            btn.classList.remove('active');
            btn.classList.add('text-muted');
        });
        let activeBtn = document.getElementById('tab-' + type);
        activeBtn.classList.add('active');
        activeBtn.classList.remove('text-muted');

        // 2. Ẩn hiện nội dung Slider
        document.querySelectorAll('.tab-content-block').forEach(block => {
            block.classList.add('d-none');
        });
        document.getElementById('block-' + type).classList.remove('d-none');
    }

    // Load More News Logic
    document.getElementById('btnLoadMoreNews')?.addEventListener('click', function() {
        const hiddenNews = document.querySelectorAll('.news-hidden');
        let count = 0;
        hiddenNews.forEach(news => {
            if (count < 10) { // Hiện thêm 10 tin (2 hàng x 5 cột)
                news.classList.remove('news-hidden');
                count++;
            }
        });
        if (document.querySelectorAll('.news-hidden').length === 0) {
            this.style.display = 'none';
        }
    });

</script>


<script>
    // Bắt sự kiện click cho TẤT CẢ các nút có class .btn-add-to-cart
    // Dùng Event Delegation để đảm bảo hoạt động kể cả với các nút trong Slider
    document.addEventListener('click', function(e) {
        // Kiểm tra nếu click vào nút hoặc icon bên trong nút
        const btn = e.target.closest('.btn-add-to-cart');
        
        if (btn) {
            e.preventDefault(); // Chặn hành động mặc định

            // Hiệu ứng loading nhỏ (tùy chọn)
            const originalIcon = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
            btn.disabled = true;

            // Lấy dữ liệu từ data attributes
            const formData = new FormData();
            formData.append('product_id', btn.getAttribute('data-id'));
            formData.append('product_name', btn.getAttribute('data-name'));
            formData.append('product_price', btn.getAttribute('data-price'));
            formData.append('product_image', btn.getAttribute('data-image'));
            formData.append('quantity', 1);

            // Gửi AJAX fetch
            fetch('cart_add.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // 1. Cập nhật số lượng trên Header
                    const badge = document.getElementById('cart-badge');
                    if (badge) badge.innerText = data.total_items;

                    // 2. Hiển thị Toast thông báo thành công
                    const toastEl = document.getElementById('liveToast');
                    const toast = new bootstrap.Toast(toastEl);
                    toast.show();
                } else {
                    alert('Có lỗi xảy ra: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Lỗi kết nối server');
            })
            .finally(() => {
                // Trả lại trạng thái nút ban đầu
                btn.innerHTML = originalIcon;
                btn.disabled = false;
            });
        }
    });
</script>