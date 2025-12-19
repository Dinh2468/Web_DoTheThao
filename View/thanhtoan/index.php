<?php
// View/thanhtoan/index.php
require_once '../../config/connect.php';
require_once '../../classes/DB.class.php';
require_once '../../classes/Sanpham.class.php';
require_once '../includes/header.php';

if (!$isLoggedIn) {
    header("Location: ../account/login.php?redirect=cart");
    exit();
}
$sanphamModel = new Sanpham();
$cart = $_SESSION['cart'] ?? [];
$total = 0;

if (empty($cart)) {
    header("Location: ../../index.php");
    exit();
}

?>
<div class="container checkout-page" style="padding: 40px 0;">
    <h2 style="text-align: center; margin-bottom: 30px;">XÁC NHẬN ĐƠN HÀNG</h2>

    <div style="display: flex; gap: 30px; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 300px; border: 1px solid #ddd; padding: 20px; border-radius: 8px;">
            <h3><i class="fas fa-truck"></i> Thông tin giao hàng</h3>
            <form action="../../controller/DonhangController.php" method="POST" id="checkout-form">
                <input type="hidden" name="action" value="place_order">


                <div class="form-group" style="margin-bottom: 15px;">
                    <label>Họ và tên người nhận:</label>
                    <input type="text" name="ho_ten" class="form-control" required placeholder="Ví dụ: Nguyễn Văn A">
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label>Số điện thoại người nhận:</label>
                    <input type="tel" name="so_dien_thoai" class="form-control" required placeholder="Ví dụ: 0901234567">
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label>Địa chỉ nhận hàng chi tiết:</label>
                    <textarea name="dia_chi" class="form-control" required placeholder="Số nhà, tên đường, phường/xã..." style="height: 100px;"></textarea>
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label>Ghi chú thêm (nếu có):</label>
                    <textarea name="ghi_chu" class="form-control" placeholder="Ví dụ: Giao giờ hành chính"></textarea>
                </div>
            </form>
        </div>

        <div style="flex: 1; min-width: 300px; background: #f9f9f9; padding: 20px; border-radius: 8px; border: 1px solid #eee;">
            <h3><i class="fas fa-shopping-cart"></i> Chi tiết đơn hàng</h3>
            <div class="order-summary-list">
                <?php foreach ($cart as $id => $qty):
                    $sanphamModel->readOne($id);
                    $subtotal = $sanphamModel->gia * $qty;
                    $total += $subtotal;
                ?>
                    <div style="display: flex; align-items: center; margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                        <img src="../../assets/images/sanphams/<?php echo $sanphamModel->hinh_anh; ?>" width="60" style="border-radius: 4px;">
                        <div style="margin-left: 15px; flex: 1;">
                            <p style="margin: 0; font-weight: bold;"><?php echo $sanphamModel->ten_sanpham; ?></p>
                            <p style="margin: 0; color: #666;">Số lượng: <?php echo $qty; ?> x <?php echo number_format($sanphamModel->gia, 0, ',', '.'); ?>đ</p>
                        </div>
                        <p style="margin: 0; font-weight: bold;"><?php echo number_format($subtotal, 0, ',', '.'); ?>đ</p>
                    </div>
                <?php endforeach; ?>
            </div>
            <div style="margin-top: 20px;">
                <div style="display: flex; justify-content: space-between; font-size: 1.2em; font-weight: bold;">
                    <span>TỔNG CỘNG:</span>
                    <span style="color: #ED1D24;"><?php echo number_format($total, 0, ',', '.'); ?>đ</span>
                </div>

                <button type="submit" form="checkout-form" class="btn-checkout"
                    style="width: 100%; margin-top: 20px; border: none; background: #ED1D24; color: white; padding: 15px; border-radius: 5px; cursor: pointer; font-weight: bold; font-size: 16px;">
                    XÁC NHẬN ĐẶT HÀNG
                </button>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>