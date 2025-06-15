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
        <form method="post" id="checkout-form" autocomplete="off" onsubmit="return showSuccessAlert();">
            <div class="mb-3 card-info" id="card-info-section">
                <label class="form-label">Card Information</label>
                <input type="text" class="form-control mb-2" name="card_number" placeholder="Card Number">
                <div class="row">
                    <div class="col">
                        <input type="text" class="form-control mb-2" name="card_expiry" placeholder="MM/YY">
                    </div>
                    <div class="col">
                        <input type="text" class="form-control mb-2" name="card_cvc" placeholder="CVC">
                    </div>
                </div>
                <input type="text" class="form-control mb-2" name="card_name" placeholder="Name on Card">
            </div>

            <button type="submit" class="btn btn-success">Xác nhận</button>
        </form>
    </div>
</section>


<?php require_once BASE_PATH . '/includes/footer.php'; ?>
<script>
function showSuccessAlert() {
    alert("Thanh toán thành công! Cảm ơn bạn đã đặt hàng.");
    return false; // Ngăn submit form thực sự nếu không cần gửi dữ liệu
}
</script>