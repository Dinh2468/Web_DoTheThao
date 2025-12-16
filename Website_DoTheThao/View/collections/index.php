<?php
//  View/collections/index.php

// 1. Bao gồm các file cần thiết (Đi ra 1 cấp để đến thư mục gốc)
require_once '../../config/connect.php';
require_once '../../classes/DB.class.php';
require_once '../../classes/Sanpham.class.php';
require_once '../../classes/Loaisp.class.php'; // Cần để lấy tên danh mục

// 2. Khởi tạo
$database = new Database();
$db = $database->getConnection();
$sanphamModel = new Sanpham($db);
$loaispModel = new Loaisp($db); // Khởi tạo model Loaisp

$loai_id = isset($_GET['loai_id']) ? (int)$_GET['loai_id'] : 0;
$sanphams = [];
$category_name = "Tất cả sản phẩm";

if ($loai_id > 0) {
    // 3. Lấy tên danh mục
    $loaispModel->loai_id = $loai_id;
    if ($loaispModel->readOne($loai_id)) {
        $category_name = $loaispModel->ten_loai;
    }

    // 4. Lấy sản phẩm theo loai_id
    $result = $sanphamModel->readByLoaiId($loai_id);

    while ($row = $result->fetch_object()) {
        $sanphams[] = $row;
    }
}

// 5. Nhúng giao diện (Đi ra 1 cấp để đến View/includes)
require_once '../includes/header.php';
?>

<div class="category-page container">
    <div class="list-header">
        <h2 class="list-title"><?php echo htmlspecialchars($category_name); ?></h2>
        <p class="result-count">Hiển thị <?php echo count($sanphams); ?> sản phẩm</p>
    </div>

    <div class="product-grid">

        <?php if (!empty($sanphams)): ?>
            <?php
            $image_path = BASE_URL . 'assets/images/sanphams/'; // Đường dẫn ảnh
            foreach ($sanphams as $sanpham): ?>
                <div class="product-item">
                    <a href="<?php echo BASE_URL; ?>products/detail.php?id=<?php echo $sanpham->sanpham_id; ?>" class="product-link">
                        <div class="product-image-wrapper">
                            <img src="<?php echo $image_path . htmlspecialchars($sanpham->hinh_anh); ?>" alt="<?php echo htmlspecialchars($sanpham->ten_sanpham); ?>">
                        </div>
                        <div class="product-info">
                            <p class="product-name"><?php echo htmlspecialchars($sanpham->ten_sanpham); ?></p>
                            <p class="product-price"><?php echo number_format($sanpham->gia, 0, ',', '.'); ?>đ</p>
                            <form action="<?php echo BASE_URL; ?>controller/GiohangController.php" method="POST" style="margin-top: 10px;">
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
            <p style="text-align: center; grid-column: 1 / -1; padding: 40px; font-size: 1.1em;">
                Không tìm thấy sản phẩm nào trong danh mục này.
            </p>
        <?php endif; ?>

    </div>
</div>

<?php
require_once '../includes/footer.php';
?>