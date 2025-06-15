<?php
// /user-management/index.php
require_once __DIR__ . '/../config.php';
require_once BASE_PATH . '/includes/header.php';

// Kiểm tra đăng nhập
if (empty($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

$customer_id = null;

if ($user_id) {
    $stmtCusID = $pdo->prepare("SELECT customer_id FROM customers WHERE user_id = ?");
    $stmtCusID->execute([$user_id]);
    $customer = $stmtCusID->fetch();

    if ($customer) {
        $customer_id = $customer['customer_id'];
    }
}

$stmt = $pdo->prepare("SELECT * FROM shipping_addresses WHERE customer_id = ?");
$stmt->execute([$customer_id]);
$addresses = $stmt->fetchAll();
?>
<style>
    .div_breadcrumb{
        margin-top: 80px; 
    }
    .card img {
        transition: transform 0.3s ease;
        height: 160px;
        object-fit: contain;
        margin-top: 10px;
    }
    .card:hover img {
        transform: scale(1.05);
    }
    .card {
        border: 1px solid #EEEEEE;
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
                Quản lý thông tin cá nhân
            </li>
        </ol>
    </div>
</nav>
<section class="container mb-4">
    <div class="row p-3 bg-white">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h3 class="mb-0">Địa chỉ giao hàng</h3>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addressModal">
                + Thêm địa chỉ mới
            </button>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="addressModal" tabindex="-1" aria-labelledby="addressModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Thêm địa chỉ giao hàng</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <form method="POST" action="store-address.php">
                                <input type="text" name="recipient_name" class="form-control" placeholder="Người nhận" required>
                                <input type="text" name="phone" class="form-control mt-2" placeholder="Điện thoại" required>
                                <textarea name="address" class="form-control mt-2" placeholder="Địa chỉ" required></textarea>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" id="is_default" type="checkbox" name="is_default" value="1">
                                    <label class="form-check-label" for="is_default">Đặt làm mặc định</label>
                                </div>
                                <button type="submit" class="btn btn-success mt-2">Lưu</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>Người nhận</th>
                    <th>Điện thoại</th>
                    <th>Địa chỉ</th>
                    <th>Mặc định</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($addresses as $address): ?>
                <tr>
                    <td><?= htmlspecialchars($address['recipient_name']) ?></td>
                    <td><?= htmlspecialchars($address['phone']) ?></td>
                    <td><?= htmlspecialchars($address['address']) ?></td>
                    <td><?= $address['is_default'] ? '✔️' : '' ?></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addressModal<?= $address['address_id'] ?>">
                                Sửa
                            </button>
                            
                            <form method="POST" action="delete-address.php" onsubmit="return confirm('Bạn chắc chắn muốn xóa địa chỉ này?')">
                                <input type="hidden" name="address_id" value="<?= $address['address_id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <!-- Modal -->
                <div class="modal fade" id="addressModal<?= $address['address_id'] ?>" tabindex="-1" aria-labelledby="addressModal<?= $address['address_id'] ?>Label" aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Sửa địa chỉ giao hàng</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row p-4">
                                    <form method="POST" action="edit-address.php">
                                        <input type="hidden" name="address_id" value="<?= $address['address_id'] ?>">
                                        <input type="text" name="recipient_name" class="form-control" value="<?= htmlspecialchars($address['recipient_name']) ?>" placeholder="Người nhận" required>
                                        <input type="text" name="phone" class="form-control mt-2" value="<?= htmlspecialchars($address['phone']) ?>" placeholder="Điện thoại" required>
                                        <textarea name="address" class="form-control mt-2" placeholder="Địa chỉ" required><?= htmlspecialchars($address['address']) ?></textarea>
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" id="is_default" type="checkbox" name="is_default" value="1" <?= $address['is_default'] ? 'checked' : '' ?> >
                                            <label class="form-check-label" for="is_default">Đặt làm mặc định</label>
                                        </div>
                                        <div class="d-flex justify-content-center">
                                            <button type="submit" class="btn btn-success">Lưu</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require_once BASE_PATH . '/includes/footer.php'; ?>