<?php
require_once __DIR__ . '/../config.php';

$order_id = $_POST['order_id'] ?? null;
$reason = trim($_POST['reason'] ?? '');

if (!$order_id || !$reason) {
    die('Thiếu thông tin yêu cầu hoàn trả.');
}

// Kiểm tra đơn hàng tồn tại và đã hoàn tất
$stmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = ? AND status = 'completed'");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    die('Không tìm thấy đơn hàng hợp lệ để hoàn trả.');
}

// Kiểm tra đã gửi yêu cầu chưa
$stmt = $pdo->prepare("SELECT * FROM return_requests WHERE order_id = ?");
$stmt->execute([$order_id]);
if ($stmt->rowCount() > 0) {
    die('Bạn đã gửi yêu cầu hoàn trả cho đơn hàng này.');
}

// Lưu yêu cầu
$stmt = $pdo->prepare("INSERT INTO return_requests (order_id, reason) VALUES (?, ?)");
$stmt->execute([$order_id, $reason]);

header("Location: " . BASE_URL . "/order/order-history.php");
exit;
?>