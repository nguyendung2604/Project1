<?php
// Lấy 8 sản phẩm mới nhất cùng ảnh đại diện
$sql = "SELECT p.product_id, p.name, p.price, pi.image_url FROM products p
        LEFT JOIN product_images pi ON p.product_id = pi.product_id
        GROUP BY p.product_id
        ORDER BY p.created_at DESC LIMIT 8";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<section id="featured-products" class="py-5 bg-light">
    <div class="container">
        <h2 class="h2 fw-bold text-center mb-5">Featured Products</h2>
        <div class="row g-4">
            <?php foreach ($products as $product): ?>
            <div class="col-md-3 d-flex">
                <div class="card shadow-sm w-100 d-flex flex-column">
                    
                    <!-- Ảnh có hiệu ứng zoom -->
                    <div class="card-img-wrapper">
                        <img src="<?= htmlspecialchars($product['image_url'] ?? 'images/product.jpg') ?>"
                             class="card-img-top img-fluid"
                             alt="<?= htmlspecialchars($product['name']) ?>"
                             style="height: 200px; object-fit: cover;">
                    </div>

                    <div class="card-body text-center d-flex flex-column">
                        <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                        <p class="card-text text-primary fw-bold mb-2">$<?= number_format($product['price']) ?></p>
                        <a href="<?php echo BASE_URL; ?>/product/details.php?id=<?= $product['product_id'] ?>"
                           class="btn btn-outline-primary btn-sm mt-auto">View Details</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>