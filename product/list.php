<?php
// /user-management/index.php
require_once __DIR__ . '/../config.php';
require_once BASE_PATH . '/includes/header.php';

// Lấy danh sách thương hiệu
$sqlbrands = "SELECT * FROM brands ORDER BY brand_id DESC";
$stmtbra = $pdo->prepare($sqlbrands);
$stmtbra->execute();
$brands = $stmtbra->fetchAll(PDO::FETCH_ASSOC);

// Lấy danh sách danh mục sản phẩm
$sqlcategories = "SELECT * FROM categories ORDER BY category_id DESC";
$stmtcategories = $pdo->prepare($sqlcategories);
$stmtcategories->execute();
$categories = $stmtcategories->fetchAll(PDO::FETCH_ASSOC);

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
    GROUP BY p.product_id
    ORDER BY p.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Lọc sản phẩm
$filtered = [];

foreach ($products as $product) {
    $show = true;

    // Lọc theo hãng
    if (!empty($_GET['brand']) && is_array($_GET['brand'])) {
        $show &= in_array($product['brand_id'], $_GET['brand']);
    }

    // Lọc theo danh mục
    if (!empty($_GET['category']) && is_array($_GET['category'])) {
        $show &= in_array($product['category_id'], $_GET['category']);
    }
    // Lọc theo đánh giá (sao)
    if (!empty($_GET['rating'])) {
        $show &= ((int)$product['review'] == (int)$_GET['rating']);
    }

    // Lọc theo khoảng giá
    if (!empty($_GET['price']) && is_array($_GET['price'])) {
        $match_price = false;
        foreach ($_GET['price'] as $range) {
            [$min, $max] = explode('-', $range);
            if ($product['price'] >= $min && $product['price'] <= $max) {
                $match_price = true;
                break;
            }
        }
        $show &= $match_price;
    }

    if ($show) $filtered[] = $product;
}
// List giá
$price_ranges = [
    '0-10000000' => 'Dưới 10 triệu',
    '10000000-15000000' => '10 - 15 triệu',
    '15000000-20000000' => '15 - 20 triệu',
    '20000000-100000000' => 'Trên 20 triệu'
];
// List giá
$list_review = [
    '1' => '&#9733;',
    '2' => '&#9733;',
    '3' => '&#9733;',
    '4' => '&#9733;',
    '5' => '&#9733;'
];
?>
<style>
    .div_breadcrumb{
        margin-top: 80px; 
    }
    .product-card img {
        transition: transform 0.3s ease;
        height: 160px;
        object-fit: contain;
    }
    .product-card:hover img {
        transform: scale(1.05);
    }
    .product-card {
        border: 1px solid #EEEEEE;
    }
</style>
<style>
  .star-rating .star {
    font-size: 2rem;
    color: lightgray;      /* làm mờ */
    cursor: pointer;
    transition: color 0.2s;
  }

  .star-rating .star.selected {
    color: gold;           /* in đậm khi chọn */
  }
</style>
<!-- Breadcrumb -->
<nav class="container" aria-label="breadcrumb">
    <div class="row div_breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?php echo BASE_URL; ?>/index.php" class="text-muted text-decoration-none">Home</a>
            </li>
            <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">
                List products
            </li>
        </ol>
    </div>
</nav>
<section id="featured-products" class="bg-light">
    <div class="container p-4 bg-white">
        <div class="row">
            <!-- Bộ lọc -->
            <div class="col-md-3 border-end border-1">
                <form method="get">
                    <h5>Price range</h5>
                    <?php foreach ($price_ranges as $key => $label): ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                   name="price[]"
                                   value="<?= $key ?>"
                                   id="price_<?= $key ?>"
                                   <?= (!empty($_GET['price']) && in_array($key, $_GET['price'])) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="price_<?= $key ?>"><?= $label ?></label>
                        </div>
                    <?php endforeach; ?>
                    <hr>
                    <h5>BRANDS</h5>
                    <?php foreach ($brands as $brand): ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                   name="brand[]"
                                   value="<?= $brand['brand_id'] ?>"
                                   id="brand_<?= $brand['brand_id'] ?>"
                                   <?= (!empty($_GET['brand']) && in_array($brand['brand_id'], $_GET['brand'])) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="brand_<?= $brand['brand_id'] ?>"><?= $brand['name'] ?></label>
                        </div>
                    <?php endforeach; ?>
                    <hr>
                    <h5 class="mt-3">CATEGORIES</h5>
                    <?php foreach ($categories as $category): ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                   name="category[]"
                                   value="<?= $category['category_id'] ?>"
                                   id="category_<?= $category['category_id'] ?>"
                                   <?= (!empty($_GET['category']) && in_array($category['category_id'], $_GET['category'])) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="category_<?= $category['category_id'] ?>"><?= $category['name'] ?></label>
                        </div>
                    <?php endforeach; ?>
                    <hr>
                    <h5>Review</h5>
                    <input type="hidden" name="rating" id="rating-value" value="<?= htmlspecialchars($_GET['rating'] ?? '') ?>">

                    <div class="star-rating">
                        <?php 
                        $selectedRating = isset($_GET['rating']) ? (int)$_GET['rating'] : 0;
                        foreach ($list_review as $key => $label): 
                        ?>
                            <span 
                                data-value="<?= $key ?>" 
                                class="star <?= ($key <= $selectedRating) ? 'selected' : '' ?>">
                                &#9733;
                            </span>
                        <?php endforeach; ?>
                    </div>

                    

                    <button type="submit" class="btn btn-primary w-100 mt-3">Filter</button>
                </form>
            </div>

            <!-- Sản phẩm -->
            <div class="col-md-9">
                <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-0">
                    <?php if (empty($filtered)): ?>
                        <div class="col-12">
                            <div class="alert alert-warning w-100">No matching products</div>
                        </div>
                    <?php endif; ?>
                    <?php foreach ($filtered as $product): ?>
                        <div class="col">
                            <div class="p-3 h-100 product-card">
                                <a href="<?php echo BASE_URL; ?>/product/details.php?id=<?= $product['product_id'] ?>">
                                    <img src="<?= $product['image_url'] ?>" class="card-img-top" alt="<?= $product['name'] ?>">
                                </a>
                                <div class="card-body text-center d-flex flex-column">
                                    <h6 class="card-title">
                                        <a class="text-decoration-none text-dark" href="<?php echo BASE_URL; ?>/product/details.php?id=<?= $product['product_id'] ?>">
                                            <?= $product['name'] ?>
                                        </a>
                                    </h6>
                                    <div class="mt-auto">
                                        <p class="mb-1 text-success fw-bold fs-5">
                                            $<?= number_format($product['price']) ?>
                                        </p>
                                        <?php if (!empty($product['old_price']) && $product['old_price'] > $product['price']): ?>
                                            <p class="mb-1 text-muted fst-italic">
                                                <del>$<?= number_format($product['old_price']) ?></del>
                                                <span class="badge bg-danger">
                                                    -<?= round(100 * ($product['old_price'] - $product['price']) / $product['old_price']) ?>%
                                                </span>
                                            </p>
                                        <?php endif; ?>
                                        <!-- Add to Cart -->
                                        <div class="d-flex align-items-center justify-content-center gap-2">
                                            <!-- Form Add to Cart -->
                                            <form action="<?= BASE_URL ?>/product/add-to-cart.php" method="POST" class="d-flex align-items-center">
                                                <input type="hidden" name="action" value="list">
                                                <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                                                <input type="number" name="quantity" value="1" min="1" max="<?= $product['quantity'] ?>" class="form-control form-control-sm me-2" style="width: 80px;">
                                                <button type="submit" title="Add to cart" class="btn btn-outline-danger btn-sm rounded-circle">
                                                    <i class="bi bi-plus"></i>
                                                </button>
                                            </form>

                                            <!-- Form Add to Favorite -->
                                            <?php if (isset($_SESSION['user_id'])): ?>
                                                <form action="<?= BASE_URL ?>/product/add-to-favorite.php" method="POST" class="d-flex align-items-center">
                                                    <input type="hidden" name="action" value="list">
                                                    <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                                                    <button type="submit" title="Thêm vào yêu thích" class="btn btn-sm btn-warning ms-2">
                                                        ❤️
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                        <a href="<?= BASE_URL ?>/product/compare-add.php?id=<?= $product['product_id'] ?>" class="">So sánh chi tiết ></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const stars = document.querySelectorAll(".star-rating .star");
    const input = document.getElementById("rating-value");

    function updateStars(rating) {
        stars.forEach(star => {
            const val = parseInt(star.getAttribute("data-value"));
            if (val <= rating) {
                star.classList.add("selected");
            } else {
                star.classList.remove("selected");
            }
        });
    }

    stars.forEach(star => {
        star.addEventListener("click", function () {
            const selectedValue = parseInt(this.getAttribute("data-value"));
            input.value = selectedValue;
            updateStars(selectedValue);
        });
    });

    // Khởi tạo trạng thái khi load lại trang
    const initial = parseInt(input.value);
    if (initial) {
        updateStars(initial);
    }
});
</script>

<?php require_once BASE_PATH . '/includes/footer.php'; ?>