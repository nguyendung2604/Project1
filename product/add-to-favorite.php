<?php
require_once __DIR__ . '/../config.php';

// Kiểm tra đăng nhập
if (empty($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'] ?? null;

if (!$product_id || !is_numeric($product_id)) {
    die('Sản phẩm không hợp lệ.');
}

// Kiểm tra đã tồn tại chưa
$stmt = $pdo->prepare("SELECT 1 FROM favorites WHERE user_id = ? AND product_id = ?");
$stmt->execute([$user_id, $product_id]);

if ($stmt->fetch()) {
    echo "Sản phẩm đã có trong danh sách yêu thích.";
} else {
    // Thêm mới
    $insertStmt = $pdo->prepare("INSERT INTO favorites (user_id, product_id) VALUES (?, ?)");
    try {
        $insertStmt->execute([$user_id, $product_id]);
        echo "Đã thêm vào danh sách yêu thích.";
    } catch (PDOException $e) {
        echo "Lỗi khi thêm: " . $e->getMessage();
    }
}

if ($_POST['action'] == 'list') {
    // Chuyển hướng về lại sản phẩm
    $redirect_url = BASE_URL . '/product/list.php';

    header("Location: $redirect_url");
    exit;
}else{
    // Chuyển hướng về lại trang giỏ hàng
    $redirect_url = BASE_URL . '/product/details.php?id='.$product_id;
    header("Location: $redirect_url");
    exit;
}

?>
