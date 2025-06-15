<?php
// /user-management/index.php
require_once __DIR__ . '/../config.php';

$product_id = (int) ($_GET['id'] ?? 0);

if (!isset($_SESSION['compare'])) {
    $_SESSION['compare'] = [];
}

// Nếu sản phẩm đã có trong danh sách thì không thêm lại
if (in_array($product_id, $_SESSION['compare'])) {
    $redirect_url = BASE_URL . '/product/compare.php';

    header("Location: $redirect_url");
    exit;
}

// Giới hạn tối đa 4 sản phẩm
if (count($_SESSION['compare']) >= 4) {
    $_SESSION['compare_message'] = "Chỉ có thể so sánh tối đa 4 sản phẩm.";
    $redirect_url = BASE_URL . '/product/compare.php';

    header("Location: $redirect_url");
    exit;
}

$_SESSION['compare'][] = $product_id;

$redirect_url = BASE_URL . '/product/compare.php';

header("Location: $redirect_url");
exit;
?>