<?php
require_once __DIR__ . '/../config.php';
require_once BASE_PATH . '/includes/header.php';

// Kiểm tra đăng nhập
if (empty($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

$userId = $_SESSION['user_id'];

// Lấy customer_id từ bảng customers theo user_id
$stmtCust = $pdo->prepare("SELECT customer_id FROM customers WHERE user_id = ? LIMIT 1");
$stmtCust->execute([$userId]);
$customer = $stmtCust->fetch(PDO::FETCH_ASSOC);
if (!$customer) {
    echo '<div class="alert alert-warning">Không tìm thấy thông tin khách hàng.</div>';
    require_once BASE_PATH . '/includes/footer.php';
    exit;
}
$customerId = $customer['customer_id'];

// Lấy danh sách đơn hàng của khách
$stmtOrders = $pdo->prepare(
    "SELECT order_id, total_price, status, created_at
     FROM orders
     WHERE customer_id = :cid
     ORDER BY created_at DESC"
);
$stmtOrders->execute(['cid' => $customerId]);
$orders = $stmtOrders->fetchAll(PDO::FETCH_ASSOC);

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
            <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">
                Lịch sử mua hàng và Thanh toán
            </li>
        </ol>
    </div>
</nav>
<section class="container mb-4">
    <div class="row p-2 bg-white">
        <h2 class="mb-4">Lịch sử mua hàng và Thanh toán</h2>
          <?php if (empty($orders)): ?>
            <div class="alert alert-info">Bạn chưa có đơn hàng nào.</div>
          <?php else: ?>
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Mã đơn</th>
                  <th>Ngày đặt</th>
                  <th>Thành tiền</th>
                  <th>Trạng thái</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($orders as $order): ?>
                  <tr>
                    <td>#<?= htmlspecialchars($order['order_id']) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                    <td><?= number_format($order['total_price']) ?> đ</td>
                    <td>
                      <?php switch ($order['status']) {
                          case 'pending': echo '<span class="badge bg-warning">Chờ xử lý</span>'; break;
                          case 'processing': echo '<span class="badge bg-info">Đang xử lý</span>'; break;
                          case 'delivering': echo '<span class="badge bg-primary">Đang giao hàng</span>'; break;
                          case 'completed': echo '<span class="badge bg-success">Hoàn thành</span>'; break;
                          case 'cancelled': echo '<span class="badge bg-danger">Đã hủy</span>'; break;
                          default: echo '<span class="badge bg-secondary">' . htmlspecialchars($order['status']) . '</span>'; }
                      ?>
                    </td>
                    <td>
                      <div class="d-flex align-items-center gap-2">
                            <a href="<?php echo BASE_URL; ?>/order/order-detail.php?order_id=<?= $order['order_id'] ?>" class="btn btn-sm btn-outline-primary">
                                Xem chi tiết
                            </a>

                            <?php if ($order['status'] === 'pending'): ?>
                                <form method="POST" action="<?= BASE_URL ?>/order/cancelled.php" class="d-flex align-items-center gap-2" onsubmit="return confirm('Bạn chắc chắn muốn hủy đơn hàng này?')">
                                    <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                                    <input type="text" name="reason" class="form-control form-control-sm" placeholder="Lý do hủy..." required style="width: 250px;">
                                    <button type="submit" class="btn btn-sm btn-danger">Hủy đơn</button>
                                </form>
                            <?php endif; ?>

                            <?php if ($order['status'] === 'completed'): ?>
                                <form method="POST" action="<?= BASE_URL ?>/order/create-return.php" class="d-flex align-items-center gap-2" onsubmit="return confirm('Bạn chắc chắn muốn yêu cầu hoàn trả đơn hàng này?')">
                                    <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                                    <input type="text" name="reason" class="form-control form-control-sm" placeholder="Lý do hoàn trả..." required style="width: 250px;">
                                    <button type="submit" class="btn btn-sm btn-danger">Yêu cầu hoàn trả</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php endif; ?>
    </div>
</section>


<?php require_once BASE_PATH . '/includes/footer.php'; ?>