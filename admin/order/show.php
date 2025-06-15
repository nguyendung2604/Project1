<?php
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/admin/partials/header.php';
require_once BASE_PATH . '/admin/partials/sidebar.php';

$orderId = $_GET['id'] ?? null;

if (!$orderId || !is_numeric($orderId)) {
    echo "<div class='alert alert-danger'>Đơn hàng không hợp lệ.</div>";
    exit;
}

// Lấy thông tin đơn hàng
$stmt = $pdo->prepare("
    SELECT o.*, u.fullname AS user_name, c.name AS customer_name, c.phone, c.address
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.user_id
    LEFT JOIN customers c ON o.customer_id = c.customer_id
    WHERE o.order_id = ?
");
$stmt->execute([$orderId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo "<div class='alert alert-warning'>Không tìm thấy đơn hàng.</div>";
    exit;
}

// Lấy danh sách sản phẩm trong đơn hàng
$stmt = $pdo->prepare("
    SELECT p.name, oi.quantity, oi.price
    FROM order_items oi
    JOIN products p ON oi.product_id = p.product_id
    WHERE oi.order_id = ?
");
$stmt->execute([$orderId]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid">
    <h3>Chi tiết đơn hàng #<?= $order['order_id'] ?></h3>
    <div class="mb-4">
        <p><strong>Khách hàng:</strong> <?= htmlspecialchars($order['customer_name']) ?: '<i>Không có</i>' ?></p>
        <p><strong>Người dùng:</strong> <?= htmlspecialchars($order['user_name']) ?: '<i>Khách</i>' ?></p>
        <p><strong>Điện thoại:</strong> <?= htmlspecialchars($order['phone']) ?></p>
        <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($order['address']) ?></p>
        <p><strong>Thời gian đặt:</strong> <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
        <p><strong>Trạng thái hiện tại:</strong>
        <span class="badge bg-<?= match ($order['status']) {
            'pending' => 'secondary',
            'processing' => 'info',
            'delivering' => 'primary',
            'completed' => 'success',
            'cancelled' => 'danger',
            default => 'dark'
        } ?>">
            <?= ucfirst($order['status']) ?>
        </span>
    </p>
    <?php if ($order['status'] !== 'cancelled'): ?>
        <form method="POST" action="update-status.php" class="d-flex align-items-center gap-2">
            <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
            <label for="status" class="form-label mb-0">Cập nhật trạng thái:</label>
            <select name="status" class="form-select w-auto">
                <?php
                $statuses = ['pending', 'processing', 'delivering', 'completed', 'cancelled'];
                foreach ($statuses as $s):
                ?>
                    <option value="<?= $s ?>" <?= $order['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-primary">Cập nhật</button>
        </form>
    <?php endif; ?>
    </div>

    <h5>Sản phẩm trong đơn hàng</h5>
    <div class="table-responsive">
        <table class="table table-bordered text-center align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Sản phẩm</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Tạm tính</th>
                </tr>
            </thead>
            <tbody>
                <?php $total = 0; ?>
                <?php foreach ($items as $index => $item): ?>
                    <?php $subtotal = $item['price'] * $item['quantity']; ?>
                    <?php $total += $subtotal; ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td>$<?= number_format($item['price']) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td class="text-danger fw-bold">$<?= number_format($subtotal) ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr class="table-dark">
                    <td colspan="4"><strong>Tổng cộng</strong></td>
                    <td class="text-danger fw-bold">$<?= number_format($total) ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <h4>Phương thức thanh toán</h4>
    <div class="mb-3">
        <form method="POST" action="payment.php">
        <input type="hidden" name="total_price" value="<?= $total ?>">
        <select name="payment_method" class="form-select" required>
            <option value="cod" <?= ($_POST['payment_method'] ?? '') === 'cod' ? 'selected' : '' ?>>Thanh toán khi nhận hàng (COD)</option>
            <option value="bank_transfer" <?= ($_POST['payment_method'] ?? '') === 'bank_transfer' ? 'selected' : '' ?>>Chuyển khoản ngân hàng</option>
            <option value="card_expiry" <?= ($_POST['payment_method'] ?? '') === 'card_expiry' ? 'selected' : '' ?>>Thẻ tín dụng/ghi nợ</option>
            <option value="vnpay" <?= ($_POST['payment_method'] ?? '') === 'vnpay' ? 'selected' : '' ?>>Thanh toán VNPAY</option>
        </select>
    </div>

    <a href="<?= BASE_URL ?>/admin/order/orders.php" class="btn btn-secondary">← Quay lại danh sách đơn</a>
    <button type="submit" class="btn btn-primary">Thanh toán</button>
    </form>
</div>

<?php require_once BASE_PATH . '/admin/partials/footer.php'; ?>
