<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kiểm tra đã đăng nhập chưa
    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . BASE_URL . '/auth/login.php');
        exit;
    }

    $product_id = (int)$_POST['product_id'];
    $comment = trim($_POST['comment']);

    // Lấy dữ liệu từ session
    $user_id = $_SESSION['user_id'];

    // Truy vấn lấy customer_id tương ứng
    $customerStmt = $pdo->prepare("SELECT customer_id FROM customers WHERE user_id = ?");
    $customerStmt->execute([$user_id]);
    $customer = $customerStmt->fetch(PDO::FETCH_ASSOC);

    if (!$customer) {
        // Nếu không tìm thấy customer => có thể redirect hoặc báo lỗi
        die("Không tìm thấy thông tin khách hàng.");
    }

    $customer_id = $customer['customer_id'];

    if ($product_id > 0 && $comment) {
        $stmt = $pdo->prepare("
            INSERT INTO reviews (product_id, customer_id, comment) 
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$product_id, $customer_id, $comment]);
    }

    $redirect_url = BASE_URL . '/product/details.php?id='.$product_id;
    header("Location: $redirect_url");
    exit;
}
