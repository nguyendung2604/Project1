<?php
require_once __DIR__ . '/../../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'] ?? null;
    $status = $_POST['status'] ?? null;

//     echo '<pre>';
// var_dump($status);
// echo '</pre>';
// exit;

    $validStatuses = ['pending', 'processing', 'delivering', 'completed', 'cancelled'];

    if ($order_id && in_array($status, $validStatuses)) {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
        $stmt->execute([$status, $order_id]);

        $_SESSION['flash_message'] = [
            'type' => 'success', // Loại thông báo: success, danger, warning, info
            'message' => 'Cập nhật trạng thái thành công !'
        ];
    } else {
        $_SESSION['flash_message'] = [
            'type' => 'success', // Loại thông báo: success, danger, warning, info
            'message' => 'Add user successful !'
        ];
    }

    header("Location: show.php?id=" . urlencode($order_id));
    exit;
}
