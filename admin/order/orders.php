<?php
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/admin/partials/header.php';
require_once BASE_PATH . '/admin/partials/sidebar.php';

$conditions = [];
$params = [];

if (!empty($_GET['customer_name'])) {
    $conditions[] = "c.name LIKE ?";
    $params[] = '%' . $_GET['customer_name'] . '%';
}

if (!empty($_GET['status'])) {
    $conditions[] = "o.status = ?";
    $params[] = $_GET['status'];
}

if (!empty($_GET['date'])) {
    $conditions[] = "DATE(o.created_at) = ?";
    $params[] = $_GET['date'];
}

$where = '';
if (!empty($conditions)) {
    $where = 'WHERE ' . implode(' AND ', $conditions);
}

$sql = "
    SELECT 
        o.order_id,
        o.total_price,
        o.status,
        o.created_at,
        u.fullname AS user_name,
        c.name AS customer_name,
        c.phone AS customer_phone
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.user_id
    LEFT JOIN customers c ON o.customer_id = c.customer_id
    $where
    ORDER BY o.created_at DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="container-fluid">
    <div class="row">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Tất cả đơn hàng</h2>
            <a href="create.php" class="btn btn-success">+ Tạo đơn hàng mới</a>
        </div>
        <div class="table-responsive">
            <form method="GET" class="row g-2 mb-3">
                <div class="col-md-3">
                    <input type="text" name="customer_name" value="<?= htmlspecialchars($_GET['customer_name'] ?? '') ?>" class="form-control" placeholder="Tìm theo tên khách hàng">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">-- Tất cả trạng thái --</option>
                        <?php
                            $statuses = ['pending' => 'Chờ xử lý', 'processing' => 'Đang xử lý', 'delivering' => 'Đang giao', 'completed' => 'Hoàn tất', 'cancelled' => 'Hủy'];
                            foreach ($statuses as $key => $label):
                                $selected = ($_GET['status'] ?? '') === $key ? 'selected' : '';
                                echo "<option value=\"$key\" $selected>$label</option>";
                            endforeach;
                        ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date" value="<?= htmlspecialchars($_GET['date'] ?? '') ?>" class="form-control">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Lọc</button>
                </div>
                <div class="col-md-2">
                    <a href="?" class="btn btn-outline-secondary w-100">Đặt lại</a>
                </div>
            </form>
            <table class="table table-bordered table-striped align-middle text-center">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Khách hàng</th>
                        <th>Người dùng</th>
                        <th>Điện thoại</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Thời gian</th>
                        <th>Chi tiết</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($orders) > 0): ?>
                        <?php foreach ($orders as $index => $order): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= htmlspecialchars($order['customer_name']) ?: '<i>Không có</i>' ?></td>
                                <td><?= htmlspecialchars($order['user_name']) ?: '<i>Khách</i>' ?></td>
                                <td><?= htmlspecialchars($order['customer_phone']) ?></td>
                                <td class="text-danger fw-bold">$<?= number_format($order['total_price']) ?></td>
                                <td>
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
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                                <td>
                                    <a href="<?= BASE_URL ?>/admin/order/show.php?id=<?= $order['order_id'] ?>" class="btn btn-sm btn-outline-primary">Xem</a>
                                    
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted">Không có đơn hàng nào.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>




<?php require_once BASE_PATH . '/admin/partials/footer.php'; ?>