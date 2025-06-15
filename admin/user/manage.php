<?php
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/admin/partials/header.php';
require_once BASE_PATH . '/admin/partials/sidebar.php';

$stmt = $pdo->query("SELECT user_id, fullname, email, role, username, created_at, status FROM users ORDER BY user_id DESC");
$users = $stmt->fetchAll();

$user_id_to_block = null;

// Kiểm tra xem user_id có được truyền qua URL không
if (isset($_GET['user_id']) && is_numeric($_GET['user_id'])) {
    $user_id_to_block = (int)$_GET['user_id']; // Chuyển đổi sang số nguyên để đảm bảo an toàn

    // Truy vấn thông tin người dùng từ CSDL
    try {
        // Kiểm tra nếu email mới khác với email hiện tại của người dùng
        $stmtStatus = $pdo->prepare("SELECT status FROM users WHERE user_id = ?");
        $stmtStatus->execute([$user_id_to_block]);
        $statusCheck = $stmtStatus->fetch(PDO::FETCH_ASSOC);

        if ($statusCheck['status'] == 'actived') {
            $stmt_block = $pdo->prepare("UPDATE users SET status = ? WHERE user_id = ?");
            $stmt_block->execute(['blocked', $user_id_to_block]);

            $_SESSION['flash_message'] = [
                'type' => 'success', // Loại thông báo: success, danger, warning, info
                'message' => 'Block user successful !'
            ];
        }else{
            $stmt_block = $pdo->prepare("UPDATE users SET status = ? WHERE user_id = ?");
            $stmt_block->execute(['actived', $user_id_to_block]);

            $_SESSION['flash_message'] = [
                'type' => 'success', // Loại thông báo: success, danger, warning, info
                'message' => 'Active user successful !'
            ];
        }

        $redirect_url = BASE_URL . '/admin/user/manage.php';
        header("Location: $redirect_url");
        exit;
    } catch (PDOException $e) {
        $_SESSION['flash_message'] = [
            'type' => 'danger',
            'message' => 'Lỗi cơ sở dữ liệu: ' . $e->getMessage()
        ];
        header("Location: " . BASE_URL . "/admin/user/manage.php");
        exit();
    }
}

?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Danh sách Người dùng</h3>
        <a href="<?php echo BASE_URL; ?>/admin/user/add.php" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Thêm người dùng mới
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Thông tin người dùng</span>
            <form class="d-flex" role="search">
                <input class="form-control me-2" type="search" placeholder="Tìm kiếm..." aria-label="Search">
                <button class="btn btn-outline-success" type="submit">Tìm</button>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Full Name</th>
                            <th scope="col">User Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Role</th>
                            <th scope="col">Created At</th>
                            <th scope="col">Status</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($users)): ?>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= htmlspecialchars($user['user_id']) ?></td>
                                <td><?= htmlspecialchars($user['fullname']) ?></td>
                                <td><?= htmlspecialchars($user['username']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td>
                                    <select class="form-select form-select-sm role-select"
                                            data-user-id="<?= htmlspecialchars($user['user_id']) ?>"
                                            data-current-role="<?= htmlspecialchars($user['role']) ?>"
                                            data-url="<?= BASE_URL ?>/admin/user/update-role.php">
                                        <option value="admin" <?= ($user['role'] === 'admin') ? 'selected' : '' ?>>Admin</option>
                                        <option value="user" <?= ($user['role'] === 'user') ? 'selected' : '' ?>>User</option>
                                    </select>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($user['created_at'])) ?></td>
                                <td>
                                    <?php
                                        $badge_class = '';
                                        switch ($user['status']) {
                                            case 'blocked': $badge_class = 'bg-danger'; break;
                                            case 'actived': $badge_class = 'bg-success'; break;
                                            default: $badge_class = 'bg-secondary'; break;
                                        }
                                    ?>
                                    <span class="badge <?= $badge_class ?>"><?= htmlspecialchars($user['status']) ?></span>
                                </td>
                                <td>
                                    <a href="<?php echo BASE_URL; ?>/admin/user/edit.php?user_id=<?php echo htmlspecialchars($user['user_id']); ?>" class="btn btn-sm btn-primary me-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="#" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                    <?php if ($user['status'] == 'actived'): ?>
                                        <a href="<?php echo BASE_URL; ?>/admin/user/manage.php?user_id=<?php echo htmlspecialchars($user['user_id']); ?>" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="Block">
                                            <i class="fas fa-user-lock"></i>
                                        </a>
                                    <?php else: ?>
                                        <a href="<?php echo BASE_URL; ?>/admin/user/manage.php?user_id=<?php echo htmlspecialchars($user['user_id']); ?>" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="Active">
                                            <i class="fas fa-user-check"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">Không có người dùng nào để hiển thị.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelects = document.querySelectorAll('.role-select');

        roleSelects.forEach(selectElement => {
            selectElement.addEventListener('change', function() {
                const userId = this.dataset.userId;
                const newRole = this.value; // Lấy giá trị vai trò mới được chọn
                const updateUrl = this.dataset.url;

                // Lưu trữ vai trò cũ để rollback nếu có lỗi
                const oldRole = this.dataset.currentRole;
                // Cập nhật data-current-role ngay lập tức (UI optimistic update)
                this.dataset.currentRole = newRole;

                // Gửi yêu cầu AJAX
                fetch(updateUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    // Chuyển dữ liệu thành chuỗi URL-encoded
                    body: `user_id=${encodeURIComponent(userId)}&new_role=${encodeURIComponent(newRole)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showFlashMessage('success', data.message);
                    } else {
                        showFlashMessage('danger', data.message);
                        // Rollback UI về vai trò cũ nếu có lỗi
                        this.value = oldRole;
                        this.dataset.currentRole = oldRole;
                    }
                })
                .catch(error => {
                    console.error('Lỗi khi cập nhật vai trò:', error);
                    showFlashMessage('danger', 'Đã xảy ra lỗi hệ thống. Vui lòng thử lại.');
                    // Rollback UI về vai trò cũ nếu có lỗi mạng
                    this.value = oldRole;
                    this.dataset.currentRole = oldRole;
                });
            });
        });
    });
</script>
<?php require_once BASE_PATH . '/admin/partials/footer.php'; ?>