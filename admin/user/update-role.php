<?php
header('Content-Type: application/json'); // Báo cho trình duyệt biết đây là phản hồi JSON
require_once __DIR__ . '/../../config.php';

$response = [
    'success' => false,
    'message' => 'Unknown error.'
];
// Kiểm tra xem người dùng đã đăng nhập và có quyền để thực hiện thao tác này không
// (Ví dụ: chỉ Admin mới có thể thay đổi vai trò)
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $response['message'] = 'You do not have permission to perform this operation.';
    echo json_encode($response);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? null;
    $new_role_name = $_POST['new_role'] ?? null;

    // Xác thực dữ liệu đầu vào
    if (empty($user_id) || !is_numeric($user_id) || empty($new_role_name)) {
        $response['message'] = 'Invalid input data.';
        echo json_encode($response);
        exit();
    }

    // Kiểm tra xem role_name có hợp lệ không (từ danh sách các vai trò đã định nghĩa)
    $allowed_roles = ['admin', 'user']; // Cập nhật danh sách này nếu bạn có thêm vai trò
    if (!in_array($new_role_name, $allowed_roles)) {
        $response['message'] = 'Invalid role.';
        echo json_encode($response);
        exit();
    }

    try {
        // Bắt đầu một transaction để đảm bảo tính toàn vẹn dữ liệu
        $pdo->beginTransaction();

        // Bước 1: Lấy role_id từ role_name
        $stmt_role = $pdo->prepare("UPDATE users SET role = ? WHERE user_id = ?");
        $stmt_role->execute([$new_role_name, $user_id]);

        $pdo->commit();

        $response['success'] = true;
        $response['message'] = 'User role updated successfully !';

    } catch (PDOException $e) {
        $pdo->rollBack();
        $response['message'] = 'Lỗi cơ sở dữ liệu: ' . $e->getMessage();
        // Log lỗi chi tiết hơn nếu cần
        error_log("Lỗi cập nhật vai trò: " . $e->getMessage());
    }
}
echo json_encode($response);
exit();
?>