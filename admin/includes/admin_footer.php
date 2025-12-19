<?php
// admin/includes/admin_footer.php
?>
</div>
</div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tìm phần tử thông báo (alert)
        const alertElement = document.querySelector('.dashboard-content > .alert');

        if (alertElement) {
            // Sau 5 giây (5000ms), thêm class để ẩn dần hoặc trực tiếp ẩn đi
            setTimeout(function() {
                // Thêm một transition opacity (cần CSS cho transition)
                alertElement.style.transition = 'opacity 0.5s ease-out';
                alertElement.style.opacity = '0';

                // Sau khi ẩn dần, xóa phần tử khỏi DOM
                setTimeout(function() {
                    alertElement.style.display = 'none';
                }, 500);

            }, 5000); // Ẩn sau 5 giây
        }
    });
    $(document).ready(function() {
        // 1. Ẩn tất cả submenu theo mặc định, trừ khi mục cha đang active
        $('.sidebar-menu .submenu').each(function() {
            if (!$(this).parent().hasClass('active')) {
                $(this).hide();
            }
        });

        // 2. Xử lý sự kiện click vào mục cha
        $('.sidebar-menu .menu-toggle').on('click', function(e) {
            e.preventDefault();

            var submenu = $(this).siblings('.submenu');

            // Đóng tất cả các submenu khác đang mở
            $('.sidebar-menu .submenu').not(submenu).slideUp(200).parent().removeClass('active');

            // Mở/đóng submenu hiện tại và thêm/bỏ class active cho mục cha
            submenu.slideToggle(200);
            $(this).parent().toggleClass('active');
        });
    });
</script>
</body>

</html>