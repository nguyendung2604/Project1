<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = $_POST['order_id'] ?? null;
    $reason = trim($_POST['reason'] ?? '');

    if (!$orderId || !$reason || !is_numeric($orderId)) {
        $_SESSION['error'] = "Dữ liệu không hợp lệ.";
        header("Location: " . BASE_URL . "/order/order-history.php");
        exit;
    }

    // Kiểm tra trạng thái hiện tại
    $stmt = $pdo->prepare("SELECT status FROM orders WHERE order_id = ?");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        $_SESSION['error'] = "Không tìm thấy đơn hàng.";
        header("Location: " . BASE_URL . "/order/order-history.php");
        exit;
    }

    if ($order['status'] !== 'pending') {
        $_SESSION['error'] = "Chỉ đơn hàng đang chờ xử lý mới được hủy.";
        header("Location: " . BASE_URL . "/order/order-history.php");
        exit;
    }

    // Cập nhật trạng thái + ghi lý do (nếu bạn có cột reason trong bảng orders hoặc bảng riêng)
    $stmt = $pdo->prepare("UPDATE orders SET status = 'cancelled' WHERE order_id = ?");
    $stmt->execute([$orderId]);

    // Nếu bạn có bảng ghi log lý do hủy riêng, bạn có thể thêm tại đây

    $_SESSION['success'] = "Đơn hàng đã được hủy.";
    header("Location: " . BASE_URL . "/order/order-history.php");
    exit;
}
