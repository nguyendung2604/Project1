<?php
// /user-management/index.php
require_once __DIR__ . '/../config.php';
require_once BASE_PATH . '/includes/header.php';

$cart = $_SESSION['cart'] ?? [];
$cart_items = [];

$total = 0;

if (!empty($_SESSION['user_id'])) {
    // ✅ Trường hợp đã đăng nhập → lấy giỏ hàng từ database
    // Lấy product_id từ cart_items theo user_id
    $stmtCartCheck = $pdo->prepare("SELECT cart_id FROM carts WHERE user_id = ?");
    $stmtCartCheck->execute([$_SESSION['user_id']]);
    $cartDB = $stmtCartCheck->fetch(PDO::FETCH_ASSOC);

    if ($cartDB) {
        // Lấy danh sách tất cả sản phẩm trong database
        $sql = "SELECT 
            p.product_id, 
            p.name, 
            p.description, 
            p.price, 
            p.old_price, 
            p.quantity AS product_quantity, 
            p.brand_id,
            p.review,
            p.category_id, 
            pi.image_url,
            b.name AS brand_name, 
            c.name AS category_name,
            ci.quantity AS cart_quantity
        FROM products p
        JOIN cart_items ci ON p.product_id = ci.product_id
        JOIN carts ca ON ci.cart_id = ca.cart_id
        LEFT JOIN product_images pi ON p.product_id = pi.product_id
        LEFT JOIN brands b ON p.brand_id = b.brand_id
        LEFT JOIN categories c ON p.category_id = c.category_id
        WHERE ca.user_id = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$_SESSION['user_id']]);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($products as $product) {
            $product_id = $product['product_id'];
            $quantity = (int)$product['cart_quantity']; // lấy từ cart_items
            $price = (float)$product['price'];
            $subtotal = $price * $quantity;
            $total += $subtotal;

            $cart_items[] = [
                'id' => $product_id,
                'name' => $product['name'],
                'price' => $price,
                'image_url' => $product['image_url'],
                'quantity' => $quantity,
                'subtotal' => $subtotal,
            ];
        }
    }
} else {
    // ❌ Trường hợp chưa đăng nhập → lấy giỏ từ session
    $cart = $_SESSION['cart'] ?? [];

    if (!empty($cart)) {
        $placeholders = implode(',', array_fill(0, count($cart), '?'));
        // Lấy danh sách tất cả sản phẩm trong database
        $sql = "
            SELECT 
                p.product_id, 
                p.name, 
                p.description, 
                p.price, 
                p.old_price, 
                p.quantity, 
                p.brand_id,
                p.review,
                p.category_id, 
                pi.image_url,
                b.name AS brand_name, 
                c.name AS category_name
            FROM products p
            LEFT JOIN product_images pi ON p.product_id = pi.product_id
            LEFT JOIN brands b ON p.brand_id = b.brand_id
            LEFT JOIN categories c ON p.category_id = c.category_id
            WHERE p.product_id IN ($placeholders)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_keys($cart));
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($products as $product) {
            $product_id = $product['product_id'];
            $quantity = isset($cart[$product_id]) ? (int)$cart[$product_id] : 0;
            $price = (float)$product['price'];
            $subtotal = $price * $quantity;
            $total += $subtotal;

            $cart_items[] = [
                'id' => $product_id,
                'name' => $product['name'],
                'price' => $price,
                'image_url' => $product['image_url'],
                'quantity' => $quantity,
                'subtotal' => $subtotal,
            ];
        }
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
            <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">
                Cart Management 
            </li>
        </ol>
    </div>
</nav>
<section class="container mb-4">
    <div class="row">
        <h2 class="mb-4">🛒 Giỏ hàng của bạn</h2>
        <?php if (empty($cart_items)): ?>
            <div class="alert alert-warning">Giỏ hàng đang trống.</div>
        <?php else: ?>
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Ảnh</th>
                        <th>Sản phẩm</th>
                        <th>Đơn giá</th>
                        <th>Số lượng</th>
                        <th>Thành tiền</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item): ?>
                        <tr>
                            <td><img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" width="60"></td>
                            <td><?= htmlspecialchars($item['name']) ?></td>
                            <td><?= number_format($item['price']) ?> đ</td>
                            <td>
                                <form action="<?= BASE_URL ?>/cart/update-cart.php" method="post" class="d-flex align-items-center">
                                    <input type="hidden" name="id" value="<?= $item['product_id'] ?? $item['id'] ?>">
                                    <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" class="form-control form-control-sm w-50 me-2">
                                    <button type="submit" class="btn btn-sm btn-primary">Cập nhật</button>
                                </form>
                            </td>
                            <td><?= number_format($item['subtotal']) ?> đ</td>
                            <td>
                                <form action="<?= BASE_URL ?>/cart/remove-from-cart.php" method="post" onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?')">
                                    <input type="hidden" name="id" value="<?= $item['product_id'] ?? $item['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="4" class="text-end fw-bold">Tổng cộng:</td>
                        <td colspan="2" class="fw-bold text-danger"><?= number_format($total) ?> đ</td>
                    </tr>
                </tbody>
            </table>
            <div>
                <a href="<?php echo BASE_URL; ?>/product/clear-cart.php" class="btn btn-outline-danger" onclick="return confirm('Bạn có chắc muốn xóa toàn bộ giỏ hàng?')">🗑 Xóa tất cả</a>
                <?php if (!empty($_SESSION['user_id'])): ?>
                    <a href="<?php echo BASE_URL; ?>/order/checkout.php" class="btn btn-success">Đặt hàng</a>
                    <?php if (!empty($_SESSION['cart'])): ?>
                        <a href="<?php echo BASE_URL; ?>/cart/sync-session-cart.php" class="btn btn-outline-primary">🔄 Đồng bộ giỏ hàng</a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>/order/checkout-guest.php" class="btn btn-success">Đặt hàng</a>
                <?php endif; ?>
                
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once BASE_PATH . '/includes/footer.php'; ?>