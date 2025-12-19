<?php
// admin/Khachhangs/delete.php

require_once '../../config/connect.php'; // Cần nhúng connect.php để định nghĩa BASE_URL
require_once '../../controller/KhachhangController.php';

// Đảm bảo phải có ID
$khachhang_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($khachhang_id > 0) {
    $controller = new KhachhangController();
    $result = $controller->deleteHandler($khachhang_id);

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
