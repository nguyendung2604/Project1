<?php
require_once __DIR__ . '/../config.php';
require_once BASE_PATH . '/includes/header.php';

$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
var_dump($_GET);
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
                <a href="<?php echo BASE_URL; ?>/product/cart.php" class="text-muted text-decoration-none">Quản lý giỏ hàng</a>
            </li>
            <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">
                Đặt hàng thành công
            </li>
        </ol>
    </div>
</nav>
<section class="container mb-4">
<div class="row justify-content-center">
      <div class="col-md-8 text-center">

        <!-- Icon checkmark -->
        <svg xmlns="http://www.w3.org/2000/svg" class="mb-4" width="100" height="100" fill="green" class="bi bi-check-circle" viewBox="0 0 16 16">
          <path d="M8 15A7 7 0 1 0 8 .999 7 7 0 0 0 8 15zm0 1A8 8 0 1 1 8 .999a8 8 0 0 1 0 16z"/>
          <path d="M10.97 5.03a.75.75 0 0 1 1.07 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L4.324 9.384a.75.75 0 1 1 1.06-1.06l1.94 1.94 3.646-4.235z"/>
        </svg>

        <h1 class="mb-3">Cảm ơn bạn đã đặt hàng!</h1>
        <?php if ($orderId): ?>
          <p class="lead">Mã đơn hàng của bạn là <strong>#<?= $orderId ?></strong></p>
        <?php endif; ?>

        <div class="alert alert-success mt-4">
          Chúng tôi đã gửi xác nhận đơn hàng vào email của bạn.<br>
          Đơn hàng sẽ được xử lý và giao trong vòng 2–3 ngày làm việc.
        </div>

        <a href="<?php echo BASE_URL; ?>/product/list.php" class="btn btn-primary me-2">Tiếp tục mua sắm</a>

        <?php if (!empty($_SESSION['user_id'])): ?>
            <a href="<?php echo BASE_URL; ?>/order/order-history.php" class="btn btn-outline-secondary">
                Xem đơn hàng của tôi
            </a>
        <?php endif; ?>
      </div>
</section>


<?php require_once BASE_PATH . '/includes/footer.php'; ?>