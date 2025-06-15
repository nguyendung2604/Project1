<?php
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/admin/partials/header.php';
require_once BASE_PATH . '/admin/partials/sidebar.php';

$errors = [];

$user_id_to_edit = null;
$user_data = []; // Dữ liệu của người dùng cần chỉnh sửa

// Kiểm tra xem user_id có được truyền qua URL không
if (isset($_GET['user_id']) && is_numeric($_GET['user_id'])) {
    $user_id_to_edit = (int)$_GET['user_id']; // Chuyển đổi sang số nguyên để đảm bảo an toàn

    // Truy vấn thông tin người dùng từ CSDL dựa trên user_id_to_edit
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?"); // Giả sử cột ID là 'id'
        $stmt->execute([$user_id_to_edit]);
        $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user_data) {
            // Xử lý trường hợp không tìm thấy người dùng
            $_SESSION['flash_message'] = [
                'type' => 'danger',
                'message' => 'Người dùng không tồn tại.'
            ];
            header("Location: " . BASE_URL . "/admin/user/manage.php"); // Chuyển hướng về trang danh sách
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['flash_message'] = [
            'type' => 'danger',
            'message' => 'Lỗi cơ sở dữ liệu: ' . $e->getMessage()
        ];
        header("Location: " . BASE_URL . "/admin/user/manage.php");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $user_id_to_update = $_POST['user_id'];

    if (empty($fullname)) $errors[] = "Họ tên là bắt buộc.";
    if (empty($username)) $errors[] = "Tên đăng nhập là bắt buộc.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email không hợp lệ.";

    // Kiểm tra nếu email mới khác với email hiện tại của người dùng
    $stmtEmail = $pdo->prepare("SELECT email FROM users WHERE user_id = ?"); // Giả sử cột ID là 'id'
    $stmtEmail->execute([$user_id_to_update]);
    $emailCheck = $stmtEmail->fetch(PDO::FETCH_ASSOC);

    if ($email !== $emailCheck['email']) {
        // Chỉ kiểm tra trùng lặp nếu email có thay đổi
        $stmtCheck = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmtCheck->execute([$email]);

        // Nếu tìm thấy bất kỳ bản ghi nào có email trùng khớp
        if ($stmtCheck->fetch()) {
            $errors[] = "Email này đã được đăng ký bởi người dùng khác.";
        }
    }

    if (empty($errors)) {
        try {
            $stmt_updated = $pdo->prepare("UPDATE users SET fullname = ?, username = ?, email = ? WHERE user_id = ?");
            $stmt_updated->execute([$fullname, $username, $email, $user_id_to_update]);

            $_SESSION['flash_message'] = [
                'type' => 'success', // Loại thông báo: success, danger, warning, info
                'message' => 'Edit user successful !'
            ];

            $redirect_url = BASE_URL . '/admin/user/manage.php';
            header("Location: $redirect_url");
            exit;

        } catch (PDOException $e) {
            $errors[] = "Lỗi khi đăng ký: " . $e->getMessage();
        }
    }
}

?>

<style>
    /* Optional: Đảm bảo body và html chiếm toàn bộ chiều cao viewport */
    html, body {
        height: 100%;
        margin: 0;
        padding: 0;
        display: flex; /* Sử dụng flexbox để căn giữa theo chiều dọc nếu muốn */
        flex-direction: column;
    }
    body {
        background-color: #f8f9fa; /* Thêm màu nền để dễ nhìn hơn */
    }
    /* Nếu bạn muốn form đăng nhập luôn ở giữa màn hình theo chiều dọc */
    .position-relative {
        flex-grow: 1; /* Cho phép section chiếm hết không gian còn lại */
        display: flex;
        align-items: center; /* Căn giữa theo chiều dọc */
        justify-content: center; /* Căn giữa theo chiều ngang */
    }
</style>
<section class="position-relative">
    <div class="container py-5">
        <div class="card mt-5">
            <div class="card-header"><h3>Edit User</h3></div>
            <div class="card-body">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $error): ?><p class="mb-0"><?php echo $error; ?></p><?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form action="<?php echo BASE_URL; ?>/admin/user/edit.php" method="post">
                    <div class="row">
                        <input type="hidden" name="user_id" value="<?= $user_data ? htmlspecialchars($user_data['user_id']) : '' ?>">
                        <div class="col-md-6 mb-3">
                            <label for="fullname" class="form-label">Full name</label>
                            <input type="text" class="form-control" id="fullname" name="fullname" value="<?= $user_data ? htmlspecialchars($user_data['fullname']) : '' ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?= $user_data ? htmlspecialchars($user_data['username']) : '' ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= $user_data ? htmlspecialchars($user_data['email']) : '' ?>" required>
                        </div>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                    
                </form>
            </div>
        </div>
    </div>
</section>

<?php require_once BASE_PATH . '/admin/partials/footer.php'; ?>