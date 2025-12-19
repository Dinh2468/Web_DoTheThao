<?php
// admin/Nhanviens/add.php

require_once '../includes/admin_header.php';
require_once '../../controller/NhanvienController.php';

$controller = new NhanvienController();
$errors = [];
$form_data = [];
$message = null;
$message_success = false;

// Xử lý POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_data = $_POST;
    $result = $controller->add($form_data);

    if ($result['success']) {
        // Chuyển hướng về trang danh sách với thông báo thành công (Post-Redirect-Get)
        header("Location: index.php?status=success_add");
        exit();
    } else {
        $message = $result['message'];
        $message_success = false;
        // Đảm bảo gán errors an toàn, nếu Controller không trả về khóa 'errors'
        $errors = $result['errors'] ?? [];
    }
}
?>

<h1>Thêm Nhân viên mới</h1>
<p><a href="index.php">← Quay lại danh sách</a></p>

<?php if ($message): ?>
    <div class="alert <?php echo $message_success ? 'completed' : 'cancelled'; ?>">
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<div class="form-container">
    <form action="add.php" method="POST">
        <h3>Thông tin Đăng nhập (Tài khoản)</h3>

        <div class="form-group">
            <label for="ten_dangnhap">Tên đăng nhập: <span style="color: red;">*</span></label>
            <input type="text" id="ten_dangnhap" name="ten_dangnhap" value="<?php echo htmlspecialchars($form_data['ten_dangnhap'] ?? ''); ?>">
            <?php if (isset($errors['ten_dangnhap'])): ?><span class="error-message"><?php echo $errors['ten_dangnhap']; ?></span><?php endif; ?>
        </div>

        <div class="form-group">
            <label for="mat_khau">Mật khẩu: <span style="color: red;">*</span></label>
            <input type="password" id="mat_khau" name="mat_khau">
            <?php if (isset($errors['mat_khau'])): ?><span class="error-message"><?php echo $errors['mat_khau']; ?></span><?php endif; ?>
        </div>

        <hr>

        <h3>Thông tin Nhân viên</h3>

        <div class="form-group">
            <label for="ho_ten">Họ Tên: <span style="color: red;">*</span></label>
            <input type="text" id="ho_ten" name="ho_ten" value="<?php echo htmlspecialchars($form_data['ho_ten'] ?? ''); ?>">
            <?php if (isset($errors['ho_ten'])): ?><span class="error-message"><?php echo $errors['ho_ten']; ?></span><?php endif; ?>
        </div>

        <div class="form-group">
            <label for="chuc_vu">Chức vụ: <span style="color: red;">*</span></label>
            <input type="text" id="chuc_vu" name="chuc_vu" value="<?php echo htmlspecialchars($form_data['chuc_vu'] ?? ''); ?>">
            <?php if (isset($errors['chuc_vu'])): ?><span class="error-message"><?php echo $errors['chuc_vu']; ?></span><?php endif; ?>
        </div>

        <div class="form-group">
            <label for="email">Email: <span style="color: red;">*</span></label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>">
            <?php if (isset($errors['email'])): ?><span class="error-message"><?php echo $errors['email']; ?></span><?php endif; ?>
        </div>

        <div class="form-group">
            <label for="sdt">Số điện thoại: <span style="color: red;">*</span></label> <input type="text" id="sdt" name="sdt" value="<?php echo htmlspecialchars($form_data['sdt'] ?? ''); ?>">
            <?php if (isset($errors['sdt'])): ?><span class="error-message"><?php echo $errors['sdt']; ?></span><?php endif; ?>
        </div>

        <div class="form-group">
            <label for="ngay_vao_lam">Ngày vào làm: <span style="color: red;">*</span></label> <input type="date" id="ngay_vao_lam" name="ngay_vao_lam" value="<?php echo htmlspecialchars($form_data['ngay_vao_lam'] ?? ''); ?>">
            <?php if (isset($errors['ngay_vao_lam'])): ?><span class="error-message"><?php echo $errors['ngay_vao_lam']; ?></span><?php endif; ?>
        </div>

        <button type="submit" class="export-btn" style="background-color: #3b594b;">
            <i class="fa fa-plus"></i> Thêm Nhân viên
        </button>
    </form>
</div>

<?php require_once '../includes/admin_footer.php'; ?>