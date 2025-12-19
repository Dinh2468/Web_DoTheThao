<?php
// admin/Sanphams/delete.php
require_once '../includes/admin_header.php';
require_once '../../controller/SanphamController.php';
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    header('Location: index.php');
    exit();
}
$sanpham_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($sanpham_id <= 0) {
    $_SESSION['error_message'] = "ID Sản phẩm không hợp lệ.";
    header('Location: index.php');
    exit();
}
$sanphamController = new SanphamController();
$delete_result = $sanphamController->deleteHandler($sanpham_id);
if ($delete_result['success']) {
    $_SESSION['success_message'] = $delete_result['message'];
    header('Location: index.php?status=success_delete');
} else {
    $_SESSION['error_message'] = $delete_result['message'];
    header('Location: index.php?status=error_delete');
}
exit();
