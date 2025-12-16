<?php
// Vị trí: View/Taikhoans/index.php

// Bắt đầu session để có thể sử dụng các thông báo lỗi/thành công
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 1. Bao gồm file kết nối (đi ngược 2 cấp từ View/Taikhoans/ ra gốc)
require_once '../../config/connect.php';

// 2. Thiết lập CSDL và truy vấn
$database = new Database();
$db = $database->getConnection();

// =======================================================
// TRUY VẤN TẤT CẢ TÀI KHOẢN
// =======================================================
$query = "
    SELECT 
        taikhoan_id,
        ten_dangnhap,
        vai_tro,
        trang_thai
    FROM 
        Taikhoan
    ORDER BY 
        taikhoan_id DESC
";

$stmt = $db->query($query);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản lý Tài khoản | Admin</title>
    <link rel="stylesheet" href="../../assets/css/admin_style.css">
    <style>
        /* CSS tối thiểu cho bảng */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        .data-table th {
            background-color: #f2f2f2;
        }

        .alert.success {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border: 1px solid #c3e6cb;
            margin-bottom: 15px;
        }

        .btn {
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 4px;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>

<body>
    <div id="wrapper">
        <div id="content" style="padding: 20px;">
            <h2>DANH SÁCH TÀI KHOẢN HỆ THỐNG</h2>
            <p><a href="add.php" class="btn btn-primary">➕ Thêm Tài khoản mới</a></p>

            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert success"><?php echo $_SESSION['message'];
                                            unset($_SESSION['message']); ?></div>
            <?php endif; ?>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên Đăng Nhập</th>
                        <th>Vai Trò</th>
                        <th>Trạng Thái</th>
                        <th>Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($stmt && $stmt->num_rows > 0) {
                        while ($row = $stmt->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['taikhoan_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['ten_dangnhap']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['vai_tro']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['trang_thai']) . "</td>";
                            echo "<td>";
                            echo "<a href='edit.php?id=" . $row['taikhoan_id'] . "'>Sửa</a> | ";

                            // Form Xóa (Gửi POST đến Controller)
                            echo "<form method='POST' action='../../controller/taikhoan_controller.php' style='display:inline;' onsubmit=\"return confirm('Xóa Tài khoản sẽ xóa cả hồ sơ Khách hàng/Nhân viên liên quan. Bạn có chắc chắn muốn xóa không?');\">";
                            echo "<input type='hidden' name='action' value='delete_taikhoan'>";
                            echo "<input type='hidden' name='taikhoan_id' value='" . $row['taikhoan_id'] . "'>";
                            echo "<button type='submit' style='background: none; border: none; color: red; cursor: pointer; padding: 0;'>Xóa</button>";
                            echo "</form>";

                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' style='text-align: center;'>Không có tài khoản nào trong hệ thống.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>