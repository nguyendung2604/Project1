<?php
// /user-management/config.php (Phiên bản cuối cùng, ổn định cho virtual host)

// Bắt đầu Session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Định nghĩa đường dẫn thư mục gốc của dự án (vẫn giữ nguyên)
define('BASE_PATH', __DIR__);

// Tự động định nghĩa URL gốc, hoạt động với mọi domain
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
$host = $_SERVER['HTTP_HOST']; // Sẽ tự động lấy 'totnghiep.local'

// **ĐÂY LÀ DÒNG THAY ĐỔI QUAN TRỌNG NHẤT**
// Vì virtual host đã trỏ vào thư mục gốc, BASE_URL chỉ cần là protocol + host.
define('BASE_URL', "$protocol://$host");

// Thiết lập kết nối CSDL
$db_host = 'localhost';
$db_name = 'project';
$db_user = 'root';
$db_pass = '';
$db_port = '3307';

try {
    $pdo = new PDO("mysql:host=$db_host;port=$db_port;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Lỗi kết nối CSDL: " . $e->getMessage());
}

function get_product_by_id($id) {
    // Kết nối CSDL
    //require_once __DIR__ . '/../config.php'; // chứa kết nối PDO $pdo

    // global $pdo;

    $stmtProdcut = $pdo->prepare("SELECT product_id, name, price, image_url FROM products WHERE product_id = :id LIMIT 1");
    $stmtProdcut->execute(['id' => $id]);
    return $stmtProdcut->fetch(PDO::FETCH_ASSOC);
}
?>