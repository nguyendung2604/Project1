<?php
// /user-management/index.php
require_once __DIR__ . '/../config.php';
require_once BASE_PATH . '/includes/header.php';

// Kiểm tra đăng nhập
if (empty($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Lấy danh sách sản phẩm yêu thích
$stmt = $pdo->prepare("
    SELECT 
        p.*, 
        (SELECT pi.image_url FROM product_images pi WHERE pi.product_id = p.product_id LIMIT 1) as image_url
    FROM favorites f
    JOIN products p ON f.product_id = p.product_id
    WHERE f.user_id = ?
    ORDER BY f.created_at DESC
");
$stmt->execute([$user_id]);
$favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<style>
    .div_breadcrumb{
        margin-top: 80px; 
    }
    .card img {
        transition: transform 0.3s ease;
        height: 160px;
        object-fit: contain;
        margin-top: 10px;
    }
    .card:hover img {
        transform: scale(1.05);
    }
    .card {
        border: 1px solid #EEEEEE;
    }
</style>
<!-- Breadcrumb -->
<nav class="container mt-3 " aria-label="breadcrumb">
    <div class="row div_breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?php echo BASE_URL; ?>/index.php" class="text-muted text-decoration-none">Home</a>
            </li>
            <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">
                Sản phẩm yêu thích
            </li>
        </ol>
    </div>
</nav>
<section class="container mb-4">
    <div class="row p-3 bg-white">
        <h2 class="mb-2">Danh sách sản phẩm yêu thích</h2>

        <?php if (empty($favorites)): ?>
            <div class="alert alert-info">Bạn chưa có sản phẩm yêu thích nào.</div>
        <?php else: ?>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-0">
                <?php foreach ($favorites as $product): ?>
                    <div class="col text-center">
                        <div class="card h-100">
                            <img src="<?= htmlspecialchars($product['image_url'] ?? 'default.jpg') ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                                <p class="card-text"><?= number_format($product['price'], 0, ',', '.') ?>₫</p>
                                <form action="<?= BASE_URL ?>/product/add-to-cart.php" 
                                      method="POST" 
                                      class="d-flex justify-content-center align-items-center gap-2 mt-2">
                                    <input type="hidden" name="action" value="favorites">
                                    <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                                    <input type="number" name="quantity" value="1" min="1" max="<?= $product['quantity'] ?>" 
                                           class="form-control form-control-sm" style="width: 70px;">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-cart-plus"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once BASE_PATH . '/includes/footer.php'; ?>