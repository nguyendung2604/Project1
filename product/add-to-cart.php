<?php
// /user-management/index.php
require_once __DIR__ . '/../config.php';

$product_id = $_POST['product_id'] ?? null;
$quantity = max(1, intval($_POST['quantity'] ?? 1));

// Lấy giá sản phẩm từ DB (bắt buộc có để lưu vào session hoặc DB)
$stmt = $pdo->prepare("SELECT price FROM products WHERE product_id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die('Sản phẩm không tồn tại');
}
$price = $product['price'];

if (!isset($_SESSION['user_id'])) {
    //Chưa đăng nhập: lưu vào SESSION
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['product_id'] == $product_id) {
            $item['quantity'] += $quantity;
            $found = true;
            break;
        }
    }
    if (!$found) {
        $_SESSION['cart'][] = [
            'product_id' => $product_id,
            'quantity' => $quantity,
            'price' => $price
        ];
    }

    // Chuyển hướng về lại sản phẩm
    $redirect_url = BASE_URL . '/product/cart.php';

    header("Location: $redirect_url");
    exit;
}

//Nếu đã đăng nhập: lưu vào database
$user_id = $_SESSION['user_id'];

try {
    $pdo->beginTransaction();

    // 1. Lấy hoặc tạo giỏ
    $stmt = $pdo->prepare("SELECT cart_id FROM carts WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $cart = $stmt->fetch();

    if (!$cart) {
        $stmt = $pdo->prepare("INSERT INTO carts (user_id, total_price) VALUES (?, 0)");
        $stmt->execute([$user_id]);
        $cart_id = $pdo->lastInsertId();
    } else {
        $cart_id = $cart['cart_id'];
    }

    // 2. Lưu vào cart_items
    $stmt = $pdo->prepare("
        SELECT cart_item_id FROM cart_items WHERE cart_id = ? AND product_id = ?
    ");
    $stmt->execute([$cart_id, $product_id]);
    $exists = $stmt->fetch();

    if ($exists) {
        $stmt = $pdo->prepare("
            UPDATE cart_items SET quantity = quantity + ?, updated_at = NOW() 
            WHERE cart_id = ? AND product_id = ?
        ");
        $stmt->execute([$quantity, $cart_id, $product_id]);
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO cart_items (cart_id, product_id, quantity, price) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$cart_id, $product_id, $quantity, $price]);
    }

    // 3. Cập nhật lại total
    $stmt = $pdo->prepare("SELECT SUM(quantity * price) FROM cart_items WHERE cart_id = ?");
    $stmt->execute([$cart_id]);
    $total = $stmt->fetchColumn();

    $stmt = $pdo->prepare("UPDATE carts SET total_price = ? WHERE cart_id = ?");
    $stmt->execute([$total, $cart_id]);

    $pdo->commit();
    header('Location: ../cart.php');
} catch (Exception $e) {
    $pdo->rollBack();
    die("Lỗi: " . $e->getMessage());
}
// $product_id = (int)($_POST['product_id'] ?? 0);
// $quantity = max(1, (int)($_POST['quantity'] ?? 1));

// // Khởi tạo giỏ hàng nếu chưa có
// if (!isset($_SESSION['cart'])) {
//     $_SESSION['cart'] = [];
// }

// // Cộng dồn nếu sản phẩm đã có
// if (isset($_SESSION['cart'][$product_id])) {
//     $_SESSION['cart'][$product_id] += $quantity;
// } else {
//     $_SESSION['cart'][$product_id] = $quantity;
// }

// // Cập nhật tổng số lượng
// $_SESSION['cart_count'] = array_sum($_SESSION['cart']);

// if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
//     // Trả JSON nếu là AJAX
//     echo json_encode(['success' => true, 'cart_count' => $_SESSION['cart_count']]);
//     exit;
// }

if ($_POST['action'] == 'list') {
    // Chuyển hướng về lại sản phẩm
    $redirect_url = BASE_URL . '/product/list.php';

    header("Location: $redirect_url");
    exit;
}elseif ($_POST['action'] == 'favorites') {
    $redirect_url = BASE_URL . '/user/favorite-list.php';

    header("Location: $redirect_url");
    exit;
}else{
    // Chuyển hướng về lại trang giỏ hàng
    // $redirect_url = BASE_URL . '/product/cart.php';
    $redirect_url = BASE_URL . '/product/details.php?id='.$product_id;
    header("Location: $redirect_url");
    exit;
}

?>