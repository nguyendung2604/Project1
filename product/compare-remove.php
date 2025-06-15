<?php
// /user-management/index.php
require_once __DIR__ . '/../config.php';

$product_id = (int) ($_GET['id'] ?? 0);

if (isset($_SESSION['compare'])) {
    $_SESSION['compare'] = array_filter($_SESSION['compare'], function ($id) use ($product_id) {
        return $id != $product_id;
    });
}

$redirect_url = BASE_URL . '/product/compare.php';
header("Location: $redirect_url");
exit;
?>