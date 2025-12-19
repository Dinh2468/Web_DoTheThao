<?php
// admin/includes/admin_header.php
// Đặt session_start() và require_once connect.php ở đầu file này
$root_dir = dirname(dirname(__DIR__));
require_once $root_dir . '/config/connect.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// $database = new Database();
// $db = $database->getConnection();
// Giả định BASE_URL được định nghĩa trong connect.php
// $base_url = "../"; // Điều chỉnh nếu bạn đặt file này trong thư mục admin/includes/
// === XÁC ĐỊNH TRANG HIỆN TẠI VÀ THƯ MỤC ===
// Lấy tên thư mục hiện tại (ví dụ: 'Khachhangs')
$current_directory = basename(dirname($_SERVER['PHP_SELF']));

// Lấy tên file hiện tại (ví dụ: 'index.php', 'add.php', 'edit.php')
$current_page = basename($_SERVER['PHP_SELF']);

// Xác định mục Active
// 1. Kiểm tra Trang chủ: Active chỉ khi đang ở thư mục 'admin' gốc VÀ không nằm trong bất kỳ thư mục con nào khác
$is_dashboard_active = ($current_directory === 'admin');

// 2. Kiểm tra Khách hàng: Active khi thư mục hiện tại là 'Khachhangs'
$is_customer_active = ($current_directory === 'Khachhangs');
// 3. Kiểm tra Nhân viên: Active khi thư mục hiện tại là 'Nhanviens'
$is_employee_active = ($current_directory === 'Nhanviens');
// 4. Kiểm tra Sản phẩm: Active khi thư mục hiện tại là 'Sanphams'
$is_sanphams_group_active = (in_array($current_directory, ['Sanphams', 'Loaisps', 'Nhacungcaps']));

// 5. Xác định mục con đang active
$is_sanpham_active = ($current_directory === 'Sanphams');
$is_loaisp_active = ($current_directory === 'Loaisps'); // Dùng cho Danh mục SP
$is_nhacungcap_active = ($current_directory === 'Nhacungcaps');
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Admin</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/admin_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="admin-wrapper">

        <div class="sidebar">
            <div class="logo-admin">ADMIN</div>
            <ul class="sidebar-menu">
                <li class="<?php echo $is_dashboard_active ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>admin/index.php"><i class="fa fa-tachometer-alt">
                        </i> Trang chủ </a>
                </li>
                <li class="menu-item has-submenu <?php echo $is_sanphams_group_active ? 'active' : ''; ?>">
                    <a href="javascript:void(0);" class="menu-toggle">
                        <i class="fa fa-cubes"></i> Quản lý Sản phẩm
                    </a>
                    <ul class="submenu">
                        <li class="<?php echo $is_sanpham_active ? 'active' : ''; ?>">
                            <a href="<?php echo BASE_URL; ?>admin/Sanphams/index.php">Sản phẩm</a>
                        </li>

                        <li class="<?php echo $is_loaisp_active ? 'active' : ''; ?>">
                            <a href="<?php echo BASE_URL; ?>admin/Loaisps/index.php">Danh mục SP</a>
                        </li>

                        <li class="<?php echo $is_nhacungcap_active ? 'active' : ''; ?>">
                            <a href="<?php echo BASE_URL; ?>admin/Nhacungcaps/index.php">Nhà Cung Cấp</a>
                        </li>
                    </ul>
                </li>
                <li><a href="#"><i class="fa fa-shopping-cart"></i> Quản lý đơn hàng</a></li>
                <li class="<?php echo $is_employee_active ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>admin/Nhanviens/index.php">
                        <i class="fa fa-user-tie"></i> Nhân viên</a>
                </li>
                <li class="<?php echo $is_customer_active ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>admin/Khachhangs/index.php">
                        <i class="fa fa-users"></i> Khách hàng</a>
                </li>
                <li><a href="#"><i class="fa fa-chart-line"></i> Báo cáo doanh thu</a></li>
                <li><a href="#"><i class="fa fa-cog"></i> Cài đặt hệ thống</a></li>
            </ul>
            <div class="copyright">
                © 2025 SportStore Admin
            </div>
        </div>

        <div class="main-content-wrapper">

            <div class="top-header">
                <div class="welcome-group">
                    <p class="welcome-text">Chào mừng trở lại, Admin!</p>
                </div>
                <div class="user-info">
                    <span class="username">Trương Ngọc Đỉnh</span>
                    <a href="#" class="logout">Đăng xuất</a>
                </div>
            </div>

            <div class="dashboard-content">