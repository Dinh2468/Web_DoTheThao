<?php
// admin/Khachhangs/index.php

// Nhúng header chung của Admin
require_once '../includes/admin_header.php';

// Cập nhật đường dẫn nhúng Controller
// Lên 2 cấp (từ admin/Khachhangs/ đến /), sau đó vào controller/
require_once '../../controller/KhachhangController.php';

// Khởi tạo Controller và gọi phương thức index để lấy dữ liệu
$controller = new KhachhangController();
$data = $controller->index();
$khachhangs = $data['khachhangs'];
// === LOGIC XỬ LÝ THÔNG BÁO ===
$status_message = null;
$status_class = '';

if (isset($_GET['status'])) {
    $status = $_GET['status'];

    if ($status === 'success_delete') {
        $status_message = "✅ Xóa khách hàng thành công!";
        $status_class = 'completed'; // Màu xanh lá
    } elseif ($status === 'error_delete') {
        $status_message = "❌ Lỗi: Không thể xóa khách hàng hoặc dữ liệu liên quan. Vui lòng kiểm tra CSDL.";
        $status_class = 'cancelled'; // Màu đỏ
    } elseif ($status === 'success_add') {
        $status_message = "✅ Thêm khách hàng mới thành công!";
        $status_class = 'completed';
    }
    // Bạn có thể thêm các trường hợp khác như success_lock, success_edit ở đây
}
if ($status_message): ?>
    <div class="alert <?php echo $status_class; ?> toast-fixed">
        <?php echo $status_message; ?>
    </div>
<?php endif; ?>
<h1>Quản lý Khách hàng</h1>
<!-- <p>Tổng cộng: ** -->
<?php
// echo count($khachhangs); 
?>
<!-- ** khách hàng</p> -->
<div class="form-container">
    <div class="recent-orders-header">
        <h3>Danh sách Khách hàng</h3>
        <a href="add.php" class="export-btn" style="background-color: #28a745;">+ Thêm Khách hàng</a>
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
            <?php if (empty($khachhangs)): ?>
                <tr>
                    <td colspan="6" style="text-align: center;">Không có khách hàng nào trong hệ thống.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($khachhangs as $kh): ?>
                    <tr>
                        <td><?php echo $kh->khachhang_id; ?></td>
                        <td><?php echo htmlspecialchars($kh->ho_ten); ?></td>
                        <td>
                            <?php echo htmlspecialchars($kh->email); ?><br>
                            <small class="text-muted">(<?php echo htmlspecialchars($kh->dien_thoai); ?>)</small>
                        </td>
                        <td><?php echo htmlspecialchars($kh->ten_dangnhap); ?></td>
                        <td>
                            <?php
                            $status_text = Khachhang::getStatusText($kh->trang_thai);
                            $status_class = Khachhang::getStatusClass($kh->trang_thai);
                            ?>
                            <span class="status-badge <?php echo $status_class; ?>">
                                <?php echo $status_text; ?>
                            </span>
                        </td>
                        <td>
                            <a href="detail.php?id=<?php echo $kh->khachhang_id; ?>" class="detail-link" style="color: #1890ff;">
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