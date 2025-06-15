<?php
require_once __DIR__ . '/../../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerIds = $_POST['customer_ids'] ?? [];
    $productIds = $_POST['products'] ?? [];
    $quantities = $_POST['quantities'] ?? [];
    $discountInput = trim($_POST['discount'] ?? '');
    $note = trim($_POST['note'] ?? '');

    if (count($customerIds) === 0 || count($productIds) === 0 || count($productIds) !== count($quantities)) {
        $_SESSION['error'] = 'Dữ liệu không hợp lệ.';
        header('Location: create.php');
        exit;
    }

    // Lấy thông tin sản phẩm và tính tổng
    $orderItems = [];
    $total = 0;
    foreach ($productIds as $i => $productId) {
        $quantity = (int)$quantities[$i];
        $stmt = $pdo->prepare("SELECT price FROM products WHERE product_id = ?");
        $stmt->execute([$productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($product) {
            $subtotal = $product['price'] * $quantity;
            $total += $subtotal;
            $orderItems[] = [
                'product_id' => $productId,
                'quantity' => $quantity,
                'price' => $product['price']
            ];
        }
    }

    // Xử lý giảm giá
    $discountAmount = 0;
    if (str_ends_with($discountInput, '%')) {
        $percent = (float)rtrim($discountInput, '%');
        $discountAmount = ($total * $percent) / 100;
    } elseif (is_numeric($discountInput)) {
        $discountAmount = (float)$discountInput;
    }
    $finalTotal = max($total - $discountAmount, 0);

    // Tạo đơn hàng cho mỗi khách
    $stmtOrder = $pdo->prepare("INSERT INTO orders (customer_id, user_id, total_price, status, note, created_at) VALUES (?, ?, ?, 'pending', ?, NOW())");
    $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");

    foreach ($customerIds as $customerId) {
        $stmtOrder->execute([$customerId, $_SESSION['user_id'] ?? null, $finalTotal, $note]);
        $orderId = $pdo->lastInsertId();

        foreach ($orderItems as $item) {
            $stmtItem->execute([$orderId, $item['product_id'], $item['quantity'], $item['price']]);
        }
    }

    $_SESSION['success'] = 'Tạo đơn hàng thành công cho ' . count($customerIds) . ' khách hàng.';
    header("Location: orders.php");
    exit;
}
