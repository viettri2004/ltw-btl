<?php
session_start();
require_once '../config/database.php';

// --- 1. NHẬN THAM SỐ TỪ URL ---
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$cat_id = isset($_GET['cat_id']) ? (int)$_GET['cat_id'] : 0;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Tham số Lọc Giá (Mặc định min=0, max=100 triệu nếu không nhập)
$price_min = isset($_GET['price_min']) && $_GET['price_min'] !== '' ? (int)$_GET['price_min'] : 0;
$price_max = isset($_GET['price_max']) && $_GET['price_max'] !== '' ? (int)$_GET['price_max'] : 100000000;

// Tham số Phân trang
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 12; // Số sản phẩm mỗi trang
$page = max(1, $page); // Đảm bảo page luôn >= 1
$offset = ($page - 1) * $limit;

// --- 2. XÂY DỰNG CÂU TRUY VẤN SQL ---
$where = "WHERE 1=1";
$params = [];

// Lọc Từ khóa
if (!empty($search)) {
    $where .= " AND name LIKE :search";
    $params[':search'] = "%$search%";
}

// Lọc Danh mục
if ($cat_id > 0) {
    $where .= " AND category_id = :cat_id";
    $params[':cat_id'] = $cat_id;
}

// Lọc Giá tiền
if (isset($_GET['price_min']) || isset($_GET['price_max'])) {
    $where .= " AND price BETWEEN :min AND :max";
    $params[':min'] = $price_min;
    $params[':max'] = $price_max;
}

// Sắp xếp
$orderBy = "ORDER BY created_at DESC";
switch ($sort) {
    case 'price_asc': $orderBy = "ORDER BY price ASC"; break;
    case 'price_desc': $orderBy = "ORDER BY price DESC"; break;
    case 'name_asc': $orderBy = "ORDER BY name ASC"; break;
    default: $orderBy = "ORDER BY created_at DESC"; break;
}

try {
    // A. ĐẾM TỔNG SỐ SẢN PHẨM (Để tính số trang)
    $countSql = "SELECT COUNT(*) FROM products $where";
    $stmtCount = $conn->prepare($countSql);
    $stmtCount->execute($params);
    $total_products = $stmtCount->fetchColumn();
    $total_pages = ceil($total_products / $limit);

    // B. LẤY DỮ LIỆU SẢN PHẨM (Có Limit & Offset)
    $sql = "SELECT * FROM products $where $orderBy LIMIT $limit OFFSET $offset";
    $stmt = $conn->prepare($sql);
    
    // Bind params thủ công vì Limit/Offset trong PDO đôi khi kén kiểu dữ liệu
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    $products = $stmt->fetchAll();

    // C. LẤY DANH MỤC (Cho Sidebar)
    $stmtCat = $conn->query("
        SELECT c.*, COUNT(p.id) as product_count 
        FROM categories c 
        LEFT JOIN products p ON c.id = p.category_id 
        GROUP BY c.id
    ");
    $categories = $stmtCat->fetchAll();

} catch(Exception $e) {
    echo "Lỗi: " . $e->getMessage();
}

// Helper: Tạo URL giữ nguyên các tham số lọc
function buildUrl($key, $value) {
    $params = $_GET;
    $params[$key] = $value;
    // Khi đổi bộ lọc, luôn reset về trang 1 (trừ khi đang bấm chuyển trang)
    if ($key != 'page') {
        $params['page'] = 1;
    }
    return '?' . http_build_query($params);
}

include '../app/Views/layouts/header.php';
?>

<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-dark">Trang chủ</a></li>
            <li class="breadcrumb-item active">Sản phẩm</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-3 mb-4">
            <div class="filter-sidebar bg-white p-3 rounded shadow-sm border">
                <div class="filter-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-funnel"></i> Bộ lọc</span>
                    <?php if(count($_GET) > 0): ?>
                        <a href="products.php" class="small text-danger text-decoration-none">Xóa hết</a>
                    <?php endif; ?>
                </div>

                <div class="mb-4">
                    <h6 class="fw-bold mb-2 small text-uppercase">Danh mục</h6>
                    <div class="list-group">
                        <a href="<?= buildUrl('cat_id', 0) ?>" 
                           class="list-group-item d-flex justify-content-between align-items-center <?= $cat_id == 0 ? 'active' : '' ?>">
                            Tất cả
                        </a>
                        <?php foreach($categories as $cat): ?>
                        <a href="<?= buildUrl('cat_id', $cat['id']) ?>" 
                           class="list-group-item d-flex justify-content-between align-items-center <?= $cat_id == $cat['id'] ? 'active' : '' ?>">
                            <?= $cat['name'] ?>
                            <span class="badge bg-light text-dark border rounded-pill"><?= $cat['product_count'] ?></span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="fw-bold mb-2 small text-uppercase">Khoảng giá (VNĐ)</h6>
                    <form action="products.php" method="GET">
                        <?php if(!empty($search)): ?><input type="hidden" name="q" value="<?= htmlspecialchars($search) ?>"><?php endif; ?>
                        <?php if($cat_id > 0): ?><input type="hidden" name="cat_id" value="<?= $cat_id ?>"><?php endif; ?>
                        <?php if($sort != 'newest'): ?><input type="hidden" name="sort" value="<?= $sort ?>"><?php endif; ?>

                        <div class="d-flex align-items-center mb-2">
                            <input type="number" name="price_min" class="form-control price-input" placeholder="0" value="<?= isset($_GET['price_min']) ? $_GET['price_min'] : '' ?>" min="0">
                            <span class="mx-1">-</span>
                            <input type="number" name="price_max" class="form-control price-input" placeholder="Max" value="<?= isset($_GET['price_max']) ? $_GET['price_max'] : '' ?>" min="0">
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100">Áp dụng</button>
                    </form>
                </div>

                </div>
        </div>

        <div class="col-lg-9">
            
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 bg-white p-3 rounded shadow-sm border">
                <div class="mb-2 mb-md-0">
                    <span class="fw-bold">Tìm thấy <?= $total_products ?> sản phẩm</span>
                    <?php if(!empty($search)): ?>
                        cho từ khóa <span class="text-danger">"<?= htmlspecialchars($search) ?>"</span>
                    <?php endif; ?>
                </div>

                <div class="d-flex align-items-center">
                    <label class="me-2 small text-muted">Sắp xếp:</label>
                    <select class="form-select form-select-sm border-secondary" style="width: 180px;" onchange="window.location.href=this.value">
                        <option value="<?= buildUrl('sort', 'newest') ?>" <?= $sort == 'newest' ? 'selected' : '' ?>>Mới nhất</option>
                        <option value="<?= buildUrl('sort', 'price_asc') ?>" <?= $sort == 'price_asc' ? 'selected' : '' ?>>Giá tăng dần</option>
                        <option value="<?= buildUrl('sort', 'price_desc') ?>" <?= $sort == 'price_desc' ? 'selected' : '' ?>>Giá giảm dần</option>
                        <option value="<?= buildUrl('sort', 'name_asc') ?>" <?= $sort == 'name_asc' ? 'selected' : '' ?>>Tên A-Z</option>
                    </select>
                </div>
            </div>

            <?php if(count($products) > 0): ?>
                <div class="row g-3">
                    <?php foreach($products as $prod): ?>
                    <div class="col-6 col-md-4">
                        <?php include 'product_card_template.php'; ?>
                    </div>
                    <?php endforeach; ?>
                </div>

                <?php if($total_pages > 1): ?>
                <nav class="mt-5">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= buildUrl('page', $page - 1) ?>">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>

                        <?php 
                        // Logic hiển thị số trang thông minh (chỉ hiện 1 vài trang đầu/cuối/giữa)
                        // Để đơn giản, ta hiện tất cả nếu < 10 trang
                        for ($p = 1; $p <= $total_pages; $p++): 
                        ?>
                            <li class="page-item <?= ($p == $page) ? 'active' : '' ?>">
                                <a class="page-link" href="<?= buildUrl('page', $p) ?>"><?= $p ?></a>
                            </li>
                        <?php endfor; ?>

                        <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= buildUrl('page', $page + 1) ?>">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>

            <?php else: ?>
                <div class="text-center py-5 bg-white rounded shadow-sm">
                    <img src="https://cdn-icons-png.flaticon.com/512/4076/4076432.png" alt="No result" style="width: 80px; opacity: 0.5;">
                    <h5 class="mt-3 text-muted">Không tìm thấy sản phẩm nào!</h5>
                    <p class="small text-muted">Hãy thử điều chỉnh bộ lọc hoặc tìm từ khóa khác.</p>
                    <a href="products.php" class="btn btn-outline-primary btn-sm mt-2">Xóa bộ lọc</a>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php include '../app/Views/layouts/footer.php'; ?>