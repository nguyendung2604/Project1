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
    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);

    if (empty($fullname)) $errors[] = "Họ tên là bắt buộc.";
    if (empty($username)) $errors[] = "Tên đăng nhập là bắt buộc.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email không hợp lệ.";

    // Kiểm tra nếu email mới khác với email hiện tại của người dùng
    if ($email !== $profile_data['email']) {
        // Chỉ kiểm tra trùng lặp nếu email có thay đổi
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        // Nếu tìm thấy bất kỳ bản ghi nào có email trùng khớp
        if ($stmt->fetch()) {
            $errors[] = "Email này đã được đăng ký bởi người dùng khác.";
        }
    }

    if (empty($errors)) {
        try {
            $stmt_update = $pdo->prepare("UPDATE users SET fullname = ?, username = ?, email = ? WHERE user_id = ?");
            $stmt_update->execute([$fullname, $username, $email, $_SESSION['user_id']]);

            $_SESSION['flash_message'] = [
                'type' => 'success', // Loại thông báo: success, danger, warning, info
                'message' => 'Update profile successful !'
            ];

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
            <div class="card-header"><h3>Update Profile</h3></div>
            <div class="card-body">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $error): ?><p class="mb-0"><?php echo $error; ?></p><?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form action="<?php echo BASE_URL; ?>/admin/user/profile.php" method="post">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="fullname" class="form-label">Full name</label>
                            <input type="text" class="form-control" id="fullname" name="fullname" value="<?= $profile_data ? htmlspecialchars($profile_data['fullname']) : '' ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?= $profile_data ? htmlspecialchars($profile_data['username']) : '' ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= $profile_data ? htmlspecialchars($profile_data['email']) : '' ?>" required>
                        </div>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                    
                </form>
            </div>
        </div>
    </div>
</section>

<?php require_once BASE_PATH . '/admin/partials/footer.php'; ?>