<?php
// Kết nối database
require 'db_connect.php';

try {
    // Truy vấn lấy danh sách sản phẩm với chỉ 1 ảnh đầu tiên
    $stmt = $pdo->prepare("
        SELECT p.product_id, p.name, p.description, p.price, p.quantity, 
               b.name AS brand_name, 
               (SELECT image_url FROM product_images WHERE product_id = p.product_id LIMIT 1) AS image_url
        FROM products p
        LEFT JOIN brands b ON p.brand_id = b.brand_id
        ORDER BY p.created_at DESC
    ");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Lỗi truy vấn: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Wireless World</title>
    <style>
        .product-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            padding: 20px;
        }
        .product-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            width: 200px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .product-card img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
        }
        .product-card h3 {
            font-size: 18px;
            margin: 10px 0;
        }
        .product-card p {
            margin: 5px 0;
            color: #555;
        }
        .price {
            font-weight: bold;
            color: #e44d26;
        }
        .stock {
            color: #28a745;
        }
        .out-of-stock {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <section>
        <h2>Browse Products</h2>
        <div class="product-container">
            <?php if (empty($products)): ?>
                <p>No products available.</p>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <?php if ($product['image_url']): ?>
                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <?php else: ?>
                            <img src="default-image.jpg" alt="No image available">
                        <?php endif; ?>
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p><strong>Brand:</strong> <?php echo htmlspecialchars($product['brand_name']); ?></p>
                        <p><?php echo htmlspecialchars($product['description']); ?></p>
                        <p class="price">$<?php echo number_format($product['price']); ?></p>
                        <p class="<?php echo $product['quantity'] > 0 ? 'stock' : 'out-of-stock'; ?>">
                            <?php echo $product['quantity'] > 0 ? 'In Stock: ' . $product['quantity'] : 'Out of Stock'; ?>
                        </p>
                        <a href="product_details.php?id=<?php echo $product['product_id']; ?>">View Details</a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</body>
</html>