<?php
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id'])) {
    // ❌ Chưa đăng nhập: xóa SESSION giỏ hàng
    unset($_SESSION['cart']);
    $_SESSION['cart_count'] = 0;
} else {
    // ✅ Đã đăng nhập: xóa giỏ hàng trong database
    $user_id = $_SESSION['user_id'];

    // Lấy cart_id
    $stmt = $pdo->prepare("SELECT cart_id FROM carts WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $cart = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cart) {
        $cart_id = $cart['cart_id'];

        // Xóa tất cả sản phẩm trong cart_items
        $stmt = $pdo->prepare("DELETE FROM cart_items WHERE cart_id = ?");
        $stmt->execute([$cart_id]);

        // Cập nhật lại tổng tiền về 0
        $stmt = $pdo->prepare("UPDATE carts SET total_price = 0 WHERE cart_id = ?");
        $stmt->execute([$cart_id]);
    }
}

// Chuyển hướng về lại trang giỏ hàng
$redirect_url = BASE_URL . '/product/cart.php';
header("Location: $redirect_url");
exit;

?>