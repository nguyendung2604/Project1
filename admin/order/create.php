<?php
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/admin/partials/header.php';
require_once BASE_PATH . '/admin/partials/sidebar.php';

// Lấy danh sách khách hàng và sản phẩm
$customers = $pdo->query("SELECT customer_id, name FROM customers ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$products = $pdo->query("SELECT product_id, name, price FROM products ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid">
    <div class="row">
        <h2 class="mb-4">Tạo đơn hàng mới</h2>

        <form method="POST" action="store.php">
            <div class="mb-3">
                <label for="customer_ids" class="form-label">Chọn khách hàng</label>
                <select name="customer_ids[]" id="customer_ids" class="form-select" multiple required>
                    <?php foreach ($customers as $customer): ?>
                        <option value="<?= $customer['customer_id'] ?>"><?= htmlspecialchars($customer['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <small class="text-muted">Giữ Ctrl để chọn nhiều khách hàng</small>
            </div>

            <h5>Sản phẩm</h5>
            <div id="product-list">
                <div class="row mb-2">
                    <div class="col-md-6">
                        <select name="products[]" class="form-select" required>
                            <option value="">-- Chọn sản phẩm --</option>
                            <?php foreach ($products as $p): ?>
                                <option value="<?= $p['product_id'] ?>"><?= htmlspecialchars($p['name']) ?> ($<?= number_format($p['price']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="number" name="quantities[]" class="form-control" min="1" value="1" required>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger remove-product">X</button>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Giảm giá (VNĐ hoặc %):</label>
                <input type="text" name="discount" class="form-control" placeholder="VD: 50000 hoặc 10%">
            </div>

            <div class="mb-3">
                <label class="form-label">Ghi chú đơn hàng:</label>
                <textarea name="note" class="form-control" rows="3" placeholder="Nhập ghi chú..."></textarea>
            </div>

            <button type="button" id="add-product" class="btn btn-secondary mb-3">+ Thêm sản phẩm</button>

            <div class="mb-3">
                <button type="submit" class="btn btn-success">Tạo đơn hàng</button>
                <a href="orders.php" class="btn btn-outline-secondary">Quay lại</a>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('add-product').addEventListener('click', () => {
    const list = document.getElementById('product-list');
    const firstRow = list.querySelector('.row');
    const clone = firstRow.cloneNode(true);
    clone.querySelectorAll('select, input').forEach(el => el.value = '');
    list.appendChild(clone);
});

document.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-product')) {
        const row = e.target.closest('.row');
        if (document.querySelectorAll('#product-list .row').length > 1) {
            row.remove();
        }
    }
});
</script>

<?php require_once BASE_PATH . '/admin/partials/footer.php'; ?>
