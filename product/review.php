<?php
// /user-management/index.php
require_once __DIR__ . '/../config.php';
require_once BASE_PATH . '/includes/header.php';

// Kiểm tra đăng nhập
if (empty($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

// Lấy product_id và order_id từ URL
$productId = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
$orderId   = isset($_GET['order_id'])   ? (int)$_GET['order_id']   : 0;

// Kiểm tra đơn hàng thuộc về user
$stmt = $pdo->prepare("SELECT oi.*, p.name, pi.image_url, o.customer_id
    FROM order_items oi
    JOIN orders o ON oi.order_id = o.order_id
    JOIN products p ON oi.product_id = p.product_id
    LEFT JOIN product_images pi ON p.product_id = pi.product_id
    WHERE oi.order_id = ? AND oi.product_id = ? AND o.customer_id = (
        SELECT customer_id FROM customers WHERE user_id = ?
    )");
$stmt->execute([$orderId, $productId, $_SESSION['user_id']]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$item) {
    echo '<div class="alert alert-warning">Bạn không có quyền đánh giá sản phẩm này.</div>';
    require_once BASE_PATH . '/includes/footer.php';
    exit;
}

// Xử lý POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = (int)($_POST['rating'] ?? 0);
    $comment = trim($_POST['comment'] ?? '');
    if ($rating < 1 || $rating > 5) {
        $error = 'Vui lòng chọn số sao đánh giá.';
    } elseif (empty($comment)) {
        $error = 'Vui lòng nhập nội dung nhận xét.';
    } else {
        // Lưu review
        $stmtRev = $pdo->prepare("INSERT INTO reviews (order_id, product_id, customer_id, rating, comment, created_at)
            VALUES (:order_id, :product_id, :cust_id, :rating, :comment, NOW())");
        $stmtRev->execute([
            'order_id'   => $orderId,
            'product_id' => $productId,
            'cust_id'    => $item['customer_id'] ?? null,
            'rating'     => $rating,
            'comment'    => $comment
        ]);
        // Redirect về lịch sử hoặc chi tiết
        $redirect_url = BASE_URL . '/order/order-history.php';
        header("Location: $redirect_url");
        exit;
    }
}
// List sao
$list_review = [
    '1' => '&#9733;',
    '2' => '&#9733;',
    '3' => '&#9733;',
    '4' => '&#9733;',
    '5' => '&#9733;'
];
?>
<style>
    .div_breadcrumb{
        margin-top: 80px; 
    }
</style>
<style>
  .star-rating .star {
    font-size: 2rem;
    color: lightgray;      /* làm mờ */
    cursor: pointer;
    transition: color 0.2s;
  }

  .star-rating .star.selected {
    color: gold;           /* in đậm khi chọn */
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
                Đánh giá sản phẩm
            </li>
        </ol>
    </div>
</nav>
<section class="container mb-4">
    <div class="row p-4 bg-white">
        <h2 class="mb-4">Đánh giá sản phẩm</h2>
    <div class="card mb-4">
        <div class="row g-0">
            <div class="col-md-3 text-center p-3">
                <img src="<?= htmlspecialchars($item['image_url']) ?>" class="img-fluid rounded" alt="">
            </div>
            <div class="col-md-9">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($item['name']) ?></h5>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" action="">
        <div class="mb-3">
            <label class="form-label">Đánh giá của bạn</label>
            <input type="hidden" name="rating" id="rating-value" value="<?= htmlspecialchars($_POST['rating'] ?? '') ?>">
            <div class="star-rating">
                <?php 
                $selectedRating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
                foreach ($list_review as $key => $label): 
                ?>
                    <span 
                        data-value="<?= $key ?>" 
                        class="star <?= ($key <= $selectedRating) ? 'selected' : '' ?>">
                        &#9733;
                    </span>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="mb-3">
            <label for="comment" class="form-label">Nhận xét</label>
            <textarea name="comment" id="comment" class="form-control" rows="4"><?= htmlspecialchars($_POST['comment'] ?? '') ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Gửi đánh giá</button>
        <a href="order_history.php" class="btn btn-secondary ms-2">Hủy</a>
    </form>
    </div>
</section>

<?php require_once BASE_PATH . '/includes/footer.php'; ?>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const stars = document.querySelectorAll(".star-rating .star");
    const input = document.getElementById("rating-value");

    function updateStars(rating) {
        stars.forEach(star => {
            const val = parseInt(star.getAttribute("data-value"));
            if (val <= rating) {
                star.classList.add("selected");
            } else {
                star.classList.remove("selected");
            }
        });
    }

    stars.forEach(star => {
        star.addEventListener("click", function () {
            const selectedValue = parseInt(this.getAttribute("data-value"));
            input.value = selectedValue;
            updateStars(selectedValue);
        });
    });

    // Khởi tạo trạng thái khi load lại trang
    const initial = parseInt(input.value);
    if (initial) {
        updateStars(initial);
    }
});
</script>