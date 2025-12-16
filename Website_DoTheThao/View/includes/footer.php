<?php
// View/includes/footer.php 
// File này chỉ chứa Footer và các script JS cuối trang
?>
<footer class="main-footer">
    <div class="footer-container">
        <span id="gioithieu"></span>
        <div class="footer-column logo-info">


            <div class="contact-details">
                <p><strong><span class="icon-address"></span> Địa chỉ:</strong></p>
                <p>CÔNG TY CỔ PHẦN THỜI TRANG ALZADO</p>
                <p>GPKD số 0107773034 cấp ngày 27/03/2017 tại Sở kế hoạch và Đầu tư Thành phố Hà Nội</p>
                <p>Văn Phòng: 180 Cao Lỗ, TP Hồ Chí Minh</p>
                <p><strong><span class="icon-phone"></span> Số điện thoại:</strong> 0961621086</p>
                <p><strong><span class="icon-email"></span> Email:</strong> sport@gmail.com</p>
            </div>
        </div>

        <div class="footer-column company-info">
            <h3>THÔNG TIN CÔNG TY</h3>
            <ul>
                <li><a href="#">Giới Thiệu</a></li>
                <li><a href="#">Công Nghệ Nổi Bật</a></li>
                <li><a href="#">Liên Hệ</a></li>
                <li><a href="#">Hệ Thống Cửa Hàng</a></li>
            </ul>
        </div>

        <div class="footer-column policies">
            <h3>CHÍNH SÁCH</h3>
            <ul>
                <li><a href="#">Chính sách bảo mật</a></li>
                <li><a href="#">Chính sách Vận Chuyển & Giao Hàng</a></li>
                <li><a href="#">Chính sách kiểm hàng</a></li>
                <li><a href="#">Chính sách thanh toán</a></li>
                <li><a href="#">Chính sách đổi trả</a></li>
            </ul>
        </div>

        <div class="footer-column newsletter-social">
            <h3>VỀ CHÚNG TÔI</h3>


            <div class="social-icons">
                <a href="#" class="social-icon facebook">F</a>
                <a href="#" class="social-icon youtube-small">Y</a>
                <a href="#" class="social-icon instagram">I</a>
                <a href="#" class="social-icon tiktok">T</a>
            </div>
        </div>



    </div>
</footer>

<div id="fb-root"></div>
<div id="fb-customer-chat" class="fb-customerchat"></div>

</body>
<script>
    $(document).ready(function() {
        $('#search-icon-toggle').on('click', function(e) {
            e.preventDefault();

            var $searchBar = $('#search-bar-dropdown');
            var $searchIcon = $(this);

            $searchBar.toggleClass('active');

            // Tùy chọn: Nếu bạn muốn ẩn icon kính lúp sau khi bấm, bạn có thể thêm logic ở đây.
            // $searchIcon.toggle(); 

            if ($searchBar.hasClass('active')) {
                // Thêm icon X (close) vào nút tìm kiếm để tắt
                $searchIcon.html('<i class="fas fa-times"></i>');
                $searchBar.find('input[name="query"]').focus();
            } else {
                // Trả lại icon kính lúp
                $searchIcon.html('<i class="fas fa-search"></i>');
            }
        });
        $('.user-toggle').click(function(e) {
            e.preventDefault(); // Ngăn chặn hành động mặc định của thẻ a
            e.stopPropagation(); // Ngăn chặn sự kiện lan truyền

            // Toggle class 'active' trên .user-dropdown
            $('.user-dropdown').toggleClass('active');
        });

        // Ẩn menu khi click bên ngoài
        $(document).click(function() {
            if ($('.user-dropdown').hasClass('active')) {
                $('.user-dropdown').removeClass('active');
            }
        });

        // Giữ menu mở nếu click vào bên trong menu
        $('.user-menu').click(function(e) {
            e.stopPropagation();
        });
    });
</script>

</html>