<?php
// admin/Nhanviens/index.php

// Nhúng header chung của Admin
require_once '../includes/admin_header.php';

// Cập nhật đường dẫn nhúng Controller (Lên 2 cấp)
require_once '../../controller/NhanvienController.php';
require_once '../../classes/Nhanvien.class.php'; // Cần để dùng các hàm getStatusText/getStatusClass

// Khởi tạo Controller và gọi phương thức index để lấy dữ liệu
$controller = new NhanvienController();
$data = $controller->index();
$nhanviens = $data['nhanviens'];

// === LOGIC XỬ LÝ THÔNG BÁO ===
$status_message = null;
$status_class = '';

if (isset($_GET['status'])) {
    $status = $_GET['status'];

    if ($status === 'success_delete') {
        $status_message = "✅ Xóa nhân viên thành công!";
        $status_class = 'completed'; // Màu xanh lá
    } elseif ($status === 'error_delete') {
        $status_message = "❌ Lỗi: Không thể xóa nhân viên hoặc dữ liệu liên quan.";
        $status_class = 'cancelled'; // Màu đỏ
    } elseif ($status === 'success_add') {
        $status_message = "✅ Thêm nhân viên mới thành công!";
        $status_class = 'completed';
    }
    // Thêm logic thông báo cho khóa/mở khóa nếu cần hiển thị ở trang danh sách
}
?>

<h1>Quản lý Nhân viên</h1>
<!-- <p>Tổng cộng:  -->
<?php //echo count($nhanviens); 
?>
<!-- nhân viên</p> -->

<?php if ($status_message): ?>
    <div class="alert <?php echo $status_class; ?> toast-fixed">
        <?php echo $status_message; ?>
    </div>
<?php endif; ?>

<div class="form-container">
    <div class="recent-orders-header">
        <h3>Danh sách Nhân viên</h3>
        <a href="add.php" class="export-btn" style="background-color: #28a745;">+ Thêm Nhân viên</a>
    </div>

    <table class="order-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Họ Tên</th>
                <th>Email / SĐT</th>
                <th>Tên Đăng Nhập</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($nhanviens)): ?>
                <tr>
                    <td colspan="6" style="text-align: center;">Không có nhân viên nào trong hệ thống.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($nhanviens as $nv): ?>
                    <tr>
                        <td><?php echo $nv->nhanvien_id; ?></td>
                        <td><?php echo htmlspecialchars($nv->ho_ten); ?></td>
                        <td>
                            <?php echo htmlspecialchars($nv->email); ?><br>
                            <small class="text-muted">(<?php echo htmlspecialchars($nv->sdt); ?>)</small>
                        </td>
                        <td><?php echo htmlspecialchars($nv->ten_dangnhap); ?></td>
                        <td>
                            <?php
                            $status_text = Nhanvien::getStatusText($nv->trang_thai);
                            $status_class = Nhanvien::getStatusClass($nv->trang_thai);
                            ?>
                            <span class="status-badge <?php echo $status_class; ?>">
                                <?php echo $status_text; ?>
                            </span>
                        </td>
                        <td>
                            <a href="detail.php?id=<?php echo $nv->nhanvien_id; ?>" class="detail-link" style="color: #1890ff;">
                                Xem chi tiết
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
// Nhúng footer chung của Admin
require_once '../includes/admin_footer.php';
?>