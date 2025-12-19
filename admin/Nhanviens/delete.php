<?php
// admin/Nhanviens/delete.php

require_once '../../config/connect.php';
require_once '../../controller/NhanvienController.php';

// Đảm bảo phải có ID
$nhanvien_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($nhanvien_id > 0) {
    $controller = new NhanvienController();
    $result = $controller->deleteHandler($nhanvien_id); // Gọi hàm xóa trong Controller

    if ($result['success']) {
        // Chuyển hướng về trang danh sách với thông báo thành công
        header("Location: index.php?status=success_delete");
        exit();
    } else {
        // Chuyển hướng về trang danh sách với thông báo lỗi
        header("Location: index.php?status=error_delete");
        exit();
    }
} else {
    // ID không hợp lệ, chuyển hướng về trang danh sách
    header("Location: index.php?status=error_invalid_id");
    exit();
}
