<?php
// controller/SanphamController.php

// Thiết lập đường dẫn gốc
$root_path = dirname(__DIR__);

// 1. Nhúng các file cần thiết
require_once $root_path . '/config/connect.php';
require_once $root_path . '/classes/DB.class.php';
require_once $root_path . '/classes/Sanpham.class.php'; // Nhúng Sanpham Model
require_once $root_path . '/classes/Loaisp.class.php';  // Nhúng Loaisp Model (để lấy danh sách loại sản phẩm)

class SanphamController
{
    private $sanphamModel;
    private $loaispModel; // Dùng để lấy danh sách Loại sản phẩm

    public function __construct()
    {
        // Khởi tạo các model
        $this->sanphamModel = new Sanpham();
        $this->loaispModel = new Loaisp();
    }

    // ----------------------------------------------------------------------
    // PHƯƠNG THỨC ĐỌC (READ)
    // ----------------------------------------------------------------------

    /**
     * Phương thức chính để hiển thị danh sách sản phẩm.
     */
    public function index()
    {
        $sanphams = $this->sanphamModel->readAll();
        return [
            'sanphams' => $sanphams
        ];
    }

    /**
     * Lấy chi tiết thông tin một sản phẩm.
     */
    public function detail($id)
    {
        $sanpham = null;
        $message = null;

        if ($this->sanphamModel->readOne($id)) {
            $sanpham = (object)[
                'sanpham_id' => $this->sanphamModel->sanpham_id,
                'loai_id' => $this->sanphamModel->loai_id,
                'ncc_id' => $this->sanphamModel->ncc_id,
                'thuong_hieu' => $this->sanphamModel->thuong_hieu,
                'ten_loai' => $this->sanphamModel->ten_loai,
                'ten_ncc' => $this->sanphamModel->ten_ncc,
                'ten_sanpham' => $this->sanphamModel->ten_sanpham,
                'gia' => $this->sanphamModel->gia,
                'so_luong_ton' => $this->sanphamModel->so_luong_ton,
                'mo_ta' => $this->sanphamModel->mo_ta,
                'hinh_anh' => $this->sanphamModel->hinh_anh,
            ];
        } else {
            $message = "Lỗi: Không tìm thấy Sản phẩm có ID " . $id;
        }

        return [
            'sanpham' => $sanpham,
            'message' => $message
        ];
    }

    /**
     * Lấy danh sách các loại sản phẩm để hiển thị trong form Add/Edit.
     */
    public function getLoaiSanphams()
    {
        return $this->loaispModel->readAll();
    }


    // ----------------------------------------------------------------------
    // PHƯƠNG THỨC VALIDATE
    // ----------------------------------------------------------------------

    /**
     * Hàm validate dữ liệu (Dùng chung cho cả add và edit).
     */
    private function validateSanphamData($data, $file, $is_new = true)
    {
        $errors = [];
        $current_id = $data['sanpham_id'] ?? 0;

        // 1. KIỂM TRA CÁC TRƯỜNG BẮT BUỘC
        if (empty($data['ten_sanpham'])) $errors['ten_sanpham'] = 'Tên sản phẩm không được để trống.';
        if (empty($data['loai_id'])) $errors['loai_id'] = 'Vui lòng chọn Loại Sản phẩm.';
        if (!is_numeric($data['gia']) || $data['gia'] <= 0) $errors['gia'] = 'Giá bán phải là số dương.';
        if (!is_numeric($data['so_luong_ton']) || $data['so_luong_ton'] < 0) $errors['so_luong_ton'] = 'Tồn kho phải là số không âm.';

        // 2. KIỂM TRA TÍNH DUY NHẤT CỦA TÊN SP
        if (!isset($errors['ten_sanpham']) && $this->sanphamModel->checkTenSanphamExists($data['ten_sanpham'], $current_id)) {
            $errors['ten_sanpham'] = 'Tên sản phẩm này đã tồn tại.';
        }

        // 3. XỬ LÝ ẢNH (CHỈ KHI CÓ FILE MỚI HOẶC TRANG THÊM MỚI)
        $is_file_provided = isset($file['hinh_anh']) && $file['hinh_anh']['error'] === UPLOAD_ERR_OK;

        // 3. XỬ LÝ ẢNH (CHỈ KHI CÓ FILE MỚI HOẶC TRANG THÊM MỚI)
        // SỬA: Thay vì kiểm tra trực tiếp $file['hinh_anh']['error'], ta dùng biến đã kiểm tra sự tồn tại
        if ($is_new || $is_file_provided) {

            if ($is_new && !$is_file_provided) {
                // Nếu là trang thêm mới VÀ không có file, báo lỗi
                $errors['hinh_anh'] = 'Vui lòng chọn ảnh đại diện.';
            } else if ($is_file_provided) {
                $file_info = $file['hinh_anh'];
                $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
                $max_size = 5 * 1024 * 1024; // 5MB

                // Dòng này (tương đương dòng 105 cũ) được an toàn hơn
                if (!in_array($file_info['type'], $allowed_types)) {
                    $errors['hinh_anh'] = 'Chỉ chấp nhận file ảnh JPG, PNG, WEBP.';
                } elseif ($file_info['size'] > $max_size) {
                    $errors['hinh_anh'] = 'Kích thước ảnh không được vượt quá 5MB.';
                }
            }
        }

        return $errors;
    }

    // ----------------------------------------------------------------------
    // PHƯƠNG THỨC THAO TÁC (CRUD)
    // ----------------------------------------------------------------------

    /**
     * Phương thức xử lý thêm sản phẩm mới.
     */
    public function add($post_data)
    {
        $errors = $this->validateSanphamData($post_data, true);

        if (!empty($errors)) {
            return [
                'success' => false,
                'message' => 'Vui lòng kiểm tra lại các trường bị lỗi.',
                'errors' => $errors
            ];
        }

        if ($this->sanphamModel->create($post_data)) {
            // TODO: Thêm logic xử lý file upload ảnh (nếu có)
            return ['success' => true, 'message' => 'Thêm sản phẩm mới thành công!'];
        } else {
            return ['success' => false, 'message' => 'Đã xảy ra lỗi trong quá trình thêm vào CSDL.'];
        }
    }

    /**
     * Phương thức xử lý cập nhật sản phẩm.
     */
    public function edit($id, $post_data, $file_data)
    {
        $post_data['sanpham_id'] = $id;

        $current_sanpham = $this->detail($id)['sanpham'];
        if (!$current_sanpham) {
            return ['success' => false, 'message' => 'Sản phẩm không tồn tại.'];
        }
        $hinh_anh_hien_tai = $current_sanpham->hinh_anh;
        $errors = $this->validateSanphamData($post_data, $file_data, false);

        if (!empty($errors)) {
            return [
                'success' => false,
                'message' => 'Vui lòng kiểm tra lại các trường bị lỗi.',
                'errors' => $errors
            ];
        }

        $new_filename = $hinh_anh_hien_tai;
        if (isset($file_data['hinh_anh']) && $file_data['hinh_anh']['error'] === UPLOAD_ERR_OK) {
            $file_info = $file_data['hinh_anh'];

            $upload_dir = dirname(__DIR__) . '/assets/images/sanphams/';
            $ext = pathinfo($file_info['name'], PATHINFO_EXTENSION);
            $new_filename = 'sanpham_' . $id . '_' . time() . '.' . $ext;
            $target_file = $upload_dir . $new_filename;



            if (move_uploaded_file($file_info['tmp_name'], $target_file)) {

                if ($hinh_anh_hien_tai && $hinh_anh_hien_tai !== '1.webp') {
                    @unlink($upload_dir . $hinh_anh_hien_tai);
                }
            } else {


                return ['success' => false, 'message' => 'Lỗi khi tải lên file ảnh mới. Vui lòng kiểm tra quyền ghi của thư mục /assets/images/sanphams/.'];
            }
        }

        // 3. Chuẩn bị dữ liệu cập nhật
        $data_to_update = [
            'ten_sanpham' => $post_data['ten_sanpham'],
            'loai_id' => $post_data['loai_id'],
            'ncc_id' => $post_data['ncc_id'],
            'thuong_hieu' => $post_data['thuong_hieu'],
            'gia' => $post_data['gia'],
            'so_luong_ton' => $post_data['so_luong_ton'],
            'mo_ta' => $post_data['mo_ta'],
            'hinh_anh' => $new_filename,
        ];


        // 4. Gọi Model để cập nhật CSDL
        if ($this->sanphamModel->update($id, $data_to_update)) {
            return ['success' => true, 'message' => 'Cập nhật sản phẩm thành công!'];
        } else {
            return ['success' => false, 'message' => 'Đã xảy ra lỗi trong quá trình cập nhật vào CSDL.'];
        }
    }

    /**
     * Xử lý xóa sản phẩm.
     */
    public function deleteHandler($id)
    {
        if ((int)$id <= 0) {
            return ['success' => false, 'message' => 'ID sản phẩm không hợp lệ.'];
        }

        if ($this->sanphamModel->delete($id)) { // Gọi Model
            return ['success' => true, 'message' => 'Xóa sản phẩm thành công!'];
        } else {
            // Nếu Model trả về FALSE
            return ['success' => false, 'message' => 'Lỗi: Không thể xóa sản phẩm hoặc dữ liệu liên quan. Vui lòng kiểm tra các khóa ngoại.'];
        }
    }
}
