<?php
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/admin/partials/header.php';
require_once BASE_PATH . '/admin/partials/sidebar.php';

// Xử lý thêm mới phương thức
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy thông tin khách
    $payment_method = $_POST['payment_method'] ?? 'cod';
    $total_price_param = (int) ($_POST['total_price'] ?? 0);

    // Lưu gia tiền vào session
    $_SESSION['order_total_price'] = $total_price_param;
    // Chuyển hướng sang trang thanh toán nếu không phải là cod
    if ($payment_method == 'card_expiry') {
        
        $redirect_url = BASE_URL . '/admin/order/card-expiry.php';
        header("Location: $redirect_url");
        exit;
    } elseif ($payment_method == 'bank_transfer') {
        $redirect_url = BASE_URL . '/admin/order/bank-transfer.php';
        header("Location: $redirect_url");
        exit;
    } elseif ($payment_method == 'vnpay') {
        $redirect_url = BASE_URL . '/admin/order/vnpay.php';
        header("Location: $redirect_url");
        exit;
    }
 }
?>
<div class="container-fluid">
    <div class="row">
        <h3 class="mb-3">Phương thức thanh toán</h3>

     <form method="POST" class="mb-4">
        <h4>Phương thức thanh toán</h4>
        <div class="mb-3">
            <select name="payment_method" class="form-select" required>
                <option value="cod" <?= ($_POST['payment_method'] ?? '') === 'cod' ? 'selected' : '' ?>>Thanh toán khi nhận hàng (COD)</option>
                <option value="bank_transfer" <?= ($_POST['payment_method'] ?? '') === 'bank_transfer' ? 'selected' : '' ?>>Chuyển khoản ngân hàng</option>
                <option value="card_expiry" <?= ($_POST['payment_method'] ?? '') === 'card_expiry' ? 'selected' : '' ?>>Thẻ tín dụng/ghi nợ</option>
                <option value="vnpay" <?= ($_POST['payment_method'] ?? '') === 'vnpay' ? 'selected' : '' ?>>Thanh toán VNPAY</option>
            </select>
        </div>
    </form>
    </div>
</div>


<?php require_once BASE_PATH . '/admin/partials/footer.php'; ?>