<?php
// admin/Sanphams/detail.php

// Nhúng header chung của trang admin
require_once '../includes/admin_header.php';
// Nhúng Controller Sanpham
require_once '../../controller/SanphamController.php';

// Khởi tạo Controller
$controller = new SanphamController();

// Lấy ID sản phẩm từ URL
$sanpham_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($sanpham_id <= 0) {
    // Nếu không có ID, chuyển hướng hoặc hiển thị lỗi
    $_SESSION['error_message'] = "ID Sản phẩm không hợp lệ.";
    header('Location: index.php');
    exit();
}

// Lấy dữ liệu chi tiết sản phẩm
$data = $controller->detail($sanpham_id);
$sanpham = $data['sanpham'];
$message = $data['message']; // Thông báo lỗi nếu không tìm thấy

if (!$sanpham) {
    $_SESSION['error_message'] = $message ?? 'Không tìm thấy sản phẩm.';
    header('Location: index.php');
    exit();
}
$image_path = BASE_URL . 'assets/images/sanphams/';
$default_image = '1.webp';
?>

<h1>Chi Tiết Sản phẩm: <?php echo htmlspecialchars($sanpham->ten_sanpham); ?></h1>

<div class="detail-container">
    <div class="detail-header">
        <a href="edit.php?id=<?php echo htmlspecialchars($sanpham->sanpham_id); ?>" class="export-btn" style="background-color: #007bff;"><i class="fa fa-edit"></i> Sửa Sản phẩm</a>
        <a href="delete.php?id=<?php echo htmlspecialchars($sanpham->sanpham_id); ?>"
            class="export-btn delete-btn" style="background-color: #dc3545;"
            onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không? Thao tác này sẽ xóa các bảng liên quan và không thể hoàn tác.');">
            <i class="fa fa-trash"></i> Xóa Sản phẩm
        </a>
        <a href="index.php" class="export-btn" style="background-color: #6c757d;"><i class="fa fa-arrow-left"></i> Quay lại Danh sách</a>
    </div>

    <div class="detail-main-content">

        <div class="detail-info-card image-description-col">
            <h2>Hình ảnh & Mô tả</h2>

            <div class="product-image-box">
                <img src="<?php echo $image_path . htmlspecialchars($sanpham->hinh_anh); ?>"
                    alt="<?php echo htmlspecialchars($sanpham->ten_sanpham); ?>"
                    onerror="this.onerror=null;this.src='<?php echo $image_path . $default_image; ?>';"
                    style="max-width: 100%; height: auto; border-radius: 5px;">
            </div>

            <h3>Mô tả Sản phẩm</h3>
            <div class="mo-ta-box">
                <?php echo nl2br(htmlspecialchars($sanpham->mo_ta ?? 'Chưa có mô tả chi tiết.')); ?>
            </div>
        </div>

        <div class="detail-info-card info-col">
            <h2>Thông tin Cơ bản & Kho</h2>

            <div class="info-row">
                <label>Mã sản phẩm (ID):</label>
                <span><?php echo htmlspecialchars($sanpham->sanpham_id); ?></span>
            </div>
            <div class="info-row">
                <label>Tên Sản phẩm:</label>
                <strong><?php echo htmlspecialchars($sanpham->ten_sanpham); ?></strong>
            </div>
            <div class="info-row">
                <label>Thương hiệu:</label>
                <span><?php echo htmlspecialchars($sanpham->thuong_hieu ?? 'N/A'); ?></span>
            </div>
            <div class="info-row">
                <label>Loại Sản phẩm:</label>
                <span><?php echo htmlspecialchars($sanpham->ten_loai); ?></span>
            </div>
            <div class="info-row">
                <label>Nhà Cung Cấp:</label>
                <span><?php echo htmlspecialchars($sanpham->ten_ncc ?? 'N/A'); ?></span>
            </div>

            <hr>

            <div class="info-row">
                <label>Giá bán:</label>
                <span class="price"><?php echo number_format($sanpham->gia, 0, ',', '.'); ?> VNĐ</span>
            </div>
            <div class="info-row">
                <label>Số lượng Tồn kho:</label>
                <span class="stock"><?php echo htmlspecialchars($sanpham->so_luong_ton); ?></span>
            </div>

        </div>

    </div>

</div>

<?php
// Nhúng footer chung của trang admin
require_once '../includes/admin_footer.php';
?>