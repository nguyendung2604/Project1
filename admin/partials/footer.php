    </div> 
    <footer>
        <div class="container">
            <p>&copy; 2024 Dashboard của bạn. Tất cả quyền được bảo lưu.</p>
        </div>
    </footer>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        document.getElementById('sidebar-toggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('collapsed');
            document.body.classList.toggle('sidebar-open');
        });
    </script>
    <script>
        function showFlashMessage(type, message) {
            let backgroundColor = '';
            if (type === 'success') {
                backgroundColor = 'linear-gradient(to right, #00b09b, #96c93d)';
            } else if (type === 'danger') {
                backgroundColor = 'linear-gradient(to right, #ff5f6d, #ffc371)';
            } else if (type === 'warning') {
                backgroundColor = 'linear-gradient(to right, #f7b731, #fbd786)';
            } else { // info
                backgroundColor = 'linear-gradient(to right, #2193b0, #6dd5ed)';
            }

            Toastify({
                text: message,
                duration: 5000, // 5 giây
                close: true,
                gravity: "top", // `top` or `bottom`
                position: "right", // `left`, `center` or `right`
                stopOnFocus: true, // Prevents dismissing of toast on hover
                style: {
                    background: backgroundColor,
                },
                onClick: function(){} // Callback after click
            }).showToast();
        }

        document.addEventListener('DOMContentLoaded', function() {
            // PHP để lấy thông báo từ session và chuyển thành JS object
            // Đảm bảo session_start() đã được gọi ở đầu file PHP của bạn
            <?php
                $flash_message = null;
                if (isset($_SESSION['flash_message'])) {
                    $flash_message = $_SESSION['flash_message'];
                    unset($_SESSION['flash_message']); // Xóa thông báo khỏi session sau khi đọc
                }
            ?>

            const flashMessageData = <?php echo json_encode($flash_message); ?>;

            if (flashMessageData && flashMessageData.message) {
                // Gọi hàm showFlashMessage với dữ liệu từ session
                showFlashMessage(flashMessageData.type, flashMessageData.message);
            }
        });
    </script>
</body>
</html>