<?php
require_once __DIR__ . '/../config.php';


$cart = [];

// Mảng lưu giá trị mặc định cho form
$formData = [
    'name'      => '',
    'phone'     => '',
    'email'     => '',
    'address'   => '',
    'consignee' => ''
];

if (!empty($_SESSION['user_id'])) {
    // Lấy cart_id theo user_id
    $stmtCart = $pdo->prepare("SELECT cart_id FROM carts WHERE user_id = ? LIMIT 1");
    $stmtCart->execute([$_SESSION['user_id']]);
    $cartRow = $stmtCart->fetch(PDO::FETCH_ASSOC);

    if ($cartRow) {
        $cart_id = $cartRow['cart_id'];

        // Lấy cart_items theo cart_id
        $stmtItems = $pdo->prepare("
            SELECT product_id, quantity 
            FROM cart_items 
            WHERE cart_id = ?
        ");
        $stmtItems->execute([$cart_id]);
        $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

        foreach ($items as $item) {
            $cart[$item['product_id']] = $item['quantity'];
        }
    }
} else {
    // Nếu chưa đăng nhập, dùng session
    $cart = $_SESSION['cart'] ?? [];
}

// Nếu đã đăng nhập, lấy thông tin user
if (!empty($_SESSION['user_id'])) {
    //Kiểm tra theo user_id xem đã tồn tại trong bảng customer chưa
    $stmtCheckCustomerByIdUser = $pdo->prepare("
        SELECT customer_id 
        FROM customers 
        WHERE user_id = ?
        LIMIT 1
    ");
    $stmtCheckCustomerByIdUser->execute([ $_SESSION['user_id'] ]);
    $customerExistingByIdUser = $stmtCheckCustomerByIdUser->fetch(PDO::FETCH_ASSOC);

    if (!$customerExistingByIdUser) {
        $stmtCheckUser = $pdo->prepare("
            SELECT * 
            FROM users 
            WHERE user_id = ?
            LIMIT 1
        ");
        $stmtCheckUser->execute($_SESSION['user_id']);
        $userExisting = $stmtCheckUser->fetch(PDO::FETCH_ASSOC);

        // Lấy thông tin email của users tìm trong customer
        $stmtCheckCustomerByEmail = $pdo->prepare("
            SELECT * 
            FROM customers 
            WHERE email = ?
            LIMIT 1
        ");
        $stmtCheckCustomerByEmail->execute($userExisting['email']);
        $customerExistingByEmail = $stmtCheckCustomerByEmail->fetch(PDO::FETCH_ASSOC);

        if ($customerExistingByEmail) {
            $stmtCustomerUpdate = $pdo->prepare("UPDATE customers SET user_id = ? WHERE customer_id = ?");
            $stmtCustomerUpdate->execute([$_SESSION['user_id'], $customerExistingByEmail['customer_id']]);
        }
    }
    // Lấy thông tin khách hàng từ user
    $stmtUser = $pdo->prepare("
        SELECT 
            c.name, 
            c.phone, 
            c.email, 
            c.address,
            c.consignee
        FROM users u
        JOIN customers c 
          ON c.user_id = u.user_id
        WHERE u.user_id = ?
        LIMIT 1
    ");
    $stmtUser->execute([ $_SESSION['user_id'] ]);

    if ($u = $stmtUser->fetch(PDO::FETCH_ASSOC)) {
        $formData['name']      = $u['name'];
        $formData['phone']     = $u['phone'];
        $formData['email']     = $u['email'];
        $formData['address']   = $u['address'];
        $formData['consignee'] = $u['consignee'];
    } 
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy thông tin khách
    $customer_name    = trim($_POST['name']);
    $customer_phone   = trim($_POST['phone']);
    $customer_address = trim($_POST['address']);
    $customer_email = trim($_POST['email']);
    $customer_consignee = trim($_POST['consignee']);
    $payment_method = $_POST['payment_method'] ?? 'cod';
    $coupon_code = trim($_POST['coupon_code'] ?? '');
    $total_price_param = (int) ($_POST['total_price'] ?? 0);

    // Số tiền dược giảm
    $discount_amount = 0;

    if (empty($cart)) {
        $error = 'Giỏ hàng trống!';
    } elseif (!$customer_name || !$customer_phone || !$customer_address || !$customer_email || !$customer_consignee) {
        $error = 'Vui lòng điền đầy đủ thông tin giao hàng.';
    } else {
        //Thử lấy customer theo email hoặc phone
        $stmtCheckCustomer = $pdo->prepare("
            SELECT customer_id 
            FROM customers 
            WHERE email = :email 
               OR phone = :phone
            LIMIT 1
        ");
        $stmtCheckCustomer->execute([
            'email' => $customer_email,
            'phone' => $customer_phone
        ]);
        $customerExisting = $stmtCheckCustomer->fetch(PDO::FETCH_ASSOC);

        if ($customerExisting) {
            // Nếu đã có, dùng ID cũ
            $customerId = $customerExisting['customer_id'];
        } else {
            //lƯU THÔNG ITN KHÁCH HÀNG
            $stmt = $pdo->prepare("
                INSERT INTO customers (name, phone, email, address, consignee)
                VALUES (:name, :phone, :email, :address, :consignee)
            ");
            
            $stmt->execute([
                'name'    => $customer_name,
                'phone'   => $customer_phone,
                'email'   => $customer_email,
                'address' => $customer_address,
                'consignee'  => $customer_consignee
            ]);
            // Lấy khách hàng vừa tạo
            $customerId = $pdo->lastInsertId();
        }
        // Tính tổng
        $total = 0;
        foreach ($cart as $pid => $qty) {
            // Lấy giá sản phẩm
            $p = $pdo->prepare("SELECT price FROM products WHERE product_id = ?");
            $p->execute([$pid]);
            $row = $p->fetch();
            $total += $row['price'] * $qty;
        }
        // Xử lý mã giảm giá
        $coupon = null; // Khởi tạo sớm

        if (!empty($coupon_code)) {
            $stmtCoupon = $pdo->prepare("
                SELECT * FROM coupons 
                WHERE code = ? 
                  AND (expires_at IS NULL OR expires_at > NOW())
                  AND (usage_limit IS NULL OR used_count < usage_limit)
                LIMIT 1
            ");
            $stmtCoupon->execute([$coupon_code]);
            $coupon = $stmtCoupon->fetch(PDO::FETCH_ASSOC);

            if ($coupon) {
                if ($coupon['discount_type'] === 'percentage') {
                    $discount_amount = floor($total * $coupon['discount_value'] / 100);
                } else {
                    $discount_amount = $coupon['discount_value'];
                }

                // Giới hạn giảm tối đa bằng tổng đơn
                $discount_amount = min($discount_amount, $total);

                $total -= $discount_amount;
            } else {
                $error = 'Mã giảm giá không hợp lệ hoặc đã hết hạn.';
            }
        }

        // tẠO ĐƠN HÀNG
        try {
            $stmtOrder = $pdo->prepare("
                INSERT INTO orders (user_id, customer_id, total_price, status, payment_method, coupon_code, discount_amount)
                VALUES (:user_id, :customer_id, :total_price, :status, :payment_method, :coupon_code, :discount_amount)
            ");
        
            $stmtOrder->execute([
                'user_id'    => $_SESSION['user_id'],
                'customer_id'    => $customerId,
                'total_price'   => $total,
                'status'   => 'pending',
                'payment_method' => $payment_method,
                'coupon_code'     => $coupon_code ?: null,
                'discount_amount' => $discount_amount
            ]);
            // 
            $orderId = $pdo->lastInsertId();
        } catch (PDOException $e) {
            die("Lỗi tạo đơn hàng: " . $e->getMessage());
        }

        //Lưu chi tiết đơn (order_items)
        $stmtItem = $pdo->prepare("
            INSERT INTO order_items (order_id, product_id, quantity, price)
            VALUES (:order_id, :product_id, :qty, :price)
        ");
        foreach ($cart as $pid => $qty) {
            // Lấy giá tại thời điểm
            $p = $pdo->prepare("SELECT price FROM products WHERE product_id = ?");
            $p->execute([$pid]);
            $price = $p->fetchColumn();
            $stmtItem->execute([
                'order_id'   => $orderId,
                'product_id' => $pid,
                'qty'        => $qty,
                'price'      => $price
            ]);
        }
        // ✅ Cập nhật số lần sử dụng mã giảm giá
        if (!empty($coupon)) {
            $pdo->prepare("UPDATE coupons SET used_count = used_count + 1 WHERE coupon_id = ?")
                ->execute([$coupon['coupon_id']]);
        }
        // 3. Xóa giỏ hàng và chuyển đến thank-you
        
        // Xóa giỏ hàng nếu người dùng đã đăng nhập
        if (!empty($_SESSION['user_id'])) {
            // Lấy cart_id
            $stmtGetCartDelete = $pdo->prepare("SELECT cart_id FROM carts WHERE user_id = ?");
            $stmtGetCartDelete->execute([$_SESSION['user_id']]);
            $cartRowDelete = $stmtGetCartDelete->fetch(PDO::FETCH_ASSOC);

            if ($cartRowDelete) {
                $cart_idDe = $cartRowDelete['cart_id'];

                // Xóa cart_items trước
                $pdo->prepare("DELETE FROM cart_items WHERE cart_id = ?")->execute([$cart_idDe]);

                // Xóa cart
                $pdo->prepare("DELETE FROM carts WHERE cart_id = ?")->execute([$cart_idDe]);
            }
        }else{
            unset($_SESSION['cart'], $_SESSION['cart_count']);
        }
        // Lưu gia tiền vào session
        $_SESSION['order_total_price'] = $total_price_param;
        // Chuyển hướng sang trang thanh toán nếu không phải là cod
        if ($payment_method == 'card_expiry') {
            
            $redirect_url = BASE_URL . '/order/card-expiry.php';
            header("Location: $redirect_url");
            exit;
        } elseif ($payment_method == 'bank_transfer') {
            $redirect_url = BASE_URL . '/order/bank-transfer.php';
            header("Location: $redirect_url");
            exit;
        } elseif ($payment_method == 'vnpay') {
            $redirect_url = BASE_URL . '/order/vnpay.php';
            header("Location: $redirect_url");
            exit;
        }
    }
}

require_once BASE_PATH . '/includes/header.php';
?>
<style>
    .div_breadcrumb{
        margin-top: 80px; 
    }
</style>
<!-- Breadcrumb -->
<nav class="container mt-3 " aria-label="breadcrumb">
    <div class="row div_breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?php echo BASE_URL; ?>/index.php" class="text-muted text-decoration-none">Home</a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?php echo BASE_URL; ?>/product/cart.php" class="text-muted text-decoration-none">Quản lý giỏ hàng</a>
            </li>
            <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">
                Đặt hàng
            </li>
        </ol>
    </div>
</nav>
<section class="container mb-4">
    <div class="row p-2 bg-white">
        <h4>Thông tin nhận hàng</h4>

      <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form action="" method="post">
        <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Họ và tên</label>
              <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($_POST['name'] ?? $formData['name']) ?>">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Điện thoại</label>
              <input type="text" name="phone" class="form-control" required value="<?= htmlspecialchars($_POST['phone'] ?? $formData['phone']) ?>">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($_POST['email'] ?? $formData['email']) ?>">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Địa chỉ</label>
              <textarea name="address" class="form-control" required><?= htmlspecialchars($_POST['address'] ?? $formData['address']) ?></textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label">Tên người nhận</label>
              <input type="text" name="consignee" class="form-control" required value="<?= htmlspecialchars($_POST['consignee'] ?? $formData['consignee']) ?>">
            </div>
        </div>
        <hr>
        <h4>Mã giảm giá</h4>
        <div class="mb-3">
            <input type="text" name="coupon_code" class="form-control" placeholder="Nhập mã (nếu có)" value="<?= htmlspecialchars($_POST['coupon_code'] ?? '') ?>">
        </div>
        <hr>
        <h4>Chi tiết giỏ hàng</h4>
        <ul class="list-group mb-3">
          <?php
          $total = 0;
          foreach ($cart as $pid => $qty):
            $p = $pdo->prepare("SELECT name, price FROM products WHERE product_id = ?");
            $p->execute([$pid]);
            $prod = $p->fetch();
            $subtotal = $prod['price'] * $qty;
            $total += $subtotal;
          ?>
          <li class="list-group-item d-flex justify-content-between">
            <div><?= htmlspecialchars($prod['name']) ?> × <?= $qty ?></div>
            <div><?= number_format($subtotal) ?> đ</div>
          </li>
          <?php endforeach; ?>

          <?php if (!empty($discount_amount)): ?>
            <li class="list-group-item d-flex justify-content-between">
              <div>Mã giảm giá (<?= htmlspecialchars($coupon_code) ?>):</div>
              <div>-<?= number_format($discount_amount) ?> đ</div>
            </li>

            <li class="list-group-item d-flex justify-content-between fw-bold">
                <div>Tổng cộng:</div>
                <div><?= number_format($total - $discount_amount) ?> đ</div>
                <input type="hidden" name="total_price" value="<?= $total - ($discount_amount ?? 0) ?>">
            </li>
            <?php else: ?>
                <li class="list-group-item d-flex justify-content-between fw-bold">
                <div>Tổng cộng:</div>
                <div><?= number_format($total) ?> đ</div>
                <input type="hidden" name="total_price" value="<?= $total ?>">
              </li>
          <?php endif; ?>
        </ul>
        <hr>
        <h4>Phương thức thanh toán</h4>
        <div class="mb-3">
            <select name="payment_method" class="form-select" required>
                <option value="cod" <?= ($_POST['payment_method'] ?? '') === 'cod' ? 'selected' : '' ?>>Thanh toán khi nhận hàng (COD)</option>
                <option value="bank_transfer" <?= ($_POST['payment_method'] ?? '') === 'bank_transfer' ? 'selected' : '' ?>>Chuyển khoản ngân hàng</option>
                <option value="card_expiry" <?= ($_POST['payment_method'] ?? '') === 'card_expiry' ? 'selected' : '' ?>>Thẻ tín dụng/ghi nợ</option>
                <option value="vnpay" <?= ($_POST['payment_method'] ?? '') === 'vnpay' ? 'selected' : '' ?>>Thanh toán VNPAY</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Xác nhận đặt hàng và thanh toán</button>
      </form>
    </div>
</section>


<?php require_once BASE_PATH . '/includes/footer.php'; ?>