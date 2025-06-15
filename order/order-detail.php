<?php
require_once __DIR__ . '/../config.php';
require_once BASE_PATH . '/includes/header.php';

// Kiểm tra đăng nhập
if (empty($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

// Lấy order_id từ URL
$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

// Lấy customer_id của user
$stmtCust = $pdo->prepare("SELECT customer_id FROM customers WHERE user_id = ? LIMIT 1");
$stmtCust->execute([$_SESSION['user_id']]);
$customer = $stmtCust->fetch(PDO::FETCH_ASSOC);
if (!$customer) {
    echo '<div class="alert alert-danger">Không tìm thấy thông tin khách hàng.</div>';
    require_once BASE_PATH . '/includes/footer.php';
    exit;
}
$customerId = $customer['customer_id'];

// Lấy thông tin đơn hàng
$stmtOrder = $pdo->prepare(
    "SELECT order_id, total_price, status, created_at
     FROM orders
     WHERE order_id = :oid AND customer_id = :cid
     LIMIT 1"
);
$stmtOrder->execute(['oid' => $orderId, 'cid' => $customerId]);
$order = $stmtOrder->fetch(PDO::FETCH_ASSOC);
if (!$order) {
    echo '<div class="alert alert-warning">Đơn hàng không tồn tại hoặc bạn không có quyền xem.</div>';
    require_once BASE_PATH . '/includes/footer.php';
    exit;
}

// Lấy chi tiết sản phẩm trong đơn
$stmtItems = $pdo->prepare(
    "SELECT oi.product_id, oi.quantity, oi.price, p.name, pi.image_url
     FROM order_items oi
     JOIN products p ON oi.product_id = p.product_id
     LEFT JOIN product_images pi ON p.product_id = pi.product_id
     WHERE oi.order_id = :oid"
);
$stmtItems->execute(['oid' => $orderId]);
$items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
?>
<style>
    .div_breadcrumb{
        margin-top: 80px; 
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
                <a href="<?php echo BASE_URL; ?>/order/order-history.php" class="text-muted text-decoration-none">Lịch sử mua hàng</a>
            </li>
            <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">
                Chi tiết đơn hàng
            </li>
        </ol>
    </div>
</nav>
<section class="container mb-4">
    <div class="row p-2 bg-white">
        <h2 class="mb-3">Chi tiết đơn hàng #<?= htmlspecialchars($order['order_id']) ?></h2>
    <div class="mb-4">
        <p><strong>Ngày đặt:</strong> <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
        <p><strong>Trạng thái:</strong> 
            <?php switch ($order['status']) {
                case 'pending': echo '<span class="badge bg-warning">Chờ xử lý</span>'; break;
                case 'processing': echo '<span class="badge bg-info">Đang xử lý</span>'; break;
                case 'delivering': echo '<span class="badge bg-primary">Đang giao hàng</span>'; break;
                case 'completed': echo '<span class="badge bg-success">Hoàn thành</span>'; break;
                case 'cancelled': echo '<span class="badge bg-danger">Đã hủy</span>'; break;
                default: echo '<span class="badge bg-secondary">'.htmlspecialchars($order['status']).'</span>'; }
            ?>
        </p>
        <p><strong>Tổng tiền:</strong> <?= number_format($order['total_price']) ?> đ</p>
    </div>

    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th>Ảnh</th>
                <th>Sản phẩm</th>
                <th>Đơn giá</th>
                <th>Số lượng</th>
                <th>Thành tiền</th>
                <th>Đánh giá</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><img src="<?= htmlspecialchars($item['image_url']) ?>" width="60" alt=""></td>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td><?= number_format($item['price']) ?> đ</td>
                    <td><?= $item['quantity'] ?></td>
                    <td><?= number_format($item['price'] * $item['quantity']) ?> đ</td>
                    <td>
                        <?php if ($order['status'] === 'completed'): ?>
                            <a href="<?php echo BASE_URL; ?>/product/review.php?product_id=<?= $item['product_id'] ?>&order_id=<?= $order['order_id'] ?>"
                               class="btn btn-sm btn-warning">
                                Đánh giá
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div>
        <a href="<?php echo BASE_URL; ?>/order/order-history.php" class="btn btn-secondary mt-3">← Quay lại lịch sử mua hàng</a>
    </div>
    </div>
</section>


<?php require_once BASE_PATH . '/includes/footer.php'; ?>