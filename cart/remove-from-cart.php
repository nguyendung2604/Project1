<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    
    if (!$id) {
        header("Location: " . BASE_URL . "/product/cart.php");
        exit;
    }

    if (!isset($_SESSION['user_id'])) {
        // ❌ Chưa đăng nhập → xóa khỏi SESSION
        if (isset($_SESSION['cart'][$id])) {
            unset($_SESSION['cart'][$id]);
        }
        $_SESSION['cart_count'] = array_sum($_SESSION['cart'] ?? []);
    } else {
        // ✅ Đã đăng nhập → xóa khỏi database
        $user_id = $_SESSION['user_id'];

        // Lấy cart_id
        $stmt = $pdo->prepare("SELECT cart_id FROM carts WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $cart = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cart) {
            $cart_id = $cart['cart_id'];

            // Xóa sản phẩm trong cart_items
            $stmt = $pdo->prepare("DELETE FROM cart_items WHERE cart_id = ? AND product_id = ?");
            $stmt->execute([$cart_id, $id]);

            // Cập nhật lại tổng tiền giỏ hàng
            $stmt = $pdo->prepare("SELECT SUM(quantity * price) as total_price FROM cart_items WHERE cart_id = ?");
            $stmt->execute([$cart_id]);
            $total_price = (int)($stmt->fetchColumn() ?? 0);

            $stmt = $pdo->prepare("UPDATE carts SET total_price = ? WHERE cart_id = ?");
            $stmt->execute([$total_price, $cart_id]);
        }
    }

    // Chuyển hướng về lại trang giỏ hàng
    header("Location: " . BASE_URL . "/product/cart.php");
    exit;
}


?>