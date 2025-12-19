<?php
// View/includes/header.php 

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$root_dir = dirname(dirname(dirname(__FILE__)));

require_once $root_dir . '/config/connect.php';

require_once $root_dir . '/classes/DB.class.php';
require_once $root_dir . '/classes/Loaisp.class.php';

$loaispModel = new Loaisp();

$categories = $loaispModel->readAll();


$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;

// Lấy tên người dùng từ Session
$userName = $isLoggedIn && isset($_SESSION['ten_dangnhap'])
    ? htmlspecialchars($_SESSION['ten_dangnhap'])
    : 'Khách hàng';
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <title>Sport Vietnam - Thời Trang Thể Thao </title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>

<body id="template-index">
    <div class="opacity_menu"></div>
    <header class="main-header">
        <div class="header-content">
            <div class="logo">
                <img src="<?php echo BASE_URL; ?>assets/images/logo.png" alt="Logo">
            </div>
            <div class="header-nav-search-wrapper">

                <nav class="main-nav">
                    <ul>
                        <li><a href="<?php echo BASE_URL; ?>">TRANG CHỦ</a></li>

                        <li class="dropdown">
                            <a href="#">DANH MỤC SẢN PHẨM <i class="fas fa-chevron-down dropdown-arrow"></i></a>

                            <div class="dropdown-content">
                                <?php if (!empty($categories)): ?>
                                    <?php foreach ($categories as $category): ?>
                                        <a href="<?php echo BASE_URL; ?>View/collections/index.php?loai_id=<?php echo htmlspecialchars($category->loai_id); ?>"
                                            title="<?php echo htmlspecialchars($category->ten_loai); ?>">
                                            <?php echo htmlspecialchars($category->ten_loai); ?>
                                        </a>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <a href="#" style="color: #ED1D24;">Không có danh mục</a>
                                <?php endif; ?>
                            </div>
                        </li>

                        <li><a href="#gioithieu">LIÊN HỆ</a></li>
                        <li><a href="#gioithieu">GIỚI THIỆU</a></li>
                        <li><a href="#">TIN TỨC</a></li>

                    </ul>
                </nav>
                <div id="search-bar-dropdown">
                    <form action="<?php echo BASE_URL; ?>View/Sanphams/search.php" method="GET" class="search-form">
                        <input type="text" name="query" placeholder="Tìm kiếm sản phẩm, thương hiệu..." required>
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>
            </div>



            <div class="user-actions">

                <a href="javascript:void(0)" class="icon-link search-toggle" id="search-icon-toggle">
                    <i class="fas fa-search"></i>
                </a>
                <div class="user-dropdown">
                    <a href="javascript:void(0)" class="icon-link user-toggle">
                        <i class="far fa-user"></i>
                    </a>
                    <div class="dropdown-content user-menu">
                        <?php if ($isLoggedIn): ?>
                            <span class="user-name">Xin chào, <?php echo htmlspecialchars($userName); ?></span>
                            <a href="<?php echo BASE_URL; ?>account/settings">Cài đặt</a>
                            <a href="<?php echo BASE_URL; ?>controller/TaikhoanController.php?action=logout">Đăng xuất</a>
                        <?php else: ?>
                            <a href="<?php echo BASE_URL; ?>View/account/login">Đăng nhập</a>
                        <?php endif; ?>
                    </div>
                </div>

                <a href="<?php echo BASE_URL; ?>View/giohang/index.php" class="icon-link cart-icon">
                    <i class="fas fa-shopping-bag"></i>
                    <span class="cart-count">
                        <?php
                        $count = 0;
                        if (isset($_SESSION['cart'])) {
                            foreach ($_SESSION['cart'] as $qty) $count += $qty;
                        }
                        echo $count;
                        ?>
                    </span>
                </a>
            </div>

        </div>


        <div class="red-bar"></div>
    </header>