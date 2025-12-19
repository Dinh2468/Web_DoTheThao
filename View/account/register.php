<?php
// Vị trí: View/account/register.php
require_once '../includes/header.php';
?>
<div class="container login-page-container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="login-form-wrapper">
                <h2 class="form-title">ĐĂNG KÝ TÀI KHOẢN MỚI</h2>
                <p class="form-description">Vui lòng điền thông tin bên dưới để tạo tài khoản:</p>
                <form action="<?php echo BASE_URL; ?>controller/TaikhoanController.php" method="POST" class="login-form">
                    <input type="hidden" name="action" value="register">
                    <div class="form-group">
                        <label for="reg_username">Tên đăng nhập (Email):</label>
                        <input type="username" id="reg_username" name="ten_dangnhap" class="form-control" placeholder="Nhập tên đăng " required>
                    </div>
                    <div class="form-group">
                        <label for="reg_password">Mật khẩu:</label>
                        <input type="password" id="reg_password" name="mat_khau" class="form-control" required
                            placeholder="Mật khẩu (Tối thiểu 6 ký tự)">
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Nhập lại Mật khẩu:</label>
                        <input type="password" id="confirm_password" name="confirm_mat_khau" class="form-control" required
                            placeholder="Nhập lại mật khẩu">
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary btn-login">ĐĂNG KÝ</button>
                    </div>
                    <div class="form-links">
                        <p><a href="<?php echo BASE_URL; ?>View/account/login.php">Đăng nhập ngay</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
require_once '../includes/footer.php';
?>