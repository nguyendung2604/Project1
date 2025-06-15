<?php
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/admin/partials/header.php';
require_once BASE_PATH . '/admin/partials/sidebar.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    if (empty($fullname)) $errors[] = "Họ tên là bắt buộc.";
    if (empty($username)) $errors[] = "Tên đăng nhập là bắt buộc.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email không hợp lệ.";
    if (strlen($password) < 6) $errors[] = "Mật khẩu phải có ít nhất 6 ký tự.";
    if ($password !== $password_confirm) $errors[] = "Mật khẩu xác nhận không khớp.";

    $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $errors[] = "Email này đã được đăng ký.";
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        try {
            $sql = "INSERT INTO users (fullname, username, email, password) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$fullname, $username, $email, $hashed_password]);

            $_SESSION['flash_message'] = [
                'type' => 'success', // Loại thông báo: success, danger, warning, info
                'message' => 'Add user successful !'
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
            <div class="card-header"><h3>Add User</h3></div>
            <div class="card-body">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $error): ?><p class="mb-0"><?php echo $error; ?></p><?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form action="<?php echo BASE_URL; ?>/admin/user/add.php" method="post">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="fullname" class="form-label">Full name</label>
                            <input type="text" class="form-control" id="fullname" name="fullname">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password_confirm" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
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