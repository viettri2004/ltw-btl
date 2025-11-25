<?php
session_start();
require_once '../config/database.php';

// --- 1. KHỞI TẠO THAM SỐ TÌM KIẾM ---
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$cat_id = isset($_GET['cat_id']) ? (int)$_GET['cat_id'] : 0;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// --- 2. XÂY DỰNG CÂU TRUY VẤN SQL ---
$where = "WHERE 1=1";
$params = [];

// Lọc theo từ khóa
if (!empty($search)) {
    $where .= " AND name LIKE :search";
    $params[':search'] = "%$search%";
}

// Lọc theo danh mục
if ($cat_id > 0) {
    $where .= " AND category_id = :cat_id";
    $params[':cat_id'] = $cat_id;
}

// Xử lý Sắp xếp (Sort)
$orderBy = "ORDER BY created_at DESC"; // Mặc định: Mới nhất
switch ($sort) {
    case 'price_asc':
        $orderBy = "ORDER BY price ASC";
        break;
    case 'price_desc':
        $orderBy = "ORDER BY price DESC";
        break;
    default:
        $orderBy = "ORDER BY created_at DESC";
        break;
}

try {
    // A. Lấy danh sách sản phẩm
    $sql = "SELECT * FROM products $where $orderBy";
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll();

    // B. Lấy danh sách danh mục (cho Sidebar)
    // Kèm theo đếm số lượng sản phẩm trong mỗi danh mục (Optional - nâng cao)
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

// Helper: Giữ lại các tham số URL hiện tại khi bấm link
function buildUrl($key, $value) {
    $params = $_GET;
    $params[$key] = $value;
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
                <div class="filter-header">
                    <i class="bi bi-funnel"></i> Bộ lọc sản phẩm
                </div>

                <div class="mb-4">
                    <h6 class="fw-bold mb-2 text-dark small text-uppercase">Danh mục</h6>
                    <div class="list-group">
                        <a href="<?= buildUrl('cat_id', 0) ?>" 
                           class="list-group-item d-flex justify-content-between align-items-center <?= $cat_id == 0 ? 'active' : '' ?>">
                            Tất cả
                            <i class="bi bi-chevron-right small"></i>
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

                </div>
        </div>

        <div class="col-lg-9">
            
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 bg-white p-3 rounded shadow-sm border">
                <div class="mb-2 mb-md-0">
                    <?php if(!empty($search)): ?>
                        Kết quả tìm kiếm cho: <span class="fw-bold text-danger">"<?= htmlspecialchars($search) ?>"</span>
                    <?php else: ?>
                        <span class="fw-bold">Tất cả sản phẩm</span>
                    <?php endif; ?>
                    <span class="text-muted small ms-2">(<?= count($products) ?> sản phẩm)</span>
                </div>

                <div class="d-flex align-items-center">
                    <label class="me-2 small text-muted">Sắp xếp:</label>
                    <select class="form-select form-select-sm border-secondary" style="width: 180px;" onchange="window.location.href=this.value">
                        <option value="<?= buildUrl('sort', 'newest') ?>" <?= $sort == 'newest' ? 'selected' : '' ?>>Mới nhất</option>
                        <option value="<?= buildUrl('sort', 'price_asc') ?>" <?= $sort == 'price_asc' ? 'selected' : '' ?>>Giá tăng dần</option>
                        <option value="<?= buildUrl('sort', 'price_desc') ?>" <?= $sort == 'price_desc' ? 'selected' : '' ?>>Giá giảm dần</option>
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
            <?php else: ?>
                <div class="text-center py-5 bg-white rounded shadow-sm">
                    <img src="https://cdn-icons-png.flaticon.com/512/4076/4076432.png" alt="No result" style="width: 100px; opacity: 0.5;">
                    <h5 class="mt-3 text-muted">Không tìm thấy sản phẩm nào!</h5>
                    <p class="small text-muted">Vui lòng thử từ khóa khác hoặc xóa bộ lọc.</p>
                    <a href="products.php" class="btn btn-primary btn-sm mt-2">Xóa bộ lọc</a>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php include '../app/Views/layouts/footer.php'; ?>