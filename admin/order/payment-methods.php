<?php
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/admin/partials/header.php';
require_once BASE_PATH . '/admin/partials/sidebar.php';

// Xử lý thêm mới phương thức
// Xử lý thêm mới
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $method_code = $_POST['method_code'];
    $method_name = $_POST['method_name'];

    $stmt = $pdo->prepare("INSERT INTO payment_methods (method_code, method_name) VALUES (?, ?)");
    $stmt->execute([$method_code, $method_name]);
    header("Location: payment-methods.php");
    exit;
}

// Xử lý cập nhật
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id = $_POST['payment_method_id'];
    $method_code = $_POST['method_code'];
    $method_name = $_POST['method_name'];

    $stmt = $pdo->prepare("UPDATE payment_methods SET method_code = ?, method_name = ? WHERE payment_method_id = ?");
    $stmt->execute([$method_code, $method_name, $id]);
    header("Location: payment-methods.php");
    exit;
}

// Xử lý xóa
if (isset($_GET['delete_id'])) {
    $deleteId = (int)$_GET['delete_id'];
    $pdo->prepare("DELETE FROM payment_methods WHERE payment_method_id = ?")->execute([$deleteId]);
}

// Lấy danh sách
try {
    $stmt = $pdo->query("SELECT * FROM payment_methods ORDER BY payment_method_id ASC");
    $paymentMethods = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Lỗi truy vấn: " . $e->getMessage());
}

// Nếu muốn sửa, lấy bản ghi cần sửa
$editMethod = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM payment_methods WHERE payment_method_id = ?");
    $stmt->execute([$id]);
    $editMethod = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<div class="container-fluid">
    <div class="row">
        <h3 class="mb-3">Phương thức thanh toán</h3>

     <form method="POST" class="mb-4">
        <?php if ($editMethod): ?>
            <input type="hidden" name="payment_method_id" value="<?= $editMethod['payment_method_id'] ?>">
        <?php endif; ?>

        <div class="mb-3">
            <label>Mã phương thức</label>
            <input type="text" name="method_code" class="form-control" value="<?= htmlspecialchars($editMethod['method_code'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label>Tên phương thức</label>
            <input type="text" name="method_name" class="form-control" value="<?= htmlspecialchars($editMethod['method_name'] ?? '') ?>" required>
        </div>
        <button type="submit" name="<?= $editMethod ? 'update' : 'add' ?>" class="btn btn-<?= $editMethod ? 'primary' : 'success' ?>">
            <?= $editMethod ? 'Cập nhật' : 'Thêm mới' ?>
        </button>
        <?php if ($editMethod): ?>
            <a href="manage_payment_methods.php" class="btn btn-secondary">Hủy</a>
        <?php endif; ?>
    </form>

    <!-- Danh sách phương thức -->
    <table class="table table-bordered table-striped">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Mã</th>
                <th>Tên hiển thị</th>
                <th>Ngày tạo</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($paymentMethods as $method): ?>
            <tr>
                <td><?= $method['payment_method_id'] ?></td>
                <td><?= htmlspecialchars($method['method_code']) ?></td>
                <td><?= htmlspecialchars($method['method_name']) ?></td>
                <td><?= date('d/m/Y H:i', strtotime($method['created_at'])) ?></td>
                <td>
                    <a href="?edit=<?= $method['payment_method_id'] ?>" class="btn btn-sm btn-warning">Sửa</a>
                    <a href="?delete_id=<?= $method['payment_method_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa phương thức này?')">Xóa</a>
                </td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>
    </div>
</div>


<?php require_once BASE_PATH . '/admin/partials/footer.php'; ?>