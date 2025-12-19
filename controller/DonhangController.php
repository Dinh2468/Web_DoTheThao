<?php
// controller/DonhangController.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/connect.php';
require_once '../classes/DB.class.php';
$db = new DB();
$action = $_POST['action'] ?? '';
if ($action == 'place_order') {
    $khachhang_id = $_SESSION['taikhoan_id'];
    $ho_ten_nhan = $db->escapeString($_POST['ho_ten']);
    $sdt_nhan = $db->escapeString($_POST['so_dien_thoai']);
    $dia_chi_giao = $db->escapeString($_POST['dia_chi']);
    $ngay_dat = date('Y-m-d H:i:s');
    $tong_tien = 0;
    foreach ($_SESSION['cart'] as $id => $qty) {
        $query_check = "SELECT ten_sanpham, so_luong_ton FROM sanpham WHERE sanpham_id = $id";
        $sp = $db->selectOne($query_check);
        $tong_tien += ($sp->gia * $qty);
        if ($qty > $sp->so_luong_ton) {
            echo "<script>
                alert('Sản phẩm " . $sp->ten_sanpham . " hiện chỉ còn " . $sp->so_luong_ton . " sản phẩm. Vui lòng cập nhật lại giỏ hàng!');
                window.location.href='../View/cart/index.php';
            </script>";
            exit();
        }
    }
    $query_dh = "INSERT INTO donhang (khachhang_id, ho_ten_nhan, sdt_nhan, ngay_dat, tong_tien, trang_thai, dia_chi_giao) 
                 VALUES ($khachhang_id, '$ho_ten_nhan', '$sdt_nhan', '$ngay_dat', $tong_tien, 1, '$dia_chi_giao')";
    if ($db->execute($query_dh)) {
        $donhang_id = $db->getConnectionHandle()->insert_id;
        foreach ($_SESSION['cart'] as $id => $qty) {
            $query_price = "SELECT gia FROM sanpham WHERE sanpham_id = $id";
            $sp = $db->selectOne($query_price);
            $gia_ban = $sp->gia;
            $query_ct = "INSERT INTO chitiet_donhang (donhang_id, sanpham_id, so_luong, gia_ban) 
                         VALUES ($donhang_id, $id, $qty, $gia_ban)";
            $db->execute($query_ct);
        }
        unset($_SESSION['cart']);
        echo "<script>alert('Đặt hàng thành công! Đơn hàng của bạn là #" . $donhang_id . "'); window.location.href='../index.php';</script>";
        foreach ($_SESSION['cart'] as $id => $qty) {
            $db->execute("UPDATE sanpham SET so_luong_ton = so_luong_ton - $qty WHERE sanpham_id = $id");
        }
        exit();
    } else {
        echo "<script>alert('Lỗi hệ thống khi tạo đơn hàng.'); window.history.back();</script>";
        exit();
    }
}
