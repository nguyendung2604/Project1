<?php
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/admin/partials/header.php';
require_once BASE_PATH . '/admin/partials/sidebar.php';


?>

<div class="container-fluid">
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
</div>


<?php require_once BASE_PATH . '/admin/partials/footer.php'; ?>
<script>
function showSuccessAlert() {
    alert("Thanh toán thành công! Cảm ơn bạn đã đặt hàng.");
    return false; // Ngăn submit form thực sự nếu không cần gửi dữ liệu
}
</script>