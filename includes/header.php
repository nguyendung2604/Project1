<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wireless World</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/slider-brand.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/product.css">

</head>
<body>
 <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand brand-font text-primary me-4" href="#">Wireless World</a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="d-flex align-items-center order-lg-last">
                <!-- Search Bar -->
                <div class="position-relative me-3 w-100" style="max-width: 400px;">
                    <input type="text" id="search-box" class="form-control pe-5" placeholder="Search phones...">
                    <div class="position-absolute top-50 end-0 translate-middle-y me-3">
                        <i class="bi bi-search text-muted"></i>
                    </div>
                    <div id="search-suggestions" class="dropdown-menu w-100 shadow p-2" style="display:none; max-height: 400px; overflow-y: auto;">
                        <!-- Dữ liệu gợi ý sẽ được chèn ở đây -->
                    </div>
                </div>
                <?php
                    $cart_count = 0;

                    if (isset($_SESSION['user_id'])) {
                        // Nếu đã đăng nhập, lấy tổng số lượng sản phẩm từ cart_items
                        $stmt = $pdo->prepare("
                            SELECT SUM(ci.quantity) 
                            FROM carts c
                            JOIN cart_items ci ON c.cart_id = ci.cart_id
                            WHERE c.user_id = ?
                        ");
                        $stmt->execute([$_SESSION['user_id']]);
                        $cart_count = (int)$stmt->fetchColumn();
                    } else {
                        // Nếu chưa đăng nhập, lấy từ session
                        if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
                            foreach ($_SESSION['cart'] as $item) {
                                $cart_count += $item['quantity'];
                            }
                        }
                    }
                ?>
                <!-- Icons -->
                <a href="<?php echo BASE_URL; ?>/product/cart.php" class="btn position-relative">
                    <i class="bi bi-cart"></i>
                    <?php if ($cart_count > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?= $cart_count ?>
                        </span>
                    <?php endif; ?>
                </a>
                
                <!-- User Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-link text-dark p-2 me-2 nav-icon" type="button" id="userDropdown"
                            data-bs-toggle="dropdown" aria-expanded="false" aria-label="User Profile">
                        <i class="bi bi-person-circle"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <?php
                            // Kiểm tra xem người dùng đã đăng nhập hay chưa bằng cách kiểm tra biến session 'user_id'
                            if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
                                // Người dùng đã đăng nhập
                                ?>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/user/profile.php">Hello, <?php echo htmlspecialchars($_SESSION['user_fullname'] ?? 'Guest'); ?></a></li>
                                <?php if ($_SESSION['role'] == 'admin'): ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/admin/index.php">Administrator</a></li>
                                <?php elseif ($_SESSION['role'] == 'user'): ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/order/order-history.php">Lịch sử mua hàng & Thanh toán</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/user/favorite-list.php">Sản phẩm yêu thích 💖</a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/auth/logout.php">Logout</a></li>
                                <?php
                            } else {
                                // Người dùng chưa đăng nhập
                        ?>
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/auth/login.php"><i class="bi bi-person-circle"></i> Login</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/auth/register.php"><i class="bi bi-arrow-left-square-fill"></i> Register</a></li>
                        <?php
                            }
                        ?>
                    </ul>
                </div>
            </div>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link fw-medium <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-medium <?php echo (strpos($_SERVER['REQUEST_URI'], '#brands') !== false) ? 'active' : ''; ?>" href="#brands">Brands</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-medium <?php echo (basename($_SERVER['PHP_SELF']) == 'list.php' && strpos($_SERVER['REQUEST_URI'], '/product/') !== false) ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/product/list.php">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-medium <?php echo (strpos($_SERVER['REQUEST_URI'], '#deals') !== false) ? 'active' : ''; ?>" href="#deals">Deals</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-medium <?php echo (strpos($_SERVER['REQUEST_URI'], '#about') !== false) ? 'active' : ''; ?>" href="#about">About Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-medium <?php echo (strpos($_SERVER['REQUEST_URI'], '#contact') !== false) ? 'active' : ''; ?>" href="#contact">Contact</a>
                    </li>
                </ul>
            </div>

        </div>
    </nav>