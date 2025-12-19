<?php
// admin/Khachhangs/add.php
require_once '../includes/admin_header.php';
require_once '../../controller/KhachhangController.php';

$controller = new KhachhangController();
$errors = [];
$form_data = [];
$message = null;
$message_success = false;

// Xử lý POST request
// Xử lý POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_data = $_POST;
    $result = $controller->add($form_data);

    if ($result['success']) {
        $message = $result['message'];
        $message_success = true;
        header("Location: index.php?status=success_add");
        exit();
    } else {
        $message = $result['message'];
        $message_success = false;
        $errors = $result['errors'] ?? [];
    }
}
?>

<h1>Thêm Khách hàng mới</h1>
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

        <h3>Thông tin Khách hàng</h3>

        <div class="form-group">
            <label for="ho_ten">Họ Tên: <span style="color: red;">*</span></label>
            <input type="text" id="ho_ten" name="ho_ten" value="<?php echo htmlspecialchars($form_data['ho_ten'] ?? ''); ?>">
            <?php if (isset($errors['ho_ten'])): ?><span class="error-message"><?php echo $errors['ho_ten']; ?></span><?php endif; ?>
        </div>

        <div class="form-group">
            <label for="email">Email: <span style="color: red;">*</span></label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>">
            <?php if (isset($errors['email'])): ?><span class="error-message"><?php echo $errors['email']; ?></span><?php endif; ?>
        </div>

        <div class="form-group">
            <label for="dien_thoai">Điện thoại: <span style="color: red;">*</span></label>
            <input type="text" id="dien_thoai" name="dien_thoai" value="<?php echo htmlspecialchars($form_data['dien_thoai'] ?? ''); ?>">
            <?php if (isset($errors['dien_thoai'])): ?><span class="error-message"><?php echo $errors['dien_thoai']; ?></span><?php endif; ?>
        </div>

        <div class="form-group">
            <label for="dia_chi">Địa chỉ: <span style="color: red;">*</span></label>
            <textarea id="dia_chi" name="dia_chi" rows="3"><?php echo htmlspecialchars($form_data['dia_chi'] ?? ''); ?></textarea>
            <?php if (isset($errors['dia_chi'])): ?><span class="error-message"><?php echo $errors['dia_chi']; ?></span><?php endif; ?>
        </div>

        <button type="submit" class="export-btn" style="background-color: #3b594b;">
            <i class="fa fa-plus"></i> Thêm Khách hàng
        </button>
    </form>
</div>

<?php require_once '../includes/admin_footer.php'; ?>