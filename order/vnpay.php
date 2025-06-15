<?php
require_once __DIR__ . '/../config.php';
require_once 'phpqrcode/qrlib.php';

// Lấy dữ liệu từ URL
$total_price = $_SESSION['order_total_price'];
$total_price = floatval($total_price);

// Thông tin ngân hàng
$bank_account = '123456789';
$bank_code = 'VCB';
$account_name = 'CONG TY ABC';
$transfer_content = 'Thanh toan don hang #' . date('YmdHis');

// Dữ liệu QR đơn giản mô phỏng theo VietQR
$qr_text = "Bank: $bank_code\nAccount: $bank_account\nName: $account_name\nAmount: $total_price\nContent: $transfer_content";

// Tạo QR và lưu tạm vào file
$temp_qr_file = 'qrcode.png';
QRcode::png($qr_text, $temp_qr_file, QR_ECLEVEL_H, 6);

require_once BASE_PATH . '/includes/header.php';
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
                <a href="<?php echo BASE_URL; ?>/order/order-history.php" class="text-muted text-decoration-none">Quản lý lịch sử mua hàng</a>
            </li>
            <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">
                Thanh toán đơn hàng bằng VNPay
            </li>
        </ol>
    </div>
</nav>
<section class="container mb-4">
    <div class="row justify-content-center">
      <h3>Quét mã QR để chuyển khoản</h3>
      <div class="text-center">
        <!-- <img src="<?= $temp_qr_file ?>" alt="QR Code" /> -->
        <img src="https://kalite.vn/wp-content/uploads/2021/09/maqrkalite.jpg" style="height: 300px;" alt="QR Code" />
      </div>
      <div class="mt-4">
        <ul class="list-group">
          <li class="list-group-item"><strong>Ngân hàng:</strong> <?= htmlspecialchars($bank_code) ?></li>
          <li class="list-group-item"><strong>Số tài khoản:</strong> <?= htmlspecialchars($bank_account) ?></li>
          <li class="list-group-item"><strong>Chủ tài khoản:</strong> <?= htmlspecialchars($account_name) ?></li>
          <li class="list-group-item"><strong>Số tiền:</strong> <?= number_format($total_price, 0, ',', '.') ?> đ</li>
          <li class="list-group-item"><strong>Nội dung chuyển khoản:</strong> <?= htmlspecialchars($transfer_content) ?></li>
        </ul>
      </div>
    </div>
</section>


<?php require_once BASE_PATH . '/includes/footer.php'; ?>