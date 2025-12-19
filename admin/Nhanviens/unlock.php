<?php
// admin/Nhanviens/unlock.php

require_once '../../config/connect.php';
require_once '../../controller/NhanvienController.php';
$nhanvien_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($nhanvien_id > 0) {
    $controller = new NhanvienController();
    $result = $controller->unlockHandler($nhanvien_id);

    if ($result['success']) {

        header("Location: detail.php?id=$nhanvien_id&status=success_unlock");
        exit();
    } else {

        header("Location: detail.php?id=$nhanvien_id&status=error_unlock");
        exit();
    }
} else {

    header("Location: index.php?status=error_invalid_id");
    exit();
}
