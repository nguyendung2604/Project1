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

$recipient_name = $_POST['recipient_name'];
$phone = $_POST['phone'];
$address = $_POST['address'];
$is_default = isset($_POST['is_default']) ? 1 : 0;

if ($is_default) {
    $pdo->prepare("UPDATE shipping_addresses SET is_default = 0 WHERE customer_id = ?")->execute([$customer_id]);
}

$stmt = $pdo->prepare("INSERT INTO shipping_addresses (customer_id, recipient_name, phone, address, is_default) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$customer_id, $recipient_name, $phone, $address, $is_default]);

header('Location: profile.php');
