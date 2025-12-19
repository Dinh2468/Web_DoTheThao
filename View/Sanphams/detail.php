<?php
// View/Sanphams/detail.php
require_once '../../controller/SanphamController.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$controller = new SanphamController();
$result = $controller->detail($id);

$product = $result['sanpham'];
$error_message = $result['message'];

require_once '../includes/header.php';
?>

<div class="container product-detail-page" style="padding: 50px 0;">
    <?php if ($product): ?>
        <div style="display: flex; gap: 40px; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 300px;">
                <img src="<?php echo BASE_URL; ?>assets/images/sanphams/<?php echo $product->hinh_anh; ?>"
                    style="width: 100%; border: 1px solid #eee; border-radius: 8px;">
            </div>

            <div style="flex: 1; min-width: 300px;">
                <h1 style="margin-top: 0;"><?php echo $product->ten_sanpham; ?></h1>
                <p style="color: #666;">Thương hiệu: <strong><?php echo $product->thuong_hieu; ?></strong></p>
                <p style="color: #666;">Loại: <strong><?php echo $product->ten_loai; ?></strong></p>

                <h2 style="color: #ED1D24; font-size: 28px; margin: 20px 0;">
                    <?php echo number_format($product->gia, 0, ',', '.'); ?>đ
                </h2>

                <div class="product-description" style="margin-bottom: 30px; line-height: 1.6;">
                    <h3>Mô tả sản phẩm:</h3>
                    <p><?php echo nl2br($product->mo_ta); ?></p>
                </div>

                <form action="<?php echo BASE_URL; ?>controller/GiohangController.php" method="POST" class="ajax-cart-form">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="sanpham_id" value="<?php echo $product->sanpham_id; ?>">

                    <div style="margin-bottom: 20px; display: flex; align-items: center; gap: 15px;">
                        <label>Số lượng:</label>
                        <input type="number" name="quantity" value="1" min="1" max="<?php echo $product->so_luong_ton; ?>"
                            style="width: 60px; padding: 8px; border: 1px solid #ccc;">
                        <span style="color: #888;">(Còn lại: <?php echo $product->so_luong_ton; ?>)</span>
                    </div>

                    <button type="submit" style="background: #ED1D24; color: white; padding: 15px 40px; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 16px;">
                        <i class="fas fa-shopping-cart"></i> THÊM VÀO GIỎ HÀNG
                    </button>

                    <button style="background: #888; color: white; padding: 15px 40px; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 16px; margin-left: 15px;">
                        <a href="../../index.php" style="color: #ffffffff;">Quay lại trang chủ</a>
                    </button>
                </form>
            </div>
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 100px 0;">
            <h2 style="color: #666;"><?php echo $error_message; ?></h2>
            <a href="../../index.php" style="color: #ED1D24;">Quay lại trang chủ</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>