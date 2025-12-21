<?php
session_start();

if (!isset($_SESSION['MaAdmin'])) { 
    header('Location: ../../admin/admin_login.php'); 
    exit; 
}

require_once __DIR__ . '/../config/connect.php';
$sql = "
    SELECT
        d.donhang_id   AS MaDon,
        d.ngay_dat     AS NgayDat,
        d.trang_thai   AS TrangThai,
        d.tong_tien    AS TongTien,
        k.ho_ten       AS TenNguoiDung
    FROM donhang d
    LEFT JOIN khachhang k ON d.khachhang_id = k.khachhang_id
    ORDER BY d.ngay_dat DESC
";

$result = $conn->query($sql);
$orders = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];








function getStatusBadge($status) {
    switch ((int)$status) {
        case 0:
            return '<span class="badge bg-warning text-dark">Chờ duyệt</span>';
        case 1:
            return '<span class="badge bg-info text-dark">Đang giao</span>';
        case 2:
            return '<span class="badge bg-success">Hoàn thành</span>';
        case 3:
            return '<span class="badge bg-danger">Đã hủy</span>';
        default:
            return '<span class="badge bg-secondary">Không rõ</span>';
    }
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý đơn hàng - Admin Panel</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Nunito', sans-serif; background-color: #f8f9fa; }

        .sidebar { min-height: 100vh; background: #2c3e50; box-shadow: 2px 0 5px rgba(0,0,0,0.05); }
        .sidebar .nav-link { color: rgba(255,255,255,0.7); padding: 12px 20px; transition: all 0.3s; border-radius: 8px; margin-bottom: 5px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background-color: rgba(255,255,255,0.1); color: #fff; transform: translateX(5px); }

        .table-custom th { background-color: #f8f9fa; font-weight: 700; color: #6c757d; border-top: none; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }

        tr { transition: background-color 0.2s; }
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
                        <a href="dashboard.php" class="nav-link align-middle">
                            <i class="fas fa-tachometer-alt"></i> <span class="ms-1 d-none d-sm-inline">Tổng quan</span>
                        </a>
                    </li>
                    <li class="w-100">
                        <a href="products.php" class="nav-link align-middle">
                            <i class="fas fa-box-open"></i> <span class="ms-1 d-none d-sm-inline">Sản phẩm</span>
                        </a>
                    </li>
                    <li class="w-100">
                        <a href="orders.php" class="nav-link align-middle active">
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
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold mb-1">Quản lý đơn hàng</h3>
                    <p class="text-muted small">Theo dõi và xử lý các đơn đặt hàng mới</p>
                </div>
                
                <div class="d-flex gap-2">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" class="form-control border-start-0 ps-0" placeholder="Tìm theo mã đơn, tên khách...">
                    </div>
                    <select class="form-select w-auto">
                        <option>Tất cả trạng thái</option>
                        <option>Chờ duyệt</option>
                        <option>Hoàn thành</option>
                    </select>
                </div>
            </div>

            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-custom table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">Mã đơn</th>
                                    <th>Khách hàng</th>
                                    <th>Ngày đặt</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                    <th class="text-end pe-4">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($orders)) { ?>
                                    <tr><td colspan="6" class="text-center py-5 text-muted">Chưa có đơn hàng nào.</td></tr>
                                <?php } else { ?>
                                    <?php foreach ($orders as $o): ?>
<tr>
    <td class="ps-4 fw-bold text-primary">
        #<?= $o['MaDon'] ?>
    </td>

    <td>
        <?= htmlspecialchars($o['TenNguoiDung'] ?? 'Khách vãng lai') ?>
    </td>

    <td class="text-muted">
        <?= date('d/m/Y H:i', strtotime($o['NgayDat'])) ?>
    </td>

    <td class="fw-bold">
        <?= number_format((float)$o['TongTien'], 0, ',', '.') ?>₫
    </td>

    <td>
        <?= getStatusBadge($o['TrangThai']) ?>
    </td>

    <td class="text-end pe-4">
        <a href="order_details.php?id=<?= $o['MaDon'] ?>"
           class="btn btn-sm btn-outline-primary shadow-sm">
            Xem chi tiết <i class="fas fa-arrow-right ms-1"></i>
        </a>
    </td>
</tr>

<?php endforeach; ?>

                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white border-top-0 py-3 d-flex justify-content-between align-items-center">
                    <small class="text-muted">Hiển thị <?php echo count($orders); ?> đơn hàng mới nhất.</small>
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                            <li class="page-item disabled"><a class="page-link" href="#">Trước</a></li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">Sau</a></li>
                        </ul>
                    </nav>
                </div>
            </div>

        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>