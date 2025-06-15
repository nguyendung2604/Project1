<?php
require_once __DIR__ . '/../config.php';
require_once BASE_PATH . '/admin/partials/header.php';
require_once BASE_PATH . '/admin/partials/sidebar.php';


// Lấy thống kê từ CSDL
$stmt = $pdo->query("
    SELECT 
        (SELECT COUNT(*) FROM products) AS total_products,
        (SELECT COUNT(*) FROM brands) AS total_brands,
        (SELECT COUNT(*) FROM categories) AS total_categories,
        (SELECT COUNT(*) FROM customers) AS total_customers,
        (SELECT COUNT(*) FROM users) AS total_users
");
$stats = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<div class="container-fluid mt-4">
    <div class="row">
    <div class="col-md-2 mb-3">
        <div class="card bg-primary text-white shadow-sm">
            <div class="card-body">
                <h6 class="card-title">Sản phẩm</h6>
                <p class="fs-4"><?= $stats['total_products'] ?></p>
            </div>
        </div>
    </div>

    <div class="col-md-2 mb-3">
        <div class="card bg-success text-white shadow-sm">
            <div class="card-body">
                <h6 class="card-title">Thương hiệu</h6>
                <p class="fs-4"><?= $stats['total_brands'] ?></p>
            </div>
        </div>
    </div>

    <div class="col-md-2 mb-3">
        <div class="card bg-info text-white shadow-sm">
            <div class="card-body">
                <h6 class="card-title">Danh mục</h6>
                <p class="fs-4"><?= $stats['total_categories'] ?></p>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card bg-warning text-white shadow-sm">
            <div class="card-body">
                <h6 class="card-title">Khách hàng</h6>
                <p class="fs-4"><?= $stats['total_customers'] ?></p>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card bg-dark text-white shadow-sm">
            <div class="card-body">
                <h6 class="card-title">Người dùng</h6>
                <p class="fs-4"><?= $stats['total_users'] ?></p>
            </div>
        </div>
    </div>
</div>
</div>

<?php require_once BASE_PATH . '/admin/partials/footer.php'; ?>