<?php
// admin/Sanphams/delete.php

// 1. Nhúng Header (để có session_start() và connect.php)
require_once '../includes/admin_header.php';

// 2. Nhúng Controller
require_once '../../controller/SanphamController.php';

// Kiểm tra phương thức yêu cầu (chỉ chấp nhận GET để nhận ID)
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    // Nếu không phải GET (ví dụ: truy cập trực tiếp), chuyển hướng
    header('Location: index.php');
    exit();
}

// Lấy ID sản phẩm từ URL
$sanpham_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($sanpham_id <= 0) {
    // Xử lý nếu ID không hợp lệ
    $_SESSION['error_message'] = "ID Sản phẩm không hợp lệ.";
    header('Location: index.php');
    exit();
}

// 3. Khởi tạo Controller và xử lý xóa
$sanphamController = new SanphamController();
$delete_result = $sanphamController->deleteHandler($sanpham_id); // Gọi phương thức xóa

if ($delete_result['success']) {
    // Xóa thành công
    $_SESSION['success_message'] = $delete_result['message'];
    header('Location: index.php?status=success_delete');
} else {
    // Xóa thất bại
    $_SESSION['error_message'] = $delete_result['message'];
    header('Location: index.php?status=error_delete'); // Bạn có thể định nghĩa status=error_delete để xử lý thông báo lỗi
}
exit();
