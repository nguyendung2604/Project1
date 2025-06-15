<?php
require_once '../config.php';

// Kiểm tra đăng nhập
if (empty($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

$customer_id = null;

if ($user_id) {
    $stmtCusID = $pdo->prepare("SELECT customer_id FROM customers WHERE user_id = ?");
    $stmtCusID->execute([$user_id]);
    $customer = $stmtCusID->fetch();

    if ($customer) {
        $customer_id = $customer['customer_id'];
    }
}

// Xử lý cập nhật
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $addressText = trim($_POST['address']);
    $recipientName = trim($_POST['recipient_name']);
    $phone = trim($_POST['phone']);
    $address_id = $_POST['address_id'];
    $is_default = $_POST['is_default'];

    if (empty($addressText) || empty($recipientName) || empty($phone)) {
        $_SESSION['error'] = "Vui lòng nhập đầy đủ thông tin.";
        header('Location: profile.php');
        exit;
    }

    $stmt = $pdo->prepare("UPDATE shipping_addresses SET address = ?, recipient_name = ?, phone = ?, is_default = ? WHERE address_id = ? AND customer_id = ?");
    $stmt->execute([$addressText, $recipientName, $phone, $address_id, $customer_id, $is_default]);

    $_SESSION['success'] = "Cập nhật địa chỉ thành công.";
    header('Location: profile.php');
    exit;
}
