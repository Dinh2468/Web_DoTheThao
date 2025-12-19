<?php
// admin/Nhanviens/lock.php

require_once '../../config/connect.php';
require_once '../../controller/NhanvienController.php';

// Đảm bảo phải có ID
$nhanvien_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($nhanvien_id > 0) {
    $controller = new NhanvienController();
    $result = $controller->lockHandler($nhanvien_id); // Gọi hàm khóa tài khoản (status = 0)

    if ($result['success']) {
        // Chuyển hướng về trang chi tiết nhân viên với thông báo thành công
        header("Location: detail.php?id=$nhanvien_id&status=success_lock");
        exit();
    } else {
        // Chuyển hướng về trang chi tiết với thông báo lỗi
        header("Location: detail.php?id=$nhanvien_id&status=error_lock");
        exit();
    }
} else {
    // ID không hợp lệ, chuyển hướng về trang danh sách
    header("Location: index.php?status=error_invalid_id");
    exit();
}
