<?php
// admin/Nhanviens/detail.php

require_once '../includes/admin_header.php';
require_once '../../controller/NhanvienController.php';
require_once '../../classes/Nhanvien.class.php'; // Cần để dùng các hàm getStatusText/getStatusClass

// Khởi tạo biến cho thông báo lỗi
$message_error = null;
$status_message = null;
$status_class = '';

// Kiểm tra và lấy ID nhân viên
$nhanvien_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$controller = new NhanvienController();
$data = $controller->detail($nhanvien_id);

$nhanvien = $data['nhanvien'];
$message_error = $data['message']; // Thông báo lỗi nếu không tìm thấy ID

// === LOGIC XỬ LÝ THÔNG BÁO TRẠNG THÁI ===
if (isset($_GET['status'])) {
    $status = $_GET['status'];

    if ($status === 'success_lock') {
        $status_message = "🔒 Khóa tài khoản nhân viên thành công!";
        $status_class = 'cancelled';
    } elseif ($status === 'success_unlock') {
        $status_message = "🔓 Mở khóa tài khoản nhân viên thành công!";
        $status_class = 'completed';
    } elseif ($status === 'error_lock' || $status === 'error_unlock') {
        $status_message = "❌ Lỗi: Không thể thay đổi trạng thái tài khoản nhân viên.";
        $status_class = 'cancelled';
    } elseif ($status === 'success_edit') {
        $status_message = "✅ Cập nhật nhân viên thành công!";
        $status_class = 'completed';
    }
}
?>

<h1>Chi tiết Nhân viên</h1>
<p><a href="index.php">← Quay lại danh sách</a></p>

<?php if ($status_message): ?>
    <div class="alert <?php echo $status_class; ?> toast-fixed">
        <?php echo $status_message; ?>
    </div>
<?php endif; ?>

<?php if ($message_error): ?>
    <div class="alert cancelled"><?php echo $message_error; ?></div>
<?php endif; ?>

<?php if ($nhanvien): ?>
    <div class="form-container">
        <h3>Thông tin chi tiết</h3>

        <div class="form-group">
            <label>ID Nhân viên:</label>
            <p><strong>#<?php echo htmlspecialchars($nhanvien->nhanvien_id); ?></strong></p>
        </div>

        <div class="form-group">
            <label>Tên đăng nhập:</label>
            <p><?php echo htmlspecialchars($nhanvien->ten_dangnhap); ?></p>
        </div>

        <div class="form-group">
            <label>Họ Tên:</label>
            <p><?php echo htmlspecialchars($nhanvien->ho_ten); ?></p>
        </div>
        <div class="form-group">
            <label>Chức vụ:</label>
            <p><?php echo htmlspecialchars($nhanvien->chuc_vu ?? ''); ?></p>
        </div>
        <div class="form-group">
            <label>Email:</label>
            <p><?php echo htmlspecialchars($nhanvien->email); ?></p>
        </div>

        <div class="form-group">
            <label>Số điện thoại:</label>
            <p><?php echo htmlspecialchars($nhanvien->sdt ?? ''); ?></p>
        </div>

        <div class="form-group">
            <label>Ngày vào làm:</label>
            <p><?php echo htmlspecialchars($nhanvien->ngay_vao_lam ?? ''); ?></p>
        </div>


        <div class="form-group">
            <label>Trạng thái tài khoản:</label>
            <p>
                <?php
                $status_text = Nhanvien::getStatusText($nhanvien->trang_thai);
                $status_class = Nhanvien::getStatusClass($nhanvien->trang_thai);
                ?>
                <span class="status-badge <?php echo $status_class; ?>">
                    <?php echo $status_text; ?>
                </span>
            </p>
        </div>

        <div class="detail-actions">
            <a href="edit.php?id=<?php echo $nhanvien->nhanvien_id; ?>" class="export-btn" style="background-color: #007bff; margin-right: 10px;">
                <i class="fa fa-edit"></i> Sửa thông tin
            </a>

            <?php if ((string)$nhanvien->trang_thai === '1' || (string)$nhanvien->trang_thai === 'active'): ?>
                <a href="lock.php?id=<?php echo $nhanvien->nhanvien_id; ?>" class="export-btn" style="background-color: #f0ad4e; margin-right: 10px;" onclick="return confirm('Xác nhận KHÓA tài khoản này?')">
                    <i class="fa fa-lock"></i> Khóa tài khoản
                </a>
            <?php else: ?>
                <a href="unlock.php?id=<?php echo $nhanvien->nhanvien_id; ?>" class="export-btn" style="background-color: #5cb85c; margin-right: 10px;" onclick="return confirm('Xác nhận MỞ KHÓA tài khoản này?')">
                    <i class="fa fa-unlock"></i> Mở khóa tài khoản
                </a>
            <?php endif; ?>

            <a href="delete.php?id=<?php echo $nhanvien->nhanvien_id; ?>" class="export-btn" style="background-color: #d9534f;" onclick="return confirm('CẢNH BÁO: Bạn có chắc chắn muốn XÓA VĨNH VIỄN nhân viên này? Thao tác này không thể hoàn tác.')">
                <i class="fa fa-trash"></i> Xóa vĩnh viễn
            </a>
        </div>
    </div>
<?php endif; ?>

<?php
require_once '../includes/admin_footer.php';
?>