<?php
require_once __DIR__ . '/config.php';

$query = $_GET['q'] ?? '';

$sql = "
	SELECT 
		products.product_id, 
		name, 
		price, 
		old_price, 
		image_url 
    FROM products 
    LEFT JOIN product_images ON products.product_id = product_images.product_id
    WHERE name LIKE :name
    ORDER BY name
    LIMIT 10";
header('Content-Type: application/json');

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['name' => '%' . $query . '%']);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($results);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>