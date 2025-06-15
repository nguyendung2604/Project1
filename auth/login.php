<?php
// auth/login.php
require_once __DIR__ . '/../config.php';

// Nếu đã đăng nhập, chuyển hướng đi
if (isset($_SESSION['user_id'])) {
    $redirect_url = ($_SESSION['role'] === 'admin') ? BASE_URL . '/admin/index.php' : BASE_URL . '/index.php';
    header("Location: $redirect_url");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Kiểm tra trạng thái
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user['status'] == 'actived') {
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_fullname'] = $user['fullname'];
            $_SESSION['role'] = $user['role'];

            // ✅ Đặt logic chuyển giỏ hàng vào đây
            if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                $cartItems = $_SESSION['cart'];
                $user_id = $_SESSION['user_id'];

                try {
                    $pdo->beginTransaction();

                    // Kiểm tra đã có cart chưa
                    $stmt = $pdo->prepare("SELECT cart_id FROM carts WHERE user_id = ?");
                    $stmt->execute([$user_id]);
                    $cart = $stmt->fetch();

                    if (!$cart) {
                        $stmt = $pdo->prepare("INSERT INTO carts (user_id, total_price) VALUES (?, 0)");
                        $stmt->execute([$user_id]);
                        $cart_id = $pdo->lastInsertId();
                    } else {
                        $cart_id = $cart['cart_id'];
                    }

                    foreach ($cartItems as $item) {
                        $product_id = $item['product_id'];
                        $quantity = $item['quantity'];
                        $price = $item['price'];

                        // Kiểm tra sản phẩm đã tồn tại trong cart_items chưa
                        $stmt = $pdo->prepare("
                            SELECT cart_item_id FROM cart_items WHERE cart_id = ? AND product_id = ?
                        ");
                        $stmt->execute([$cart_id, $product_id]);
                        $exists = $stmt->fetch();

                        if ($exists) {
                            $stmt = $pdo->prepare("
                                UPDATE cart_items SET quantity = quantity + ?, updated_at = NOW() 
                                WHERE cart_id = ? AND product_id = ?
                            ");
                            $stmt->execute([$quantity, $cart_id, $product_id]);
                        } else {
                            $stmt = $pdo->prepare("
                                INSERT INTO cart_items (cart_id, product_id, quantity, price) 
                                VALUES (?, ?, ?, ?)
                            ");
                            $stmt->execute([$cart_id, $product_id, $quantity, $price]);
                        }
                    }

                    // Tính lại tổng tiền
                    $stmt = $pdo->prepare("SELECT SUM(quantity * price) FROM cart_items WHERE cart_id = ?");
                    $stmt->execute([$cart_id]);
                    $total = $stmt->fetchColumn();

                    $stmt = $pdo->prepare("UPDATE carts SET total_price = ? WHERE cart_id = ?");
                    $stmt->execute([$total, $cart_id]);

                    $pdo->commit();

                    // ✅ Xóa session giỏ hàng
                    unset($_SESSION['cart']);
                } catch (Exception $e) {
                    $pdo->rollBack();
                    // Nếu có lỗi, vẫn cho đăng nhập nhưng hiển thị cảnh báo
                    error_log("Không thể chuyển giỏ hàng: " . $e->getMessage());
                }
            }

            // ✅ Sau khi xử lý xong, chuyển hướng đến trang chính
    
            $redirect_url = ($user['role'] === 'admin') ? BASE_URL . '/admin/index.php' : BASE_URL . '/index.php';
            header("Location: $redirect_url");
            exit;
        } else {
            $error = "Email hoặc mật khẩu không chính xác.";
        }
    }else{
        $error = "Account locked ! Please contact administrator to unlock.";
    }
    
}

require_once BASE_PATH . '/includes/header.php';
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
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header"><h3>Log in</h3></div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <form action="<?php echo BASE_URL; ?>/auth/login.php" method="post">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <a href="<?php echo BASE_URL; ?>/auth/register.php">Don't have an account? Sign up</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once BASE_PATH . '/includes/footer.php'; ?>