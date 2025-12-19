<?php
// View/giohang/index.php
require_once '../../config/connect.php';
require_once '../../classes/DB.class.php';
require_once '../../classes/Sanpham.class.php';
require_once '../includes/header.php';
$sanphamModel = new Sanpham();
$cart = $_SESSION['cart'] ?? [];
$total = 0;
$can_checkout = true;
?>
<div class="container cart-page" style="padding: 40px 0;">
    <h2>GIỎ HÀNG CỦA BẠN</h2>
    <?php if (empty($cart)): ?>
        <p>Giỏ hàng đang trống. <a href="../../index.php">Tiếp tục mua sắm</a></p>
    <?php else: ?>
        <form action="../../controller/GiohangController.php?action=update" method="POST">
            <table class="cart-table" style="width:100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid #ddd;">
                        <th>Sản phẩm</th>
                        <th>Giá</th>
                        <th>Số lượng</th>
                        <th>Tổng</th>
                        <th>Xóa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart as $id => $qty):
                        $sanphamModel->readOne($id);
                        $subtotal = $sanphamModel->gia * $qty;
                        $total += $subtotal;
                        // Kiểm tra tồn kho
                        $is_invalid = ($qty > $sanphamModel->so_luong_ton);
                        if ($is_invalid) $can_checkout = false;
                    ?>
                        <tr style="border-bottom: 1px solid #eee; text-align: center;">
                            <td style="display: flex; align-items: center; padding: 10px;">
                                <img src="../../assets/images/sanphams/<?php echo $sanphamModel->hinh_anh; ?>" width="80">
                                <div style="text-align: left; margin-left: 10px;">
                                    <p style="margin: 0; font-weight: bold;"><?php echo $sanphamModel->ten_sanpham; ?></p>
                                    <p style="margin: 0; font-size: 13px; color: #666;">Kho còn: <?php echo $sanphamModel->so_luong_ton; ?></p>
                                    <?php if ($is_invalid): ?>
                                        <p style="color: #ED1D24; font-size: 13px; margin: 5px 0 0 0;">
                                            <i class="fas fa-exclamation-triangle"></i> Số lượng yêu cầu vượt quá tồn kho!
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td><?php echo number_format($sanphamModel->gia, 0, ',', '.'); ?>đ</td>
                            <td>
                                <input type="number" name="qty[<?php echo $id; ?>]" value="<?php echo $qty; ?>" min="1" style="width: 50px;">
                            </td>
                            <td><?php echo number_format($subtotal, 0, ',', '.'); ?>đ</td>
                            <td>
                                <a href="../../controller/GiohangController.php?action=remove&id=<?php echo $id; ?>" style="color: red;"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="cart-summary" style="text-align: right; margin-top: 30px; display: flex; align-items: center; justify-content: flex-end; gap: 15px;">
                <h3 style="margin: 0; margin-right: auto;">Tổng tiền: <span style="color: #ED1D24;"><?php echo number_format($total, 0, ',', '.'); ?>đ</span></h3>
                <button type="submit" class="btn-cart btn-update">Cập nhật giỏ hàng</button>
                <button type="button" class="btn-cart btn-continue" onclick="window.location.href='../../index.php'">Tiếp tục mua sắm</button>
                <?php if ($can_checkout): ?>
                    <?php if ($isLoggedIn): ?>
                        <a href="../thanhtoan/index.php" class="btn-cart btn-checkout">THANH TOÁN</a>
                    <?php else: ?>
                        <a href="../account/login.php?redirect=cart" class="btn-cart btn-checkout" onclick="alert('Vui lòng đăng nhập để tiến hành thanh toán!');">THANH TOÁN</a>
                    <?php endif; ?>
                <?php else: ?>
                    <button type="button" class="btn-cart btn-checkout" disabled
                        style="background-color: #ccc; cursor: not-allowed; border-color: #ccc;"
                        title="Vui lòng cập nhật số lượng hợp lệ trước khi thanh toán">
                        THANH TOÁN
                    </button>
                <?php endif; ?>
            </div>
        </form>
    <?php endif; ?>
</div>
<?php require_once '../includes/footer.php'; ?>