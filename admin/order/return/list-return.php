<?php
require_once __DIR__ . '/../../../config.php';
require_once BASE_PATH . '/admin/partials/header.php';
require_once BASE_PATH . '/admin/partials/sidebar.php';

$stmt = $pdo->query("
    SELECT r.*, o.total_price, c.name AS customer_name
    FROM return_requests r
    JOIN orders o ON r.order_id = o.order_id
    LEFT JOIN customers c ON o.customer_id = c.customer_id
    ORDER BY r.created_at DESC
");
$requests = $stmt->fetchAll();
?>

<div class="container-fluid">
    <div class="row p-2">
        <h3>Danh sách yêu cầu hoàn trả</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Khách hàng</th>
                    <th>Đơn hàng</th>
                    <th>Lý do</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $index => $r): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($r['customer_name']) ?: '<i>Khách</i>' ?></td>
                        <td>#<?= $r['order_id'] ?> - $<?= number_format($r['total_price']) ?></td>
                        <td><?= nl2br(htmlspecialchars($r['reason'])) ?></td>
                        <td>
                            <span class="badge bg-<?= match($r['status']) {
                                'pending' => 'warning',
                                'approved' => 'success',
                                'rejected' => 'danger',
                            } ?>">
                                <?= ucfirst($r['status']) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($r['status'] === 'pending'): ?>
                                <a href="update.php?id=<?= $r['return_id'] ?>&action=approve" class="btn btn-sm btn-success">Duyệt</a>
                                <a href="update.php?id=<?= $r['return_id'] ?>&action=reject" class="btn btn-sm btn-danger">Từ chối</a>
                            <?php else: ?>
                                <em>Đã xử lý</em>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once BASE_PATH . '/admin/partials/footer.php'; ?>
