<?php
require_once __DIR__ . '/../../../config.php';

$id = $_GET['id'] ?? null;
$action = $_GET['action'] ?? '';

if (!$id || !in_array($action, ['approve', 'reject'])) {
    die('Yêu cầu không hợp lệ.');
}

$newStatus = $action === 'approve' ? 'approved' : 'rejected';

$stmt = $pdo->prepare("UPDATE return_requests SET status = ? WHERE return_id = ?");
$stmt->execute([$newStatus, $id]);

header('Location: list-return.php');
exit;
?>
