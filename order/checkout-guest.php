<?php
require_once __DIR__ . '/../config.php';
require_once BASE_PATH . '/includes/header.php';

$cart = $_SESSION['cart'] ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy thông tin khách
    $customer_name    = trim($_POST['name']);
    $customer_phone   = trim($_POST['phone']);
    $customer_address = trim($_POST['address']);
    $customer_email = trim($_POST['email']);
    $customer_consignee = trim($_POST['consignee']);

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
        // tẠO ĐƠN HÀNG
        $stmtOrder = $pdo->prepare("
            INSERT INTO orders (customer_id, total_price, status)
            VALUES (:customer_id, :total_price, :status)
        ");
        
        $stmtOrder->execute([
            'customer_id'    => $customerId,
            'total_price'   => $total,
            'status'   => 'pending'
        ]);
        // 
        $orderId = $pdo->lastInsertId();

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
        var_dump($orderId);
        // 3. Xóa giỏ hàng và chuyển đến thank-you
        unset($_SESSION['cart'], $_SESSION['cart_count']);
        $redirect_url = BASE_URL . '/order/thank-you.php?order_id='.$orderId;
        header("Location: $redirect_url");
        exit;
    }
}
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

      <form action="<?php echo BASE_URL; ?>/order/thank-you.php" method="post">
        <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Họ và tên</label>
              <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Điện thoại</label>
              <input type="text" name="phone" class="form-control" required value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Địa chỉ</label>
              <textarea name="address" class="form-control" required><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label">Tên người nhận</label>
              <input type="text" name="consignee" class="form-control" required value="<?= htmlspecialchars($_POST['consignee'] ?? '') ?>">
            </div>
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
          <li class="list-group-item d-flex justify-content-between fw-bold">
            <div>Tổng cộng:</div>
            <div><?= number_format($total) ?> đ</div>
          </li>
        </ul>

        <button type="submit" class="btn btn-success">Xác nhận đặt hàng</button>
      </form>
    </div>
</section>


<?php require_once BASE_PATH . '/includes/footer.php'; ?>