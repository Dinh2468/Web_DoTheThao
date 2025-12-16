<?php
// Vị trí: View/Taikhoans/add.php

// 1. Bao gồm file kết nối (cần để sử dụng hằng số BASE_URL)
require_once '../../config/connect.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Thêm Tài khoản mới | Admin</title>
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

        .btn-success {
            background-color: #28a745;
            color: white;
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
            <h2>THÊM TÀI KHOẢN MỚI</h2>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert error"><?php echo $_SESSION['error'];
                                            unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <form action="../../controller/taikhoan_controller.php" method="post" class="account-form">

                <input type="hidden" name="action" value="add_taikhoan">

                <div class="form-group">
                    <label for="ten_dangnhap">Tên Đăng Nhập:</label>
                    <input type="text" id="ten_dangnhap" name="ten_dangnhap" required>
                </div>

                <div class="form-group">
                    <label for="mat_khau">Mật Khẩu:</label>
                    <input type="password" id="mat_khau" name="mat_khau" required>
                </div>

                <div class="form-group">
                    <label for="vai_tro">Vai Trò:</label>
                    <select id="vai_tro" name="vai_tro" required>
                        <option value="khachhang">Khách hàng</option>
                        <option value="nhanvien">Nhân viên</option>
                        <option value="admin">Quản trị viên</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="trang_thai">Trạng Thái:</label>
                    <select id="trang_thai" name="trang_thai" required>
                        <option value="1">Hoạt động (1)</option>
                        <option value="0">Khóa (0)</option>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-success">➕ Tạo Tài Khoản</button>
                    <a href="index.php" class="btn btn-secondary">Quay lại danh sách</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>