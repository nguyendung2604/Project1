<?php
// cart/update-cart.php
require_once __DIR__ . '/../config.php';

$id = (int)($_POST['id'] ?? 0);
$quantity = (int)($_POST['quantity'] ?? 1);

if (!$id) {
    header('Location: ' . BASE_URL . '/product/cart.php');
    exit;
}

if (!isset($_SESSION['user_id'])) {
    // Người dùng chưa đăng nhập → cập nhật session cart
    if (isset($_SESSION['cart'][$id])) {
        if ($quantity > 0) {
            $_SESSION['cart'][$id] = $quantity;
        } else {
            unset($_SESSION['cart'][$id]); // Nếu số lượng <= 0 thì xoá
        }

        $_SESSION['cart_count'] = array_sum($_SESSION['cart']);
    }
} else {
    // Người dùng đã đăng nhập → cập nhật database
    $user_id = $_SESSION['user_id'];

    // Tìm cart_id của user
    $stmt = $pdo->prepare("SELECT cart_id FROM carts WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $cart = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cart) {
        $cart_id = $cart['cart_id'];

        if ($quantity > 0) {
            // Kiểm tra xem item đã tồn tại chưa
            $stmt = $pdo->prepare("SELECT * FROM cart_items WHERE cart_id = ? AND product_id = ?");
            $stmt->execute([$cart_id, $id]);
            $item = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($item) {
                // Cập nhật số lượng
                $stmt = $pdo->prepare("UPDATE cart_items SET quantity = ? WHERE cart_id = ? AND product_id = ?");
                $stmt->execute([$quantity, $cart_id, $id]);
            }
        } else {
            // Số lượng <= 0 thì xóa luôn item đó
            $stmt = $pdo->prepare("DELETE FROM cart_items WHERE cart_id = ? AND product_id = ?");
            $stmt->execute([$cart_id, $id]);
        }

        // Cập nhật lại tổng giá cart
        $stmt = $pdo->prepare("SELECT SUM(quantity * price) as total_price FROM cart_items WHERE cart_id = ?");
        $stmt->execute([$cart_id]);
        $total_price = (int)($stmt->fetchColumn() ?? 0);

        $stmt = $pdo->prepare("UPDATE carts SET total_price = ? WHERE cart_id = ?");
        $stmt->execute([$total_price, $cart_id]);
    }
}

// Quay về lại trang giỏ hàng
header("Location: " . BASE_URL . "/product/cart.php");
exit;

?>