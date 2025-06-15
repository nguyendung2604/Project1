<?php
// /user-management/index.php
require_once __DIR__ . '/../config.php';
require_once BASE_PATH . '/includes/header.php';

try {
    // Lấy ID sản phẩm từ URL
    $product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($product_id <= 0) {
        throw new Exception("Invalid product ID.");
    }

    // Truy vấn chi tiết sản phẩm
    $stmt = $pdo->prepare("
        SELECT 
            p.product_id, 
            p.name, 
            p.description, 
            p.price, 
            p.old_price, 
            p.quantity, 
            b.name AS brand_name, 
            c.name AS category_name, 
            pi.image_url
        FROM products p
        LEFT JOIN brands b ON p.brand_id = b.brand_id
        LEFT JOIN categories c ON p.category_id = c.category_id
        LEFT JOIN product_images pi ON p.product_id = pi.product_id
        WHERE p.product_id = ?
    ");

    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        throw new Exception("Product not found.");
    }
} catch (Exception $e) {
    echo "Lỗi: " . $e->getMessage();
    exit;
}

// Lấy đánh giá
$reviewStmt = $pdo->prepare("
    SELECT r.rating, r.comment, r.created_at, c.name AS customer_name
    FROM reviews r
    JOIN customers c ON r.customer_id = c.customer_id
    WHERE r.product_id = ?
    ORDER BY r.created_at DESC
");
$reviewStmt->execute([$product_id]);
$reviews = $reviewStmt->fetchAll(PDO::FETCH_ASSOC);

// Thống kê đánh giá
$summaryStmt = $pdo->prepare("
    SELECT rating, COUNT(*) as count 
    FROM reviews 
    WHERE product_id = ? AND rating IS NOT NULL
    GROUP BY rating
");
$summaryStmt->execute([$product_id]);
$summaryData = $summaryStmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Tổng số lượt
$totalRatings = array_sum($summaryData);
$averageRating = $totalRatings ? round(array_sum(array_map(fn($r, $c) => $r * $c, array_keys($summaryData), $summaryData)) / $totalRatings, 1) : 0;

?>
<style>
    .div_breadcrumb{
        margin-top: 80px; 
    }
</style>
<style>
    .comment-box {
      margin: 30px auto;
      width: 100%;
    }

    .form-control {
      border-radius: 10px;
    }

    .btn-send {
        white-space: nowrap;
      border-radius: 999px;
      background-color: #000;
      color: #fff;
    }

    .btn-send:hover {
      opacity: 0.9;
    }

    .char-counter {
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      font-size: 13px;
      color: #888;
    }
  </style>
<!-- Breadcrumb -->
<nav class="container mt-3 " aria-label="breadcrumb">
    <div class="row div_breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?php echo BASE_URL; ?>/index.php" class="text-muted text-decoration-none">Home</a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?php echo BASE_URL; ?>/product/category.php" class="text-muted text-decoration-none"><?php echo htmlspecialchars($product['category_name']); ?></a>
            </li>
            <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">
                <?php echo htmlspecialchars($product['name']); ?>
            </li>
        </ol>
    </div>
</nav>
<section class="container mb-4">
    <div class="row p-4 bg-white">
        <!-- Cột trái: Hình ảnh -->
        <div class="col-md-5 text-center">
            <?php if ($product['image_url']): ?>
                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                 alt="<?php echo htmlspecialchars($product['name']); ?>" 
                 class="img-fluid object-fit-contain"
                 style="max-height: 400px; width: 100%; object-fit: contain;">
            <?php else: ?>
                <img src="default-image.jpg" alt="No image available" class="img-fluid rounded">
            <?php endif; ?>
        </div>

        <!-- Cột phải: Thông tin sản phẩm -->
        <div class="col-md-7">
            <h4 class="fw-bold"><?php echo htmlspecialchars($product['name']); ?></h4>
            <p><strong>Brand:</strong> <?php echo htmlspecialchars($product['brand_name']); ?></p>
            <p class="text-muted"><?php echo htmlspecialchars($product['description']); ?></p>

            <h5 class="text-danger fw-bold fs-4">
                $<?php echo number_format($product['price']); ?>
            </h5>

            <?php if (!empty($product['old_price']) && $product['old_price'] > $product['price']): ?>
                <p class="text-muted">
                    <del>$<?php echo number_format($product['old_price']); ?></del>
                    <span class="badge bg-danger">
                        -<?= round(100 * ($product['old_price'] - $product['price']) / $product['old_price']) ?>%
                        (Reduce $<?= number_format($product['old_price'] - $product['price']) ?>)
                    </span>
                </p>
            <?php endif; ?>

            <p class="text-muted"><em>(Product price includes VAT)</em></p>

            <p class="fw-bold text-<?php echo ($product['quantity'] > 0) ? 'success' : 'danger'; ?>">
                <?php echo ($product['quantity'] > 0) ? 'In stock: ' . $product['quantity'] : 'Out of stock'; ?>
            </p>

            <!-- Form thêm vào giỏ hàng -->
            <?php if ($product['quantity'] > 0): ?>
                <div class="d-flex align-items-center gap-2">
                    <!-- Add to Cart Form -->
                    <form action="<?= BASE_URL ?>/product/add-to-cart.php" method="POST" class="d-flex align-items-center">
                        <input type="hidden" name="product_id" value="<?= $product['product_id']; ?>">
                        <input type="number" name="quantity" value="1" min="1" max="<?= $product['quantity']; ?>" class="form-control me-2" style="width: 80px;">
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="fa fa-shopping-cart"></i> Add to cart
                        </button>
                    </form>

                    <!-- Add to Favorite Form -->
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <form action="<?= BASE_URL ?>/product/add-to-favorite.php" method="POST" class="d-flex">
                            <input type="hidden" name="product_id" value="<?= $product['product_id']; ?>">
                            <button type="submit" title="Thêm vào danh sách yêu thích" class="btn btn-warning">
                                ❤️
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
                <!-- Mua ngay -->
                <div class="mt-3 text-center">
                    <a href="<?= BASE_URL ?>/checkout.php?buy_now=<?= $product['product_id'] ?>" class="btn btn-danger fw-bold">
                        BUY NOW
                    </a>
                    <p class="text-center mt-2 text-muted" style="font-size: 14px;">
                        Payment by cash or bank transfer
                    </p>
                </div>
            <?php else: ?>
                <p class="text-muted">Không thể mua sản phẩm này.</p>
            <?php endif; ?>

            <a href="<?php echo BASE_URL; ?>/product/list.php" class="btn btn-link mt-3">← Back to list</a>
        </div>
    </div>

    <div class="row bg-white mt-3 p-3">
        <h4>Đánh giá và bình luận</h4>
        <div class="col-md-2 mb-3 text-center">
            <span style="font-size: 40px; font-weight: bold;"><?php echo $averageRating; ?></span> / 5.0
            <div class="text-muted">(<?php echo $totalRatings; ?> lượt đánh giá)</div>
            <div class="mt-3">
                <a href="<?php echo BASE_URL; ?>/order/order-history.php" class="btn btn-danger">Đánh giá sản phẩm</a>
            </div>
        </div>

        <!-- Biểu đồ đánh giá -->
        <div class="col-md-10 p-3">
            <?php for ($i = 5; $i >= 1; $i--): ?>
                <div class="d-flex align-items-center mb-1">
                    <div style="width: 40px;"><?php echo $i; ?> ★</div>
                    <div class="progress flex-fill me-2" style="height: 8px;">
                        <div class="progress-bar bg-danger" role="progressbar" 
                            style="width: <?php echo isset($summaryData[$i]) ? ($summaryData[$i] / $totalRatings * 100) : 0; ?>%">
                        </div>
                    </div>
                    <div><?php echo $summaryData[$i] ?? 0; ?></div>
                </div>
            <?php endfor; ?>
        </div>

        <!-- Danh sách bình luận -->
        <h5><?php echo count($reviews); ?> Bình luận</h5>
        <form id="reviewForm" action="<?= BASE_URL ?>/product/submit-review.php" method="POST">
            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
            <div class="comment-box d-flex align-items-center gap-2">
                <div class="flex-grow-1 position-relative">
                    <input type="text" class="form-control pe-5" name="comment" id="commentInput" placeholder="Nhập nội dung bình luận..." maxlength="3000" oninput="updateCounter()">
                    <span class="char-counter position-absolute top-50 end-0 translate-middle-y pe-2 text-muted" id="charCount">0/3000</span>
                </div>
                <button type="submit" class="btn btn-send px-3">Gửi bình luận</button>
            </div>
        </form>
        <?php if (!empty($reviews)): ?>
            <?php foreach ($reviews as $review): ?>
                <div class="border-bottom py-2 d-flex gap-3 align-items-start">
                    <!-- Avatar -->
                    <img src="https://static.vecteezy.com/system/resources/previews/048/926/084/non_2x/silver-membership-icon-default-avatar-profile-icon-membership-icon-social-media-user-image-illustration-vector.jpg" 
                         alt="Avatar" 
                         class="rounded-circle" 
                         width="48" height="48" 
                         style="object-fit: cover;">
                    <!-- Nội dung -->
                    <div>
                        <strong><?php echo htmlspecialchars($review['customer_name']); ?></strong> 
                        <span class="text-warning">
                            <?php echo str_repeat("★", $review['rating']) . str_repeat("☆", 5 - $review['rating']); ?>
                        </span>
                        <div class="text-muted" style="font-size: 13px;">
                            <?php echo date('d/m/Y', strtotime($review['created_at'])); ?>
                        </div>
                        <p><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-muted fst-italic">Chưa có bình luận nào cho sản phẩm này.</p>
        <?php endif; ?>
    </div>
</section>
<?php require_once BASE_PATH . '/includes/footer.php'; ?>
<script>
  function updateCounter() {
    const input = document.getElementById("commentInput");
    const count = input.value.length;
    document.getElementById("charCount").innerText = `${count}/3000`;
  }
</script>