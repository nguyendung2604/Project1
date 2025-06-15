<?php
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
    $_SESSION['flash'] = 'Không có sản phẩm trong session để đồng bộ.';
    header('Location: ' . BASE_URL . '/user-management/index.php');
    exit;
}

// Kiểm tra giỏ hàng của user đã có chưa
$stmt = $pdo->prepare("SELECT cart_id FROM carts WHERE user_id = ?");
$stmt->execute([$user_id]);
$cart_row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($cart_row) {
    $cart_id = $cart_row['cart_id'];

    // Xóa cart_items cũ trước khi thêm mới
    $pdo->prepare("DELETE FROM cart_items WHERE cart_id = ?")->execute([$cart_id]);
} else {
    // Chưa có giỏ hàng → tạo mới
    $stmt = $pdo->prepare("INSERT INTO carts (user_id, total_price) VALUES (?, 0)");
    $stmt->execute([$user_id]);
    $cart_id = $pdo->lastInsertId();
}

// Duyệt từng sản phẩm trong session để lưu vào cart_items
$total_price = 0;

foreach ($cart as $product_id => $quantity) {
    // Lấy giá sản phẩm
    $stmt = $pdo->prepare("SELECT price FROM products WHERE product_id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$product) continue;

    $price = (int)$product['price'];
    $subtotal = $price * (int)$quantity;
    $total_price += $subtotal;

    // Thêm vào cart_items
    $stmt = $pdo->prepare("INSERT INTO cart_items (cart_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    $stmt->execute([$cart_id, $product_id, $quantity, $price]);
}

// Cập nhật tổng giá
$stmt = $pdo->prepare("UPDATE carts SET total_price = ? WHERE cart_id = ?");
$stmt->execute([$total_price, $cart_id]);

// Xóa session cart sau khi đồng bộ
unset($_SESSION['cart']);

$_SESSION['flash'] = 'Đã đồng bộ giỏ hàng thành công!';
header('Location: ' . BASE_URL . '/product/cart.php');
exit;
