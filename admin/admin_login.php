<?php
/* ===== CẤU HÌNH SESSION (PHẢI TRƯỚC session_start) ===== */
ini_set('session.cookie_lifetime', 0);   // đóng trình duyệt là logout
ini_set('session.gc_maxlifetime', 300);   // 30 giây không thao tác → logout


session_start();
require_once '../config/connect.php';

/* ===== NẾU ĐÃ LOGIN ADMIN → VÀO DASHBOARD ===== */
if (isset($_SESSION['MaAdmin'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$username = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Vui lòng nhập đầy đủ thông tin!';
    } else {

        $stmt = $conn->prepare(
            "SELECT * FROM taikhoan
             WHERE ten_dangnhap = ?
             AND mat_khau = MD5(?)
             AND vai_tro = 'admin'
             AND trang_thai = 1"
        );

        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();

        if ($admin) {
            $_SESSION['MaAdmin']  = $admin['taikhoan_id'];
            $_SESSION['TenAdmin'] = $admin['ten_dangnhap'];

            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Sai tài khoản hoặc mật khẩu!";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập Quản trị - Fashion Shop</title>
    
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
    
    <div class="card shadow-lg" style="width: 100%; max-width: 420px;">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex justify-content-center align-items-center mb-3" style="width: 70px; height: 70px;">
                    <i class="fas fa-user-shield" style="font-size: 2rem;"></i>
                </div>
                <h3 class="fw-bold text-dark">Admin Login</h3>
                <p class="text-muted small">Hệ thống quản lý Fashion Shop</p>
            </div>
            
            <?php if (!empty($error)) { ?>
                <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <div><?php echo $error; ?></div>
                </div>
            <?php } ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary small">Tên đăng nhập</label>
                    <div class="input-group">
                        <span class="input-group-text text-muted"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control" name="username" 
                               value="<?php echo htmlspecialchars($username); ?>" required placeholder="Nhập username...">
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label fw-bold text-secondary small">Mật khẩu</label>
                    <div class="input-group">
                        <span class="input-group-text text-muted"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" name="password" required placeholder="Nhập mật khẩu...">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">
                    ĐĂNG NHẬP <i class="fas fa-arrow-right ms-2"></i>
                </button>
            </form>
            
            <div class="text-center mt-4 pt-3 border-top">
                <a href="../index.php" class="text-decoration-none text-muted small hover-primary">
                    <i class="fas fa-home me-1"></i> Quay về trang chủ
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>