<?php
// admin/Khachhangs/edit.php
require_once '../includes/admin_header.php';
require_once '../../controller/KhachhangController.php';

$controller = new KhachhangController();
$errors = [];
$message = null;
$message_success = false;

// 1. Lấy ID và dữ liệu cũ
$khachhang_id = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;
if ($khachhang_id <= 0) {
    header("Location: index.php");
    exit();
}

$data_old = $controller->detail($khachhang_id);
if (!$data_old['khachhang']) {
    header("Location: index.php");
    exit();
}

// Khởi tạo form data bằng dữ liệu cũ (chuyển đối tượng sang mảng)
$form_data = (array)$data_old['khachhang'];

// 2. Xử lý POST request (Cập nhật)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Merge dữ liệu cũ với dữ liệu POST mới, bao gồm cả trường mật khẩu (có thể rỗng)
    $form_data = array_merge($form_data, $_POST);

    $result = $controller->edit($khachhang_id, $form_data);

    if ($result['success']) {
        $message = $result['message'];
        $message_success = true;

        // Tải lại dữ liệu sau khi update thành công (để hiển thị dữ liệu mới nhất)
        $data_old = $controller->detail($khachhang_id);
        $form_data = (array)$data_old['khachhang'];
    } else {
        $errors = $result['errors'];
        $message = $result['message'];
        $message_success = false;
    }
}
?>
<h1>Chỉnh sửa Khách hàng #<?php echo $khachhang_id; ?></h1>
<p><a href="detail.php?id=<?php echo $khachhang_id; ?>">← Quay lại chi tiết</a> | <a href="index.php">Danh sách</a></p>

<?php if ($message): ?>
    <div class="alert <?php echo $message_success ? 'completed' : 'cancelled'; ?> toast-fixed">
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<div class="form-container">
    <form action="edit.php?id=<?php echo $khachhang_id; ?>" method="POST">
        <input type="hidden" name="khachhang_id" value="<?php echo $khachhang_id; ?>">
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

        <hr>

        <h3>Cập nhật Mật khẩu (Không bắt buộc)</h3>

        <div class="form-group">
            <label for="ten_dangnhap">Tên đăng nhập (Đọc):</label>
            <input type="text" id="ten_dangnhap" value="<?php echo htmlspecialchars($form_data['ten_dangnhap'] ?? ''); ?>" disabled>
            <input type="hidden" name="ten_dangnhap" value="<?php echo htmlspecialchars($form_data['ten_dangnhap'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="mat_khau">Mật khẩu mới:</label>
            <input type="password" id="mat_khau" name="mat_khau" placeholder="Để trống nếu không muốn thay đổi">
            <?php if (isset($errors['mat_khau'])): ?><span class="error-message"><?php echo $errors['mat_khau']; ?></span><?php endif; ?>
        </div>

        <button type="submit" class="export-btn" style="background-color: #3b594b;">
            <i class="fa fa-save"></i> Lưu Thay đổi
        </button>
    </form>
</div>

<?php require_once '../includes/admin_footer.php'; ?>