<?php
session_start();

/* ===== CHỈ ADMIN MỚI ĐƯỢC TRUY CẬP ===== */
if (!isset($_SESSION['MaAdmin'])) {
    header('Location: admin_login.php');
    exit;
}

require_once '../config/connect.php'; // Kết nối DB

$error = "";
$success = "";

$form_data = [
    "username" => "",
    "email" => "",
    "fullname" => "",
    "password" => "",
    "password_confirm" => ""
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $form_data["username"] = trim($_POST["username"] ?? "");
    $form_data["email"]    = trim($_POST["email"] ?? "");
    $form_data["fullname"] = trim($_POST["fullname"] ?? "");
    $form_data["password"] = trim($_POST["password"] ?? "");
    $form_data["password_confirm"] = trim($_POST["password_confirm"] ?? "");

    /* ===== VALIDATE ===== */
    if ($form_data["username"] === "") {
        $error = "Vui lòng nhập tên đăng nhập!";
    } elseif ($form_data["email"] === "") {
        $error = "Vui lòng nhập email!";
    } elseif (!filter_var($form_data["email"], FILTER_VALIDATE_EMAIL)) {
        $error = "Email không hợp lệ!";
    } elseif ($form_data["fullname"] === "") {
        $error = "Vui lòng nhập họ tên!";
    } elseif ($form_data["password"] === "") {
        $error = "Vui lòng nhập mật khẩu!";
    } elseif (strlen($form_data["password"]) < 6) {
        $error = "Mật khẩu phải có ít nhất 6 ký tự!";
    } elseif ($form_data["password"] !== $form_data["password_confirm"]) {
        $error = "Mật khẩu xác nhận không khớp!";
    } else {
        // Kiểm tra username tồn tại chưa
        $stmt = $conn->prepare("SELECT taikhoan_id FROM taikhoan WHERE ten_dangnhap = ?");
        $stmt->bind_param("s", $form_data["username"]);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Tên đăng nhập đã tồn tại!";
        } else {
            // Hash password
            $hash = password_hash($form_data["password"], PASSWORD_BCRYPT);

            // Thêm admin mới
            $vai_tro = "admin";
            $trang_thai = "1";

            $stmt = $conn->prepare("
                INSERT INTO taikhoan (ten_dangnhap, mat_khau, vai_tro, trang_thai)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->bind_param(
                "ssss",
                $form_data["username"],
                $hash,
                $vai_tro,
                $trang_thai
            );

            if ($stmt->execute()) {
                $success = "Tạo tài khoản quản trị viên thành công!";
                $form_data = ["username"=>"","email"=>"","fullname"=>"","password"=>"","password_confirm"=>""];
            } else {
                $error = "Lỗi hệ thống, vui lòng thử lại!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký Quản trị - Fashion Shop</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(135deg, #2c3e50 0%, #4a69bd 100%);
            min-height: 100vh;
        }
        .card {
            border: none;
            border-radius: 15px;
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #5e72e4;
        }
        .input-group-text {
            background-color: #f8f9fa;
            border-right: none;
        }
        .form-control {
            border-left: none;
        }
        .input-group:focus-within .input-group-text {
            border-color: #5e72e4;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center p-4">
    
    <div class="card shadow-lg" style="width: 100%; max-width: 480px;">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex justify-content-center align-items-center mb-3" style="width: 70px; height: 70px;">
                    <i class="fas fa-user-plus" style="font-size: 2rem;"></i>
                </div>
                <h3 class="fw-bold text-dark">Admin Register</h3>
                <p class="text-muted small">Tạo tài khoản quản trị mới</p>
            </div>

            <?php if (!empty($error)) { ?>
                <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <div><?php echo $error; ?></div>
                </div>
            <?php } elseif (!empty($success)) { ?>
                <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <div><?php echo $success; ?></div>
                </div>
            <?php } ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary small">Tên đăng nhập</label>
                    <div class="input-group">
                        <span class="input-group-text text-muted"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control" name="username" 
                               value="<?php echo htmlspecialchars($form_data["username"]); ?>" required placeholder="Nhập username...">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary small">Email</label>
                    <div class="input-group">
                        <span class="input-group-text text-muted"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control" name="email" 
                               value="<?php echo htmlspecialchars($form_data["email"]); ?>" required placeholder="Nhập email...">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary small">Họ và tên</label>
                    <div class="input-group">
                        <span class="input-group-text text-muted"><i class="fas fa-id-card"></i></span>
                        <input type="text" class="form-control" name="fullname" 
                               value="<?php echo htmlspecialchars($form_data["fullname"]); ?>" required placeholder="Nhập họ tên...">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary small">Mật khẩu</label>
                    <div class="input-group">
                        <span class="input-group-text text-muted"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" name="password" required placeholder="Nhập mật khẩu...">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold text-secondary small">Xác nhận mật khẩu</label>
                    <div class="input-group">
                        <span class="input-group-text text-muted"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" name="password_confirm" required placeholder="Nhập lại mật khẩu...">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">
                    TẠO TÀI KHOẢN <i class="fas fa-arrow-right ms-2"></i>
                </button>
            </form>

            <div class="text-center mt-4 pt-3 border-top">
                <a href="admin_login.php" class="text-decoration-none text-muted small hover-primary">
                    <i class="fas fa-sign-in-alt me-1"></i> Quay về trang đăng nhập
                </a>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
