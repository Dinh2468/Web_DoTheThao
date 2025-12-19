<?php
// admin/Khachhangs/detail.php

require_once '../includes/admin_header.php';
require_once '../../controller/KhachhangController.php';

// Kiểm tra và lấy ID khách hàng
$khachhang_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$controller = new KhachhangController();
$data = $controller->detail($khachhang_id); // Gọi phương thức detail() mới

$khachhang = $data['khachhang'];
$message_error = $data['message'];

// === LOGIC XỬ LÝ THÔNG BÁO TRẠNG THÁI ===
$status_message = null;
$status_class = '';
if (isset($_GET['status'])) {
    $status = $_GET['status'];

    if ($status === 'success_lock') {
        $status_message = "🔒 Khóa tài khoản thành công!";
        $status_class = 'cancelled'; // Thường dùng màu cảnh báo/đỏ cho thao tác khóa
    } elseif ($status === 'success_unlock') {
        $status_message = "🔓 Mở khóa tài khoản thành công!";
        $status_class = 'completed'; // Màu xanh lá cho thao tác mở khóa
    } elseif ($status === 'error_lock' || $status === 'error_unlock') {
        $status_message = "❌ Lỗi: Không thể thay đổi trạng thái tài khoản.";
        $status_class = 'cancelled';
    }
}
?>

<h1>Chi tiết Khách hàng</h1>
<p><a href="index.php">← Quay lại danh sách</a></p>


<?php if ($status_message): ?>
    <div class="alert <?php echo $status_class; ?> toast-fixed">
        <?php echo $status_message; ?>
    </div>
<?php endif; ?>

<?php if ($message_error): ?>
    <div class="alert cancelled"><?php echo $message_error; ?></div>
<?php endif; ?>

<?php if ($khachhang): ?>
    <div class="form-container">
        <h3>Thông tin chi tiết</h3>

        <div class="form-group">
            <label>ID Khách hàng:</label>
            <p><strong><?php echo $khachhang->khachhang_id; ?></strong></p>
        </div>

        <div class="form-group">
            <label>Họ Tên:</label>
            <p><?php echo htmlspecialchars($khachhang->ho_ten); ?></p>
        </div>

        <div class="form-group">
            <label>Email:</label>
            <p><?php echo htmlspecialchars($khachhang->email); ?></p>
        </div>

        <div class="form-group">
            <label>Điện thoại:</label>
            <p><?php echo htmlspecialchars($khachhang->dien_thoai); ?></p>
        </div>

        <div class="form-group">
            <label>Địa chỉ:</label>
            <p><?php echo htmlspecialchars($khachhang->dia_chi); ?></p>
        </div>

        <div class="form-group">
            <label>Tên đăng nhập:</label>
            <p><?php echo htmlspecialchars($khachhang->ten_dangnhap); ?></p>
        </div>

        <div class="form-group">
            <label>Trạng thái tài khoản:</label>
            <?php
            $status_text = Khachhang::getStatusText($khachhang->trang_thai);
            $status_class = Khachhang::getStatusClass($khachhang->trang_thai);
            ?>
            <p>
                <span class="status-badge <?php echo $status_class; ?>">
                    <?php echo $status_text; ?>
                </span>
            </p>
        </div>



        <a href="edit.php?id=<?php echo $khachhang->khachhang_id; ?>" class="export-btn" style="background-color: #007bff; margin-right: 10px;">
            <i class="fa fa-edit"></i> Sửa thông tin
        </a>
        <?php if ($khachhang->trang_thai == 1): ?>
            <a href="lock.php?id=<?php echo $khachhang->khachhang_id; ?>" class="export-btn" style="background-color: #f0ad4e; margin-right: 10px;" onclick="return confirm('Xác nhận KHÓA tài khoản này? Tài khoản sẽ không thể đăng nhập.')">
                <i class="fa fa-lock"></i> Khóa tài khoản
            </a>
        <?php else: ?>
            <a href="unlock.php?id=<?php echo $khachhang->khachhang_id; ?>" class="export-btn" style="background-color: #5cb85c; margin-right: 10px;" onclick="return confirm('Xác nhận MỞ KHÓA tài khoản này?')">
                <i class="fa fa-unlock"></i> Mở khóa tài khoản
            </a>
        <?php endif; ?>
        <a href="delete.php?id=<?php echo $khachhang->khachhang_id; ?>" class="export-btn" style="background-color: #d9534f;" onclick="return confirm('CẢNH BÁO: Bạn có chắc chắn muốn XÓA VĨNH VIỄN khách hàng này? Thao tác này không thể hoàn tác.')">
            <i class="fa fa-trash"></i> Xóa vĩnh viễn
        </a>
    </div>
<?php endif; ?>

<?php
require_once '../includes/admin_footer.php';
?>