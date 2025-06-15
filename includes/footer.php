<?php
    // Lấy danh sách thương hiệu
    $sqlbrands6 = "SELECT * FROM brands ORDER BY brand_id DESC LIMIT 6";
    $stmtbra6 = $pdo->prepare($sqlbrands6);
    $stmtbra6->execute();
    $brands6 = $stmtbra6->fetchAll(PDO::FETCH_ASSOC);
?>
    <!-- Footer -->
    <footer class="bg-dark text-white pt-5 pb-3">
        <div class="container">
            <div class="row g-4 mb-5">
                <div class="col-lg-3 col-md-6">
                    <a href="#" class="brand-font text-white text-decoration-none h4 d-block mb-3">Wireless World</a>
                    <p class="text-muted mb-4">Your ultimate destination for smartphone comparison and discovery. Find the perfect phone that matches your needs and budget.</p>
                    <div class="d-flex gap-3">
                        <a href="#" class="social-icon">
                            <i class="bi-facebook fs-5"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="bi-twitter-x fs-5"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="bi-instagram fs-5"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="bi-youtube fs-5"></i>
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <h3 class="h5 fw-semibold mb-3">Quick Links</h3>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Home</a></li>
                        <li class="mb-2"><a href="#brands" class="text-muted text-decoration-none">Browse Brands</a></li>
                        <li class="mb-2"><a href="#compare" class="text-muted text-decoration-none">Compare Phones</a></li>
                        <li class="mb-2"><a href="#deals" class="text-muted text-decoration-none">Deals & Offers</a></li>
                        <li class="mb-2"><a href="#about" class="text-muted text-decoration-none">About Us</a></li>
                        <li class="mb-2"><a href="#contact" class="text-muted text-decoration-none">Contact</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <h3 class="h5 fw-semibold mb-3">Top Brands</h3>
                    <ul class="list-unstyled">
                        <?php foreach ($brands6 as $brand): ?>
                            <li class="mb-2"><a href="#" class="text-muted text-decoration-none"><?= htmlspecialchars($brand['name']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <h3 class="h5 fw-semibold mb-3">Support</h3>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Help Center</a></li>
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Privacy Policy</a></li>
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Terms of Service</a></li>
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Cookie Policy</a></li>
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Sitemap</a></li>
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Accessibility</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="border-top border-secondary pt-4">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <p class="text-muted small mb-3 mb-md-0">© 2025 MobileMaster. All rights reserved.</p>
                    </div>
                    
                    <div class="col-md-6 text-md-end">
                        <div class="d-flex align-items-center justify-content-md-end gap-3">
                            <span class="text-muted small">Payment Methods:</span>
                            <div class="d-flex gap-2">
                                <i class="bi-credit-card-2-front fs-5"></i>
                                <i class="bi-paypal fs-5"></i>
                                <i class="bi-apple fs-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>


    <script src="<?php echo BASE_URL; ?>/assets/js/script.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/slider-brand.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>

    <script>
        document.getElementById("search-box").addEventListener("input", function () {
            const query = this.value.trim();
            const suggestionBox = document.getElementById("search-suggestions");

            if (query.length < 2) {
                suggestionBox.style.display = "none";
                return;
            }

            fetch("search.php?q=" + encodeURIComponent(query), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    suggestionBox.innerHTML = "";
                    if (data.length > 0) {
                        suggestionBox.innerHTML = `
                            <div class="fw-bold text-danger mb-2">Kết quả tìm kiếm cho "<span class="text-success">${query}</span>"</div>
                        `;
                        data.forEach(p => {
                            suggestionBox.innerHTML += `
                                <div class="d-flex align-items-center mb-2 suggestion-item" style="cursor:pointer;" onclick="window.location.href='product_detail.php?id=${p.product_id}'">
                                    <img src="${p.image_url}" alt="${p.name}" class="me-2" style="width: 60px; height: 60px; object-fit: cover;">
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold text-primary">${p.name}</div>
                                        <div class="text-danger fw-bold">${Number(p.price).toLocaleString()}đ</div>
                                        ${p.old_price && p.old_price > p.price
                                            ? `<div class="text-muted"><del>${Number(p.old_price).toLocaleString()}đ</del></div>`
                                            : ""}
                                    </div>
                                </div>
                                <hr class="my-2">
                            `;
                        });
                        suggestionBox.style.display = "block";
                    } else {
                        suggestionBox.innerHTML = '<div class="text-muted">Không tìm thấy sản phẩm phù hợp</div>';
                        suggestionBox.style.display = "block";
                    }
                });
        });
    </script>

</body>
</html>