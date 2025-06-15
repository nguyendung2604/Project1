<?php
require_once __DIR__ . '/../config.php';
require_once BASE_PATH . '/includes/header.php';

$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
var_dump($_GET);
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
                Thanh toan thành công
            </li>
        </ol>
    </div>
</nav>
<section class="container mb-4">
    <div class="row justify-content-center">
        
    </div>
</section>


<?php require_once BASE_PATH . '/includes/footer.php'; ?>