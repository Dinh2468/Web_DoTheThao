<?php
// admin/Sanphams/index.php


require_once '../includes/admin_header.php';

require_once '../../controller/SanphamController.php';


$controller = new SanphamController();
$data = $controller->index();
$sanphams = $data['sanphams'];


$message = null;
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'success_add') {
        $message = "Thêm sản phẩm mới thành công!";
    } elseif ($_GET['status'] == 'success_edit') {
        $message = "Cập nhật sản phẩm thành công!";
    } elseif ($_GET['status'] == 'success_delete') {
        $message = "Xóa sản phẩm thành công!";
    }
}
?>

<h1>Quản lý Sản phẩm</h1>
<p><a href="add.php" class="export-btn" style="background-color: #28a745;">
        <i class="fa fa-plus"></i> Thêm Sản phẩm mới
    </a></p>

<?php if ($message): ?>
    <div class="alert completed">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<?php if (empty($sanphams)): ?>
    <div class="alert pending">
        Chưa có sản phẩm nào được thêm vào hệ thống.
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên Sản phẩm</th>
                    <th>Loại</th>
                    <th>Giá (VNĐ)</th>
                    <th>Tồn kho</th>

                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sanphams as $sanpham): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($sanpham->sanpham_id); ?></td>
                        <td><a href="detail.php?id=<?php echo htmlspecialchars($sanpham->sanpham_id); ?>">
                                <?php echo htmlspecialchars($sanpham->ten_sanpham); ?>
                            </a></td>
                        <td><?php echo htmlspecialchars($sanpham->ten_loai); ?></td>
                        <td><?php echo number_format($sanpham->gia, 0, ',', '.'); ?></td>
                        <td><?php echo htmlspecialchars($sanpham->so_luong_ton); ?></td>

                        <td>
                            <a href="detail.php?id=<?php echo htmlspecialchars($sanpham->sanpham_id); ?>" class="action-link view">Xem chi tiết</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php

require_once '../includes/admin_footer.php';
?>