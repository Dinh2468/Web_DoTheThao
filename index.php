<?php
// index.php (Trang chủ chính - Front-end)

// Nhúng Header (bao gồm kết nối CSDL và các thẻ <head>)
require_once 'View/includes/header.php';
// Nhúng Controller Sanpham
require_once 'controller/SanphamController.php';

// Khởi tạo Controller và lấy dữ liệu
$controller = new SanphamController();
$data = $controller->index();
$sanphams = $data['sanphams'];
$image_path = BASE_URL . 'assets/images/sanphams/'; // Đường dẫn ảnh
?>

<div class="promotion-banner simplified">
    <img src="assets/images/slider_2.jpg" alt="Giày chạy bộ 3.0ELITE" class="banner-image">
</div>

<section class="product-list-section container">
    <div class="list-header">
        <h2 class="list-title">Sản phẩm </h2>
        <a href="#" class="view-all-link">Xem tất cả</a>
    </div>

    <div class="product-grid">

        <?php if (!empty($sanphams)): ?>
            <?php foreach ($sanphams as $sanpham): ?>
                <div class="product-item">
                    <a href="<?php echo BASE_URL; ?>View/Sanphams/detail.php?id=<?php echo $sanpham->sanpham_id; ?>" class="product-link">
                        <div class="product-image-wrapper">
                            <img
                                src="<?php echo $image_path . htmlspecialchars($sanpham->hinh_anh); ?>"
                                alt="<?php echo htmlspecialchars($sanpham->ten_sanpham); ?>">
                        </div>
                        <div class="product-info">
                            <p class="product-name"><?php echo htmlspecialchars($sanpham->ten_sanpham); ?></p>

                            <p class="product-price"><?php echo number_format($sanpham->gia, 0, ',', '.'); ?>đ</p>

                            <form class="ajax-cart-form" style="padding: 0 10px 10px;">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="sanpham_id" value="<?php echo $sanpham->sanpham_id; ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn-add-to-cart">
                                    <i class="fas fa-shopping-cart"></i> Thêm vào giỏ
                                </button>
                            </form>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align: center; grid-column: 1 / -1; padding: 20px;">Không có sản phẩm nào được tìm thấy.</p>
        <?php endif; ?>

    </div>
</section>
<?php
// Nhúng Footer (chứa thẻ <footer> và đóng </body>, </html>)
require_once 'View/includes/footer.php';
?>