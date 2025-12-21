<?php
session_start();
if (!isset($_SESSION['MaAdmin'])) {
    header('Location: ../../admin/admin_login.php');
    exit;
}

require_once __DIR__ . '/../config/connect.php';

$message = '';
$error = '';

// XÓA SẢN PHẨM
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $id = (int)$_POST['delete_id'];

    // kiểm tra sản phẩm đã có trong đơn hàng chưa
    $checkSql = "SELECT COUNT(*) AS total FROM chitiet_donhang WHERE sanpham_id = $id";
    $checkResult = $conn->query($checkSql);
    $rowCheck = $checkResult->fetch_assoc();

    if ($rowCheck['total'] > 0) {
        $error = "Không thể xóa sản phẩm này vì đã có trong đơn hàng.";
    } else {
        // lấy ảnh cũ
        $old = $conn->query("SELECT hinh_anh FROM sanpham WHERE sanpham_id = $id")->fetch_assoc();
        if (!empty($old['hinh_anh'])) {
            $path = "../../uploads/products/" . $old['hinh_anh'];
            if (file_exists($path)) unlink($path);
        }

        // xóa DB
        if ($conn->query("DELETE FROM sanpham WHERE sanpham_id = $id")) {
            $message = "Đã xóa sản phẩm thành công!";
        } else {
            $error = "Lỗi khi xóa sản phẩm.";
        }
    }
}

// LẤY DANH SÁCH SẢN PHẨM
$result = $conn->query("SELECT * FROM sanpham ORDER BY sanpham_id DESC");
$products = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý sản phẩm - Admin Panel</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Nunito', sans-serif; background-color: #f8f9fa; }

        .sidebar { min-height: 100vh; background: #2c3e50; box-shadow: 2px 0 5px rgba(0,0,0,0.05); }
        .sidebar .nav-link { color: rgba(255,255,255,0.7); padding: 12px 20px; transition: all 0.3s; border-radius: 8px; margin-bottom: 5px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background-color: rgba(255,255,255,0.1); color: #fff; transform: translateX(5px); }

        .table-custom th { background-color: #f8f9fa; color: #6c757d; border-top: none; }
        .product-thumb { width: 60px; height: 60px; object-fit: cover; border-radius: 8px; border: 1px solid #dee2e6; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
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
                        <a href="products.php" class="nav-link align-middle active">
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
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold mb-1">Quản lý sản phẩm</h3>
                    <p class="text-muted small">Danh sách tất cả các sản phẩm trong cửa hàng</p>
                </div>
                <a href="product_form.php" class="btn btn-primary shadow-sm">
                    <i class="fas fa-plus me-2"></i> Thêm mới
                </a>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check-circle me-2"></i><?php echo $message; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show"><i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php endif; ?>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-custom table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">#</th>
                                    <th>Ảnh</th>
                                    <th>Tên sản phẩm</th>
                                    <th>Chi tiết</th>
                                    <th>Giá bán</th>
                                    <th>Trạng thái</th>
                                    <th class="text-end pe-4">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
<?php if (empty($products)): ?>
    <tr>
        <td colspan="7" class="text-center py-4 text-muted">Chưa có sản phẩm nào.</td>
    </tr>
<?php else: ?>
<?php foreach ($products as $index => $p): ?>
<tr>
    <td class="ps-4">#<?= $index + 1 ?></td>

    <td>
        <?php if (!empty($p['hinh_anh'])): ?>
            <img src="../../uploads/products/<?= htmlspecialchars($p['hinh_anh']) ?>" class="product-thumb">
        <?php else: ?>
            <img src="https://via.placeholder.com/60" class="product-thumb">
        <?php endif; ?>
    </td>

    <td class="fw-bold"><?= htmlspecialchars($p['ten_sanpham']) ?></td>

    <td>
        <span class="badge bg-light text-dark border">
            <?= htmlspecialchars($p['thuong_hieu'] ?? '-') ?>
        </span>
    </td>

    <td class="fw-bold text-primary">
        <?= number_format((float)$p['gia'], 0, ',', '.') ?>₫
    </td>

    <td>
        <?php if ($p['so_luong_ton'] > 0): ?>
            <span class="badge bg-success">Còn hàng</span>
        <?php else: ?>
            <span class="badge bg-danger">Hết hàng</span>
        <?php endif; ?>
    </td>

    <td class="text-end pe-4">
        <a href="product_form.php?id=<?= $p['sanpham_id'] ?>" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-edit"></i>
        </a>

        <form method="POST" class="d-inline" onsubmit="return confirm('Xóa sản phẩm này?');">
            <input type="hidden" name="delete_id" value="<?= $p['sanpham_id'] ?>">
            <button type="submit" class="btn btn-sm btn-outline-danger">
                <i class="fas fa-trash"></i>
            </button>
        </form>
    </td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
</tbody>

                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>