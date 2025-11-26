<div class="card h-100 product-card border-1 shadow-none bg-white w-100">
    <?php if(!empty($prod['discount'])): ?>
        <span class="badge bg-danger position-absolute top-0 start-0 m-2"><?= $prod['discount'] ?></span>
    <?php endif; ?>
    
    <a href="detail.php?id=<?= $prod['id'] ?>" class="product-img-box" style="height: 180px;">
        <img src="<?= !empty($prod['image']) ? $prod['image'] : 'https://via.placeholder.com/300' ?>" alt="<?= $prod['name'] ?>">
    </a>

    <div class="card-body p-2 d-flex flex-column">
        <h6 class="card-title text-truncate mb-1">
            <a href="detail.php?id=<?= $prod['id'] ?>" class="text-decoration-none text-dark small fw-bold"><?= $prod['name'] ?></a>
        </h6>
        
        <?php if(!empty($prod['chip'])): ?>
        <div class="mb-1 text-muted" style="font-size: 0.7rem;">
            <?= $prod['chip'] . ' • ' . ($prod['ram'] ?? '') ?>
        </div>
        <?php endif; ?>

        <div class="mt-auto">
            <div class="fw-bold text-danger fs-6"><?= number_format($prod['price'], 0, ',', '.') ?>đ</div>
            <?php if(!empty($prod['old_price']) && $prod['old_price'] > $prod['price']): ?>
                <div class="small text-decoration-line-through text-muted" style="font-size: 0.8rem;"><?= number_format($prod['old_price'], 0, ',', '.') ?>đ</div>
            <?php endif; ?>
            
            <div class="d-flex justify-content-between align-items-center mt-2">
                <div class="text-warning small" style="font-size: 0.7rem;">
                    <i class="bi bi-star-fill"></i> <?= $prod['rating'] ?? 5 ?>
                </div>
                
                <button type="button" 
                        class="btn btn-sm btn-light rounded-circle border btn-add-to-cart"
                        data-id="<?= $prod['id'] ?>"
                        data-name="<?= $prod['name'] ?>"
                        data-price="<?= $prod['price'] ?>"
                        data-image="<?= $prod['image'] ?? '' ?>">
                    <i class="bi bi-cart-plus"></i>
                </button>
            </div>
        </div>
    </div>
</div>