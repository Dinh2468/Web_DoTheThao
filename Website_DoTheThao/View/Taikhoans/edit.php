<?php
// Vị trí: View/Taikhoans/edit.php

// 1. Bao gồm các Class cần thiết
require_once '../../config/connect.php';
require_once '../../classes/Taikhoan.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$taikhoan_id = isset($_GET['id']) ? $_GET['id'] : die('Lỗi: Thiếu ID Tài khoản.');

// 2. Khởi tạo CSDL và Model
$database = new Database();
$db = $database->getConnection();
$taikhoan = new Taikhoan($db);

$taikhoan->taikhoan_id = $taikhoan_id;

// 3. Đọc dữ liệu tài khoản hiện tại
if (!$taikhoan->readOne()) {
    die("Lỗi: Không tìm thấy tài khoản có ID = " . $taikhoan_id);
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Sửa Tài khoản | Admin</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/admin_style.css">
    <style>
        /* CSS tối thiểu cho Form */
        .main-content {
            padding: 20px;
            max-width: 600px;
            margin: 30px auto;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .btn {
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 4px;
            cursor: pointer;
            border: none;
        }

        .btn-warning {
            background-color: #ffc107;
            color: #333;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .alert.error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border: 1px solid #f5c6cb;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <div class="admin-wrapper">
        <div class="main-content">
            <h2>SỬA THÔNG TIN TÀI KHOẢN: <?php echo htmlspecialchars($taikhoan->ten_dangnhap); ?></h2>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert error"><?php echo $_SESSION['error'];
                                            unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <form action="../../controller/taikhoan_controller.php" method="post" class="account-form">

                <input type="hidden" name="action" value="update_taikhoan">
                <input type="hidden" name="taikhoan_id" value="<?php echo htmlspecialchars($taikhoan->taikhoan_id); ?>">

                <div class="form-group">
                    <label for="ten_dangnhap">Tên Đăng Nhập:</label>
                    <input type="text" id="ten_dangnhap" name="ten_dangnhap" value="<?php echo htmlspecialchars($taikhoan->ten_dangnhap); ?>" required>
                </div>

                <div class="form-group">
                    <label for="mat_khau">Mật Khẩu (Để trống nếu không đổi):</label>
                    <input type="password" id="mat_khau" name="mat_khau" placeholder="Nhập mật khẩu mới">
                </div>

                <div class="form-group">
                    <label for="vai_tro">Vai Trò:</label>
                    <select id="vai_tro" name="vai_tro" required>
                        <option value="khachhang" <?php if ($taikhoan->vai_tro == 'khachhang') echo 'selected'; ?>>Khách hàng</option>
                        <option value="nhanvien" <?php if ($taikhoan->vai_tro == 'nhanvien') echo 'selected'; ?>>Nhân viên</option>
                        <option value="admin" <?php if ($taikhoan->vai_tro == 'admin') echo 'selected'; ?>>Quản trị viên</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="trang_thai">Trạng Thái:</label>
                    <select id="trang_thai" name="trang_thai" required>
                        <option value="1" <?php if ($taikhoan->trang_thai == 1) echo 'selected'; ?>>Hoạt động (1)</option>
                        <option value="0" <?php if ($taikhoan->trang_thai == 0) echo 'selected'; ?>>Khóa (0)</option>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-warning">💾 Cập Nhật Tài Khoản</button>
                    <a href="index.php" class="btn btn-secondary">Quay lại danh sách</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>