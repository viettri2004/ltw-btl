<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechStore - Công nghệ chính hãng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary fs-3" href="index.php">
                <i class="bi bi-box-seam-fill"></i> TechStore
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link fw-semibold" href="index.php">Trang chủ</a></li>
                    <li class="nav-item"><a class="nav-link fw-semibold" href="products.php">Sản phẩm</a></li>
                    <li class="nav-item"><a class="nav-link fw-semibold" href="#">Giới thiệu</a></li>
                    <li class="nav-item"><a class="nav-link fw-semibold" href="#">Liên hệ</a></li>
                </ul>
                <div class="d-flex align-items-center gap-3">
                    <form class="d-flex" role="search" action="products.php" method="GET">
                        <input class="form-control form-control-sm me-2 bg-light border-0" type="search" name="q" placeholder="Tìm sản phẩm..." aria-label="Search" value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>">
                    </form>

                    
                    <a href="cart.php" class="text-dark fs-5 position-relative me-2">
                        <i class="bi bi-cart3"></i>
                        <span id="cart-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.5em;">
                            <?php 
                                $cart_count = 0;
                                if(isset($_SESSION['cart'])) {
                                    foreach($_SESSION['cart'] as $item) {
                                        $cart_count += $item['quantity'];
                                    }
                                }
                                echo $cart_count;
                            ?>
                        </span>
                    </a>

                    <a href="history.php" class="text-decoration-none text-dark fw-semibold small me-2 hover-underline">
                        <i class="bi bi-clock-history"></i> Lịch sử
                    </a>

                    <?php
                    require_once dirname(__DIR__, 2) . '/Controllers/AuthController.php';
                    $currentUser = AuthController::getCurrentUser();
                    if($currentUser): ?>
                        <div class="dropdown">
                            <a href="#" class="btn btn-outline-dark btn-sm rounded-pill dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> <?= htmlspecialchars($currentUser['full_name']) ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="profile.php">Tài khoản</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="logout.php">Đăng xuất</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-dark btn-sm rounded-pill px-3">Đăng nhập</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    <main class="flex-grow-1">