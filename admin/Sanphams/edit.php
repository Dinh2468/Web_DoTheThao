<?php
// admin/Sanphams/edit.php
require_once '../includes/admin_header.php';
require_once '../../controller/SanphamController.php';
require_once '../../controller/LoaispController.php';
require_once '../../controller/NhacungcapController.php';
$sanphamController = new SanphamController();
$loaispController = new LoaispController();
$nccController = new NhaCungCapController();
$sanpham_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$current_data = null;
$loai_sanphams = $loaispController->index()['loai_sanphams'];
$nha_cung_caps = $nccController->index()['nhacungcaps'];
if ($sanpham_id > 0) {
    $detail_result = $sanphamController->detail($sanpham_id);
    $current_data = $detail_result['sanpham'];
    if (!$current_data) {
        $_SESSION['error_message'] = "Không tìm thấy Sản phẩm cần chỉnh sửa.";
        header('Location: index.php');
        exit();
    }
} else {
    $_SESSION['error_message'] = "ID Sản phẩm không hợp lệ.";
    header('Location: index.php');
    exit();
}
$form_data = [
    'sanpham_id' => $current_data->sanpham_id,
    'ten_sanpham' => $current_data->ten_sanpham,
    'loai_id' => $current_data->loai_id,
    'ncc_id' => $current_data->ncc_id,
    'thuong_hieu' => $current_data->thuong_hieu,
    'gia' => $current_data->gia,
    'so_luong_ton' => $current_data->so_luong_ton,
    'mo_ta' => $current_data->mo_ta,
    'hinh_anh_hien_tai' => $current_data->hinh_anh,
];
$errors = [];
$message = null;
$message_type = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_data = $_POST;
    $post_data['hinh_anh_hien_tai'] = $form_data['hinh_anh_hien_tai'];
    $update_result = $sanphamController->edit($sanpham_id, $post_data, $_FILES);
    if ($update_result['success']) {
        $_SESSION['success_message'] = $update_result['message'];
        header('Location: index.php?status=success_edit');
        exit();
    } else {
        $errors = $update_result['errors'] ?? [];
        $message = $update_result['message'];
        $message_type = 'error';
        $form_data = array_merge($form_data, $post_data);
    }
}
?>
<h1>Chỉnh sửa Sản phẩm: <?php echo htmlspecialchars($current_data->ten_sanpham); ?></h1>
<div class="form-container">
    <?php if ($message): ?>
        <div class="alert <?php echo $message_type === 'error' ? 'pending' : 'completed'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>
    <form action="edit.php?id=<?php echo $sanpham_id; ?>" method="POST" enctype="multipart/form-data" class="custom-form">
        <div class="form-row-group">
            <div class="form-column">
                <div class="form-group">
                    <label for="ten_sanpham">Tên Sản phẩm <span class="required">*</span></label>
                    <input type="text" id="ten_sanpham" name="ten_sanpham"
                        value="<?php echo htmlspecialchars($form_data['ten_sanpham'] ?? ''); ?>" required>
                    <?php if (isset($errors['ten_sanpham'])): ?>
                        <span class="error-message"><?php echo htmlspecialchars($errors['ten_sanpham']); ?></span>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="loai_id">Loại Sản phẩm <span class="required">*</span></label>
                    <select id="loai_id" name="loai_id" required>
                        <option value="">-- Chọn Loại SP --</option>
                        <?php foreach ($loai_sanphams as $loai): ?>
                            <option value="<?php echo htmlspecialchars($loai->loai_id); ?>"
                                <?php echo ($form_data['loai_id'] == $loai->loai_id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($loai->ten_loai); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['loai_id'])): ?>
                        <span class="error-message"><?php echo htmlspecialchars($errors['loai_id']); ?></span>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="ncc_id">Nhà Cung Cấp</label>
                    <select id="ncc_id" name="ncc_id">
                        <option value="">-- Chọn NCC (Không bắt buộc) --</option>
                        <?php foreach ($nha_cung_caps as $ncc): ?>
                            <option value="<?php echo htmlspecialchars($ncc->ncc_id); ?>"
                                <?php echo ($form_data['ncc_id'] == $ncc->ncc_id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($ncc->ten_ncc); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="thuong_hieu">Thương hiệu</label>
                    <input type="text" id="thuong_hieu" name="thuong_hieu"
                        value="<?php echo htmlspecialchars($form_data['thuong_hieu'] ?? ''); ?>">
                </div>
            </div>
            <div class="form-column">
                <div class="form-group">
                    <label for="gia">Giá bán (VNĐ) <span class="required">*</span></label>
                    <input type="number" id="gia" name="gia"
                        value="<?php echo htmlspecialchars($form_data['gia'] ?? ''); ?>" min="0" step="1000" required>
                    <?php if (isset($errors['gia'])): ?>
                        <span class="error-message"><?php echo htmlspecialchars($errors['gia']); ?></span>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="so_luong_ton">Số lượng Tồn kho <span class="required">*</span></label>
                    <input type="number" id="so_luong_ton" name="so_luong_ton"
                        value="<?php echo htmlspecialchars($form_data['so_luong_ton'] ?? ''); ?>" min="0" required>
                    <?php if (isset($errors['so_luong_ton'])): ?>
                        <span class="error-message"><?php echo htmlspecialchars($errors['so_luong_ton']); ?></span>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="hinh_anh">Hình ảnh chính (Đổi ảnh)</label>
                    <input type="file" id="hinh_anh" name="hinh_anh" accept="image/*">
                    <p class="current-image-note">Ảnh hiện tại:
                        <?php echo htmlspecialchars($form_data['hinh_anh_hien_tai'] ?? 'Chưa có ảnh'); ?>
                        <?php if ($form_data['hinh_anh_hien_tai']): ?>
                            <a href="<?php echo BASE_URL . 'images/sanphams/' . htmlspecialchars($form_data['hinh_anh_hien_tai']); ?>" target="_blank">(Xem)</a>
                        <?php endif; ?>
                    </p>
                    <?php if (isset($errors['hinh_anh'])): ?>
                        <span class="error-message"><?php echo htmlspecialchars($errors['hinh_anh']); ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="form-group full-width-group">
            <label for="mo_ta">Mô tả Sản phẩm</label>
            <textarea id="mo_ta" name="mo_ta" rows="6"><?php echo htmlspecialchars($form_data['mo_ta'] ?? ''); ?></textarea>
        </div>
        <div class="form-actions">
            <button type="submit" class="export-btn" style="background-color: #007bff;"><i class="fa fa-save"></i> Cập nhật Sản phẩm</button>
            <a href="index.php" class="export-btn" style="background-color: #6c757d;"><i class="fa fa-arrow-left"></i> Hủy và Quay lại</a>
        </div>
    </form>
</div>
<?php
require_once '../includes/admin_footer.php';
?>