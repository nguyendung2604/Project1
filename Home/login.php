<?php
require_once("db_connect.php");
?>
<?php

class Person {
    public $username;
    public $password;


    public function __construct($username, $password) {
        $this->username = $username;
        $this->password = $password;
    }

    public function check() {
        $db = new Database();
        $pdo = $db->connect();  
    
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
        $stmt->execute([$this->username,$this->password]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
        $db->close();
        return $result;
    }
}

 if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$username= $_POST['username'];  
$password= $_POST['password'];
$person1 = new Person($username,$password);

$user = $person1->check();

    if ($user) {
        // Khởi tạo session
        session_start();
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Kiểm tra quyền
        if ($user['role'] === 'admin') {
            header("Location: ../admin/index.php"); // Trang dành cho admin
            exit;
        } else {
            header("Location:index.php"); // Trang dành cho người dùng bình thường
            exit;
        }
    } else {
        echo "Invalid credentials.";
        exit;
    }
}



?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Wireless World</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Header (giống register) -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand brand-font text-primary me-4" href="index.php">Wireless World</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="d-flex align-items-center order-lg-last">
                <div class="position-relative me-3">
                    <input type="text" class="form-control pe-5 search-input" placeholder="Search phones...">
                    <div class="position-absolute top-50 end-0 translate-middle-y me-3">
                        <i class="bi bi-search text-muted"></i>
                    </div>
                </div>
                <a href="cart.php" class="btn btn-link text-dark p-2 me-2 nav-icon" aria-label="Shopping Cart">
                    <i class="bi bi-bag"></i>
                </a>
                <div class="dropdown">
                    <button class="btn btn-link text-dark p-2 me-2 nav-icon" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false" aria-label="User Profile">
                        <i class="bi bi-person-circle"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="login.php">Login</a></li>
                        <li><a class="dropdown-item" href="register.php">Register</a></li>
                    </ul>
                </div>
            </div>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link fw-medium" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link fw-medium" href="#brands">Brands</a></li>
                    <li class="nav-item"><a class="nav-link fw-medium" href="#brands">Category</a></li>
                    <li class="nav-item"><a class="nav-link fw-medium" href="#deals">Deals</a></li>
                    <li class="nav-item"><a class="nav-link fw-medium" href="#about">About Us</a></li>
                    <li class="nav-item"><a class="nav-link fw-medium" href="#contact">Contact</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div style="height: 80px;"></div>
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-4">
                <div class="text-center text-dark mb-4">
                    <h2>Login</h2>
                </div>
                
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <form action="Login.php" method="POST">

                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="username" placeholder="Username" name="username">
                            <label for="username" >Username</label>
                        </div>
                        
                        <div class="form-floating mb-3 position-relative">
                            <input type="password" class="form-control" id="password" placeholder="Password" name="password">
                            <label for="password">Password</label>
                            <button type="button" class="btn position-absolute top-50 end-0 translate-middle-y me-2 border-0 bg-transparent toggle-password">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>
                        
                       
                        <button class="btn btn-primary w-100 py-2 mb-3" type="submit">SIGN IN</button>
                        <div class="text-center">
                             <a href="register.php" class="text-decoration-none">Register</a> 
                            </div>
                               
                    </form>
                        
                      
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer (giống register) -->
    <footer class="bg-dark text-white pt-5 pb-3 mt-5">
        <div class="container">
            <div class="row g-4 mb-5">
                <div class="col-lg-3 col-md-6">
                    <a href="#" class="brand-font text-white text-decoration-none h4 d-block mb-3">Wireless World</a>
                    <p class="text-muted mb-4">Your ultimate destination for smartphone comparison and discovery. Find the perfect phone that matches your needs and budget.</p>
                    <div class="d-flex gap-3">
                        <a href="#" class="social-icon"><i class="bi-facebook fs-5"></i></a>
                        <a href="#" class="social-icon"><i class="bi-twitter-x fs-5"></i></a>
                        <a href="#" class="social-icon"><i class="bi-instagram fs-5"></i></a>
                        <a href="#" class="social-icon"><i class="bi-youtube fs-5"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h3 class="h5 fw-semibold mb-3">Quick Links</h3>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="index.php" class="text-muted text-decoration-none">Home</a></li>
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
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Samsung</a></li>
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Apple</a></li>
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Google</a></li>
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Xiaomi</a></li>
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Sony</a></li>
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">View All Brands</a></li>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>