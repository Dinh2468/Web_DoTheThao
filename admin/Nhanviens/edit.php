<?php
// admin/Nhanviens/edit.php

require_once '../includes/admin_header.php';
require_once '../../controller/NhanvienController.php';

$controller = new NhanvienController();
$nhanvien_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$errors = [];
$form_data = [];
$message = null;
$message_success = false;
$nhanvien = null; // Biến lưu trữ dữ liệu nhân viên ban đầu

// 1. Logic TẢI DỮ LIỆU BAN ĐẦU
$data_detail = $controller->detail($nhanvien_id);
$nhanvien = $data_detail['nhanvien'];

if (!$nhanvien) {
    // Nếu không tìm thấy nhân viên, hiển thị lỗi và dừng
    $message = $data_detail['message'];
} else {
    // Gán dữ liệu nhân viên ban đầu vào form_data để hiển thị
    $form_data = (array)$nhanvien;

    // === CHUẨN HÓA NGÀY: Đảm bảo format Y-m-d cho input type="date" ===
    if (!empty($form_data['ngay_vao_lam'])) {
        // Sử dụng DateTime object để đảm bảo việc chuyển đổi chính xác
        try {
            $date = new DateTime($form_data['ngay_vao_lam']);
            $form_data['ngay_vao_lam'] = $date->format('Y-m-d');
        } catch (Exception $e) {
            // Nếu có lỗi định dạng, đặt về rỗng để validation bắt lỗi
            $form_data['ngay_vao_lam'] = '';
        }
    }
}


// 2. Logic XỬ LÝ POST (Cập nhật)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ghi đè form_data bằng dữ liệu POST để giữ lại dữ liệu người dùng nhập
    $form_data = $_POST;
    if (!empty($form_data['ngay_vao_lam'])) {
        try {
            // Lấy giá trị từ POST (nếu nó là Y-m-d thì không thay đổi, nếu là định dạng khác thì chuyển)
            $date = new DateTime($form_data['ngay_vao_lam']);
            $form_data['ngay_vao_lam'] = $date->format('Y-m-d');
        } catch (Exception $e) {
            // Nếu dữ liệu ngày gửi lên bị lỗi định dạng, set về rỗng
            $form_data['ngay_vao_lam'] = '';
        }
    } else {
        // Nếu không có giá trị, đặt thành rỗng để validation bắt lỗi
        $form_data['ngay_vao_lam'] = '';
    }

    // Đảm bảo ID được truyền vào form_data cho validate
    $form_data['nhanvien_id'] = $nhanvien_id;

    // Gọi hàm edit trong Controller
    $result = $controller->edit($nhanvien_id, $form_data);

    if ($result['success']) {
        // ... (Chuyển hướng thành công) ...
    } else {
        $message = $result['message'];
        $message_success = false;
        $errors = $result['errors'] ?? [];

        // KHÔNG CẦN tải lại $nhanvien (đã làm ở đầu file)
        // Dữ liệu lỗi đã nằm trong $form_data, và nó đã được chuẩn hóa ngày.
    }
    // Đảm bảo ID được truyền vào form_data cho validate
    $form_data['nhanvien_id'] = $nhanvien_id;

    // Gọi hàm edit trong Controller
    $result = $controller->edit($nhanvien_id, $form_data);



    if ($result['success']) {
        // Chuyển hướng về trang chi tiết với thông báo thành công
        header("Location: detail.php?id=$nhanvien_id&status=success_edit");
        exit();
    } else {
        $message = $result['message'];
        $message_success = false;
        $errors = $result['errors'] ?? [];

        // Cần tải lại dữ liệu chi tiết sau khi cập nhật thất bại
        $data_detail = $controller->detail($nhanvien_id);
        $nhanvien = $data_detail['nhanvien'];
    }
}
?>

<h1>Chỉnh sửa Nhân viên: #<?php echo htmlspecialchars($nhanvien_id); ?></h1>
<p><a href="detail.php?id=<?php echo htmlspecialchars($nhanvien_id); ?>">← Quay lại chi tiết</a></p>

<?php if ($message): ?>
    <div class="alert <?php echo $message_success ? 'completed' : 'cancelled'; ?>">
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<?php if ($nhanvien): ?>
    <div class="form-container">
        <form action="edit.php?id=<?php echo htmlspecialchars($nhanvien_id); ?>" method="POST">

            <h3>Thông tin Đăng nhập (Mật khẩu)</h3>
            <br>

            <div class="form-group">
                <label>Tên đăng nhập:</label>
                <p><strong><?php echo htmlspecialchars($nhanvien->ten_dangnhap ?? ''); ?></strong></p>
            </div>

            <div class="form-group">
                <label for="mat_khau">Mật khẩu mới:</label>
                <input type="password" id="mat_khau" name="mat_khau" placeholder="Để trống nếu không thay đổi">
                <?php if (isset($errors['mat_khau'])): ?><span class="error-message"><?php echo $errors['mat_khau']; ?></span><?php endif; ?>
            </div>

            <hr>

            <h3>Thông tin Nhân viên</h3>

            <div class="form-group">
                <label for="ho_ten">Họ Tên: <span style="color: red;">*</span></label>
                <input type="text" id="ho_ten" name="ho_ten" value="<?php echo htmlspecialchars($form_data['ho_ten'] ?? ''); ?>">
                <?php if (isset($errors['ho_ten'])): ?><span class="error-message"><?php echo $errors['ho_ten']; ?></span><?php endif; ?>
            </div>

            <div class="form-group">
                <label for="chuc_vu">Chức vụ: <span style="color: red;">*</span></label>
                <input type="text" id="chuc_vu" name="chuc_vu" value="<?php echo htmlspecialchars($form_data['chuc_vu'] ?? ''); ?>">
                <?php if (isset($errors['chuc_vu'])): ?><span class="error-message"><?php echo $errors['chuc_vu']; ?></span><?php endif; ?>
            </div>

            <div class="form-group">
                <label for="email">Email: <span style="color: red;">*</span></label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>">
                <?php if (isset($errors['email'])): ?><span class="error-message"><?php echo $errors['email']; ?></span><?php endif; ?>
            </div>

            <div class="form-group">
                <label for="sdt">Số điện thoại: <span style="color: red;">*</span></label>
                <input type="text" id="sdt" name="sdt" value="<?php echo htmlspecialchars($form_data['sdt'] ?? ''); ?>">
                <?php if (isset($errors['sdt'])): ?><span class="error-message"><?php echo $errors['sdt']; ?></span><?php endif; ?>
            </div>

            <div class="form-group">
                <label for="ngay_vao_lam">Ngày vào làm: <span style="color: red;">*</span></label>

                <input type="date" id="ngay_vao_lam" name="ngay_vao_lam" value="<?php echo htmlspecialchars($form_data['ngay_vao_lam'] ?? ''); ?>">

                <?php if (isset($errors['ngay_vao_lam'])): ?><span class="error-message"><?php echo $errors['ngay_vao_lam']; ?></span><?php endif; ?>
            </div>

            <input type="hidden" name="nhanvien_id" value="<?php echo htmlspecialchars($nhanvien_id); ?>">

            <button type="submit" class="export-btn" style="background-color: #007bff;">
                <i class="fa fa-save"></i> Cập nhật Nhân viên
            </button>
        </form>
    </div>
<?php endif; ?>

<?php require_once '../includes/admin_footer.php'; ?>