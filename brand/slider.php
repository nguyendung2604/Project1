<?php
    // Lấy danh sách thương hiệu
    $sqlbrands = "SELECT * FROM brands ORDER BY brand_id DESC";
    $stmtbra = $pdo->prepare($sqlbrands);
    $stmtbra->execute();
    $brands = $stmtbra->fetchAll(PDO::FETCH_ASSOC);
?>
<section id="brands" class="py-5 bg-white">
    <div class="container position-relative">
        <h2 class="h2 fw-bold text-center mb-5">Browse by Brand</h2>
        <!-- Khung slider hiển thị 4 mục -->
        <div class="overflow-hidden">
            <div id="brand-slider" class="d-flex flex-nowrap gap-3" style="width: 100%;">
                <!-- 1 mục All Brands -->
                <div class="brand-tab text-center d-flex flex-column align-items-center justify-content-center flex-shrink-0 px-2" style="width: 24%;">
                    <div class="brand-icon mb-2 d-flex align-items-center justify-content-center rounded-circle bg-light" style="width: 60px; height: 60px;">
                        <i class="bi bi-phone fs-4"></i>
                    </div>
                    <span class="small fw-medium">All Brands</span>
                </div>

                <!-- Các mục thương hiệu -->
                <?php foreach ($brands as $brand): ?>
                    <div class="brand-tab text-center d-flex flex-column align-items-center justify-content-center flex-shrink-0 px-2" style="width: 24%;">
                        <div class="brand-icon mb-2 d-flex align-items-center justify-content-center rounded-circle bg-light" style="width: 60px; height: 60px;">
                            <i class="bi bi-phone fs-4"></i>
                        </div>
                        <span class="small fw-medium"><?= htmlspecialchars($brand['name']) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>