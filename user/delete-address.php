<?php
require_once '../config.php';

// Kiểm tra đăng nhập
if (empty($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $addressId = $_POST['address_id'] ?? null;
    $userId = $_SESSION['user_id'] ?? null;

    if (!$addressId || !$userId) {
        $_SESSION['error'] = "Dữ liệu không hợp lệ.";
        header("Location: addresses.php");
        exit;
    }

    // Lấy customer_id từ user_id
    $stmt = $pdo->prepare("SELECT customer_id FROM customers WHERE user_id = ?");
    $stmt->execute([$userId]);
    $customer = $stmt->fetch();

    if (!$customer) {
        $_SESSION['error'] = "Không tìm thấy khách hàng.";
        header('Location: profile.php');
        exit;
    }

    $customerId = $customer['customer_id'];

    // Kiểm tra địa chỉ có thuộc khách hàng không
    $stmt = $pdo->prepare("SELECT * FROM shipping_addresses WHERE address_id = ? AND customer_id = ?");
    $stmt->execute([$addressId, $customerId]);
    $address = $stmt->fetch();

    if (!$address) {
        $_SESSION['error'] = "Địa chỉ không tồn tại hoặc không thuộc quyền sở hữu.";
        header('Location: profile.php');
        exit;
    }

    // Xóa địa chỉ
    $stmt = $pdo->prepare("DELETE FROM shipping_addresses WHERE address_id = ?");
    $stmt->execute([$addressId]);

    $_SESSION['success'] = "Xóa địa chỉ thành công.";
    header('Location: profile.php');
    exit;
}
