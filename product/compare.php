<?php
require_once __DIR__ . '/../config.php';
require_once BASE_PATH . '/includes/header.php';

// Lấy danh sách sản phẩm cần so sánh
$productIds = $_SESSION['compare'] ?? [];

if (!empty($productIds)) {
    // Tạo placeholders cho câu lệnh SQL
    $placeholders = implode(',', array_fill(0, count($productIds), '?'));

    $sql = "
        SELECT 
            p.product_id, 
            p.name, 
            p.price, 
            pi.image_url,
            pa.ram, 
            pa.cpu, 
            pa.screen_size, 
            pa.color
        FROM products p
        LEFT JOIN product_attributes pa ON p.product_id = pa.product_id
        LEFT JOIN product_images pi ON p.product_id = pi.product_id
        WHERE p.product_id IN ($placeholders)
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array_values($productIds));
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$sqlList = "SELECT 
        p.product_id, 
        p.name, 
        p.price, 
        pi.image_url
    FROM products p
    LEFT JOIN product_images pi ON p.product_id = pi.product_id
    LIMIT 20";
$stmtList = $pdo->query($sqlList);
$productsList = $stmtList->fetchAll(PDO::FETCH_ASSOC);

// Lấy danh sách đã so sánh (nếu có)
$compareList = $_SESSION['compare'] ?? [];
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
                So sánh sản phẩm
            </li>
        </ol>
    </div>
</nav>
<section class="container mb-4">
    <div class="row p-4 bg-white">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">So sánh sản phẩm</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal">
                + Thêm sản phẩm để so sánh
            </button>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Chọn sản phẩm để thêm vào so sánh</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Ảnh</th>
                                        <th>Tên sản phẩm</th>
                                        <th>Giá</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($productsList as $product): ?>
                                        <?php
                                            $imageUrl = $product['image_url'] ?? BASE_URL . '/assets/no-image.png';
                                        ?>
                                        <tr>
                                            <td style="width: 50px;">
                                                <img src="<?= htmlspecialchars($imageUrl) ?>" alt="<?= htmlspecialchars($product['name']) ?>" 
                                                     style="height: 80px; object-fit: contain;">
                                            </td>
                                            <td><?= htmlspecialchars($product['name']) ?></td>
                                            <td class="text-danger fw-bold">$<?= number_format($product['price']) ?></td>
                                            <td>
                                                <?php if (in_array($product['product_id'], $compareList)): ?>
                                                    <button class="btn btn-secondary w-100" disabled>Đã thêm</button>
                                                <?php else: ?>
                                                    <a href="<?= BASE_URL ?>/product/compare-add.php?id=<?= $product['product_id'] ?>" class="btn btn-success w-100">
                                                        + Thêm vào so sánh
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <?php if (!empty($products)): ?>
                <table class="table table-bordered text-center align-middle">
                    <thead class="table-danger">
                        <tr>
                            <th>Thông tin</th>
                            <?php foreach ($products as $product): ?>
                                <th>
                                    <img src="<?= $product['image_url'] ? htmlspecialchars($product['image_url']) : BASE_URL . '/assets/no-image.png' ?>" 
                                         alt="<?= htmlspecialchars($product['name']) ?>" 
                                         width="100" height="110" 
                                         style="object-fit: contain; background-color: #f9f9f9;"><br>
                                    <strong><?= htmlspecialchars($product['name']) ?></strong><br>
                                    <a href="<?= BASE_URL ?>/product/compare-remove.php?id=<?= $product['product_id'] ?>" 
                                       class="btn btn-sm btn-outline-danger mt-2">Xóa</a>
                                </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>Giá</th>
                            <?php foreach ($products as $product): ?>
                                <td class="text-danger fw-bold">$<?= number_format($product['price']) ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <th>RAM</th>
                            <?php foreach ($products as $product): ?>
                                <td><?= htmlspecialchars($product['ram']) ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <th>CPU</th>
                            <?php foreach ($products as $product): ?>
                                <td><?= htmlspecialchars($product['cpu']) ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <th>Màn hình</th>
                            <?php foreach ($products as $product): ?>
                                <td><?= htmlspecialchars($product['screen_size']) ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <th>Màu sắc</th>
                            <?php foreach ($products as $product): ?>
                                <td><?= htmlspecialchars($product['color']) ?></td>
                            <?php endforeach; ?>
                        </tr>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-warning">Chưa thêm sản phẩm để so sánh.</div>
            <?php endif; ?>
        </div>

        <div class="text-center mt-4">
            <a href="javascript:history.back()" class="btn btn-secondary">← Quay lại</a>
        </div>
    </div>
</section>

<?php require_once BASE_PATH . '/includes/footer.php'; ?>