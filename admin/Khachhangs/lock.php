<?php
// admin/Khachhangs/lock.php

require_once '../../config/connect.php';
require_once '../../controller/KhachhangController.php';

// Đảm bảo phải có ID
$khachhang_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($khachhang_id > 0) {
    $controller = new KhachhangController();
    $result = $controller->lockHandler($khachhang_id); // Gọi hàm khóa

    if ($result['success']) {
        // Chuyển hướng về trang chi tiết khách hàng với thông báo thành công
        header("Location: detail.php?id=$khachhang_id&status=success_lock");
        exit();
    } else {
        // Chuyển hướng về trang chi tiết với thông báo lỗi
        header("Location: detail.php?id=$khachhang_id&status=error_lock");
        exit();
    }
} else {
    // ID không hợp lệ, chuyển hướng về trang danh sách
    header("Location: index.php?status=error_invalid_id");
    exit();
}
