<?php
session_start();
require_once '../config/connect.php';

// Bắt buộc login
if (!isset($_SESSION['MaAdmin'])) {
    header('Location: admin_login.php');
    exit;
}

// Logout nếu quá 5 phút không hoạt động
$max_time = 30; //  30s
if (isset($_SESSION['last_login_time']) && (time() - $_SESSION['last_login_time'] > $max_time)) {
    session_destroy();
    header('Location: admin_login.php');
    exit;
}

// Cập nhật thời gian hoạt động
$_SESSION['last_login_time'] = time();

// Lấy dữ liệu tổng quan
$totalProducts = $conn->query("SELECT COUNT(*) AS total FROM sanpham")->fetch_assoc()['total'] ?? 0;
$totalOrders   = $conn->query("SELECT COUNT(*) AS total FROM donhang")->fetch_assoc()['total'] ?? 0;
$totalRevenue  = $conn->query("
    SELECT SUM(ct.so_luong * ct.gia_ban) AS total
    FROM donhang d
    JOIN chitiet_donhang ct ON d.donhang_id = ct.donhang_id
    WHERE d.trang_thai = 2
")->fetch_assoc()['total'] ?? 0;
$pendingOrders = $conn->query("SELECT COUNT(*) AS total FROM donhang WHERE trang_thai = 0")->fetch_assoc()['total'] ?? 0;
?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Fashion Shop</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Nunito', sans-serif; background-color: #f8f9fa; }

        .sidebar { min-height: 100vh; background: #2c3e50; box-shadow: 2px 0 5px rgba(0,0,0,0.05); }
        .sidebar .nav-link { color: rgba(255,255,255,0.7); padding: 12px 20px; transition: all 0.3s; border-radius: 8px; margin-bottom: 5px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background-color: rgba(255,255,255,0.1); color: #fff; transform: translateX(5px); }
        .sidebar .nav-link i { width: 25px; }
        
        .stat-card { border: none; border-radius: 15px; transition: transform 0.3s ease; overflow: hidden; }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
        .icon-box { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
        
        .card-blue { border-left: 5px solid #0d6efd; }
        .card-warning { border-left: 5px solid #ffc107; }
        .card-success { border-left: 5px solid #198754; }
        .card-danger { border-left: 5px solid #dc3545; }
        
        .bg-blue-light { background-color: rgba(13, 110, 253, 0.1); color: #0d6efd; }
        .bg-warning-light { background-color: rgba(255, 193, 7, 0.1); color: #ffc107; }
        .bg-success-light { background-color: rgba(25, 135, 84, 0.1); color: #198754; }
        .bg-danger-light { background-color: rgba(220, 53, 69, 0.1); color: #dc3545; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row flex-nowrap">
        
        <div class="col-auto col-md-3 col-xl-2 px-sm-2 px-0 sidebar">
            <div class="d-flex flex-column align-items-center align-items-sm-start px-3 pt-3 text-white min-vh-100">
                <a href="dashboard.php" class="d-flex align-items-center pb-3 mb-md-0 me-md-auto text-white text-decoration-none border-bottom w-100">
                    <i class="fas fa-shirt fa-2x me-2 text-info"></i>
                    <span class="fs-5 d-none d-sm-inline fw-bold">Admin Panel</span>
                </a>
                <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-center align-items-sm-start w-100 mt-4" id="menu">
                    <li class="nav-item w-100">
                        <a href="dashboard.php" class="nav-link align-middle active">
                            <i class="fas fa-tachometer-alt"></i> <span class="ms-1 d-none d-sm-inline">Tổng quan</span>
                        </a>
                    </li>
                    <li class="w-100">
                        <a href="products.php" class="nav-link align-middle">
                            <i class="fas fa-box-open"></i> <span class="ms-1 d-none d-sm-inline">Sản phẩm</span>
                        </a>
                    </li>
                    <li class="w-100">
                        <a href="orders.php" class="nav-link align-middle">
                            <i class="fas fa-shopping-cart"></i> <span class="ms-1 d-none d-sm-inline">Đơn hàng</span>
                        </a>
                    </li>
                    <li class="w-100 mt-4 border-top pt-2">
                         <a href="admin_register.php" class="nav-link align-middle">
                            <i class="fas fa-user-plus"></i> <span class="ms-1 d-none d-sm-inline">Tạo Admin mới</span>
                        </a>
                    </li>
                    <li class="w-100">
                        <a href="admin_logout.php" class="nav-link align-middle text-danger">
                            <i class="fas fa-sign-out-alt"></i> <span class="ms-1 d-none d-sm-inline">Đăng xuất</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="col py-4 px-4">
            <div class="d-flex justify-content-between align-items-center mb-5 bg-white p-3 rounded shadow-sm">
                <div>
                    <h4 class="mb-0 fw-bold text-dark">Xin chào, <?php echo htmlspecialchars($_SESSION['TenAdmin']); ?>! 👋</h4>
                    <small class="text-muted">Đây là số liệu thống kê cửa hàng hôm nay.</small>
                </div>
                <div class="d-flex align-items-center">
                    <div class="text-end me-3 d-none d-md-block">
                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($_SESSION['TenAdmin']); ?></div>
                        <div class="small text-muted">Quản trị viên</div>
                    </div>
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['TenAdmin']); ?>&background=random" class="rounded-circle shadow-sm" width="45" height="45">
                </div>
            </div>

            <div class="row g-4">
                
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card stat-card shadow-sm h-100 card-blue">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-1 fw-bold text-uppercase small">Sản phẩm</p>
                                    <h3 class="mb-0 fw-bold text-dark"><?php echo number_format($totalProducts); ?></h3>
                                </div>
                                <div class="icon-box bg-blue-light">
                                    <i class="fas fa-box"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card stat-card shadow-sm h-100 card-warning">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-1 fw-bold text-uppercase small">Đơn hàng</p>
                                    <h3 class="mb-0 fw-bold text-dark"><?php echo number_format($totalOrders); ?></h3>
                                </div>
                                <div class="icon-box bg-warning-light">
                                    <i class="fas fa-receipt"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card stat-card shadow-sm h-100 card-success">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-1 fw-bold text-uppercase small">Doanh thu</p>
                                    <h3 class="mb-0 fw-bold text-success"><?php echo number_format($totalRevenue, 0, ',', '.'); ?>₫</h3>
                                </div>
                                <div class="icon-box bg-success-light">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card stat-card shadow-sm h-100 card-danger">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-1 fw-bold text-uppercase small">Chờ xử lý</p>
                                    <h3 class="mb-0 fw-bold text-danger"><?php echo number_format($pendingOrders); ?></h3>
                                </div>
                                <div class="icon-box bg-danger-light">
                                    <i class="fas fa-hourglass-half"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="orders.php?status=0" class="btn btn-sm btn-outline-danger w-100 rounded-pill">Xử lý ngay <i class="fas fa-arrow-right ms-1"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row mt-5">
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0 fw-bold">Hành động nhanh</h5>
                        </div>
                        <div class="card-body">
                             <div class="d-flex gap-2">
                                 <a href="product_form.php" class="btn btn-primary"><i class="fas fa-plus me-1"></i> Thêm sản phẩm mới</a>
                                 <a href="orders.php" class="btn btn-light border"><i class="fas fa-list me-1"></i> Xem tất cả đơn hàng</a>
                             </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>