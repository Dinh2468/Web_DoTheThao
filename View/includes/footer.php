<?php
// View/includes/footer.php 
?>
<footer class="main-footer">
    <div class="footer-container">
        <span id="gioithieu"></span>
        <div class="footer-column logo-info">
            <div class="contact-details">
                <p><strong><span class="icon-address"></span> Địa chỉ:</strong></p>
                <p>CÔNG TY CỔ PHẦN THỜI TRANG ALZADO</p>
                <p>GPKD số 0107773034 cấp ngày 27/03/2017 tại Sở kế hoạch và Đầu tư Thành phố Hồ Chí Minh</p>
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
    <div id="cart-notification" style="display:none; position:fixed; top:20px; right:20px; background:#28a745; color:white; padding:15px 25px; border-radius:5px; z-index:9999; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <i class="fas fa-check-circle"></i> Thêm vào giỏ hàng thành công!
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
            if ($searchBar.hasClass('active')) {
                $searchIcon.html('<i class="fas fa-times"></i>');
                $searchBar.find('input[name="query"]').focus();
            } else {
                $searchIcon.html('<i class="fas fa-search"></i>');
            }
        });
        $('.user-toggle').click(function(e) {
            e.preventDefault();
            e.stopPropagation();
            $('.user-dropdown').toggleClass('active');
        });
        $(document).click(function() {
            if ($('.user-dropdown').hasClass('active')) {
                $('.user-dropdown').removeClass('active');
            }
        });
        $('.user-menu').click(function(e) {
            e.stopPropagation();
        });
        $('.ajax-cart-form').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var url = "<?php echo BASE_URL; ?>controller/GiohangController.php";
            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize(),
                success: function(response) {
                    $('.cart-count').text(response);
                    $('#cart-notification').fadeIn().delay(2000).fadeOut();
                },
                error: function() {
                    alert("Có lỗi xảy ra, vui lòng thử lại!");
                }
            });
        });
    });
</script>

</html>