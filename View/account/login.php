<?php
// View/account/login.php
// Nhúng Header (bao gồm kết nối CSDL và các thẻ <head>)
require_once '../includes/header.php';
?>
<div class="container login-page-container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="login-form-wrapper">
                <h2 class="form-title">ĐĂNG NHẬP TÀI KHOẢN</h2>
                <?php
                if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger" style="color: #a94442; background-color: #f2dede; border-color: #ebccd1; padding: 10px; margin-bottom: 15px; border: 1px solid transparent; border-radius: 4px;">
                        <?php echo htmlspecialchars($_SESSION['error']);  ?>
                    </div>
                    <?php unset($_SESSION['error']);
                    ?>
                <?php endif; ?>
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-success" style="color: #3c763d; background-color: #dff0d8; border-color: #d6e9c6; padding: 10px; margin-bottom: 15px; border: 1px solid transparent; border-radius: 4px;">
                        <?php echo $_SESSION['message']; ?>
                    </div>
                    <?php unset($_SESSION['message']); ?>
                <?php endif; ?>
                <form action="<?php echo BASE_URL; ?>controller/TaikhoanController.php" method="POST" class="login-form">
                    <input type="hidden" name="action" value="login">
                    <input type="hidden" name="redirect" value="<?php echo isset($_GET['redirect']) ? htmlspecialchars($_GET['redirect']) : ''; ?>">
                    <div class="form-group">
                        <label for="username">Tên đăng nhập:</label>
                        <input type="text" id="username" name="username" class="form-control" placeholder="Nhập tên đăng nhập của bạn" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Mật khẩu</label>
                        <input type="password" id="password" name="password" class="form-control" required
                            placeholder="Mật khẩu">
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary btn-login">ĐĂNG NHẬP</button>
                    </div>
                    <div class="form-links">
                        <a href="<?php echo BASE_URL; ?>account/forgot_password.php" class="forgot-password">Quên mật khẩu?</a>
                        <span class="separator">|</span>
                        <a href="<?php echo BASE_URL; ?>View/account/register.php" class="create-account">Đăng ký tài khoản mới</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
require_once '../includes/footer.php';
?>