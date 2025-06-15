<?php
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/admin/partials/header.php';
require_once BASE_PATH . '/admin/partials/sidebar.php';

$errors = [];

$profile_stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?"); // Đổi tên biến để rõ ràng hơn
$profile_stmt->execute([$_SESSION['user_id']]);

// Lấy dữ liệu từ kết quả truy vấn
$profile_data = $profile_stmt->fetch(PDO::FETCH_ASSOC); // Lấy một hàng dưới dạng mảng kết hợp

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ... (Toàn bộ logic xử lý form giữ nguyên như hướng dẫn trước) ...
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmNewPassword = $_POST['confirm_new_password'];

    if (strlen($newPassword) < 6) $errors[] = "Mật khẩu phải có ít nhất 6 ký tự.";
    if ($newPassword !== $confirmNewPassword) $errors[] = "Mật khẩu xác nhận không khớp.";

    if (empty($errors)) {
        $hashed_password = password_hash($newPassword, PASSWORD_DEFAULT);
        try {
            $stmt_change = $pdo->prepare("UPDATE users SET password = ? WHERE user_id = ?");
            $stmt_change->execute([$hashed_password, $_SESSION['user_id']]);

            $_SESSION['flash_message'] = [
                'type' => 'success', // Loại thông báo: success, danger, warning, info
                'message' => 'Change password successful !'
            ];

        } catch (PDOException $e) {
            $errors[] = "Lỗi khi thay đổi: " . $e->getMessage();
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
            <div class="card-header"><h3>Change password</h3></div>
            <div class="card-body">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $error): ?><p class="mb-0"><?php echo $error; ?></p><?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form action="<?php echo BASE_URL; ?>/admin/user/change-password.php" method="post">
                    <div class="row">
                        <div class="mb-3">
                            <label for="currentPassword" class="form-label">Current password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="currentPassword" name="current_password" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="currentPassword">
                                    <i class="fas fa-eye-slash"></i> </button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="newPassword" class="form-label">New password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="newPassword" name="new_password" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="newPassword">
                                    <i class="fas fa-eye-slash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="confirmNewPassword" class="form-label">Confirm new password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="confirmNewPassword" name="confirm_new_password" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="confirmNewPassword">
                                    <i class="fas fa-eye-slash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Change</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Lấy tất cả các nút ẩn/hiện mật khẩu
        const togglePasswordButtons = document.querySelectorAll('.toggle-password');

        togglePasswordButtons.forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.dataset.target;
                const passwordInput = document.getElementById(targetId);
                const icon = this.querySelector('i');

                // Thay đổi type của input
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    // Thay đổi lớp biểu tượng Font Awesome
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                } else {
                    passwordInput.type = 'password';
                    // Thay đổi lớp biểu tượng Font Awesome
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                }
            });
        });
    });  
</script>

<?php require_once BASE_PATH . '/admin/partials/footer.php'; ?>