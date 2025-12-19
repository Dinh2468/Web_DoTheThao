<?php
// controller/GiohangController.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$action = $_POST['action'] ?? $_GET['action'] ?? '';
switch ($action) {
    case 'add':
        $id = $_POST['sanpham_id'] ?? 0;
        $qty = (int)($_POST['quantity'] ?? 1);
        if ($id > 0) {
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }
            if (isset($_SESSION['cart'][$id])) {
                $_SESSION['cart'][$id] += $qty;
            } else {
                $_SESSION['cart'][$id] = $qty;
            }
            $total_count = 0;
            foreach ($_SESSION['cart'] as $item_qty) {
                $total_count += $item_qty;
            }
            echo $total_count;
            exit();
        }
        break;
    case 'remove':
        $id = $_GET['id'];
        unset($_SESSION['cart'][$id]);
        header("Location: ../View/giohang/index.php");
        break;
    case 'update':
        foreach ($_POST['qty'] as $id => $qty) {
            if ($qty <= 0) unset($_SESSION['cart'][$id]);
            else $_SESSION['cart'][$id] = $qty;
        }
        header("Location: ../View/giohang/index.php");
        break;
}
