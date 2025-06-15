<?php
// Khai báo biến $current_page để xác định trang hiện tại
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav id="sidebar" class="d-flex flex-column">
    <div class="sidebar-header">
        <h3>Admin Dashboard</h3>
    </div>
    <ul class="nav flex-column sidebar-menu flex-grow-1">
        <li class="nav-item">
            <a class="nav-link <?= ($current_page == 'index.php') ? 'active' : '' ?>" href="<?php echo BASE_URL; ?>/admin/index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= ($current_page == 'manage.php') ? 'active' : '' ?>" href="<?php echo BASE_URL; ?>/admin/user/manage.php"><i class="fas fa-users"></i> Người dùng</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= ($current_page == 'users.php') ? 'active' : '' ?>" href="#"><i class="fas fa-box"></i> Sản phẩm</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= ($current_page == 'orders.php') ? 'active' : '' ?>" href="<?php echo BASE_URL; ?>/admin/order/orders.php"><i class="fas fa-file-alt"></i> Đơn hàng</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= ($current_page == 'payment-methods.php') ? 'active' : '' ?>" href="<?php echo BASE_URL; ?>/admin/order/payment-methods.php"><i class="fas fa-file-alt"></i> Phương thức thanh toán</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= ($current_page == 'list-return.php') ? 'active' : '' ?>" href="<?php echo BASE_URL; ?>/admin/order/return/list-return.php"><i class="fas fa-file-alt"></i> Yêu cầu hoàn trả</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= ($current_page == 'users.php') ? 'active' : '' ?>" href="#"><i class="fas fa-cog"></i> Cài đặt</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<?php echo BASE_URL; ?>/auth/logout.php"><i class="fas fa-sign-out-alt"></i> Log out</a>
        </li>
    </ul>
</nav>