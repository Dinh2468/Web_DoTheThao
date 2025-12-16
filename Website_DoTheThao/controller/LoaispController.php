<?php
// controller/loaiController.php

// Thiết lập đường dẫn gốc
$root_path = dirname(__DIR__);

// 1. Nhúng các file cần thiết
require_once $root_path . '/config/connect.php';
require_once $root_path . '/classes/DB.class.php';
require_once $root_path . '/classes/loaisp.class.php'; // Nhúng loai Model

class loaispController
{
    private $loaiModel;

    public function __construct()
    {
        // Khởi tạo model loai
        $this->loaiModel = new Loaisp();
    }

    // ----------------------------------------------------------------------
    // PHƯƠNG THỨC ĐỌC (READ)
    // ----------------------------------------------------------------------

    /**
     * Phương thức chính để hiển thị danh sách loại sản phẩm.
     */
    public function index()
    {
        $loai_sanphams = $this->loaiModel->readAll();

        // Đảm bảo phải trả về mảng với khóa là 'loai_sanphams'
        return [
            'loai_sanphams' => $loai_sanphams
        ];
    }

    /**
     * Lấy chi tiết thông tin một loại sản phẩm.
     */
    public function detail($id)
    {
        $loai = null;
        $message = null;

        if ($this->loaiModel->readOne($id)) {

            $loai = (object)[
                'loai_id' => $this->loaiModel->loai_id,
                'ten_loai' => $this->loaiModel->ten_loai,
                'mo_ta' => $this->loaiModel->mo_ta,
            ];
        } else {
            $message = "Lỗi: Không tìm thấy Loại Sản phẩm có ID " . $id;
        }

        return [
            'loai' => $loai,
            'message' => $message
        ];
    }

    // ----------------------------------------------------------------------
    // PHƯƠNG THỨC VALIDATE
    // ----------------------------------------------------------------------

    /**
     * Hàm validate dữ liệu (Dùng chung cho cả add và edit).
     */
    private function validateloaiData($data, $is_new = true)
    {
        $errors = [];
        $current_id = $data['loai_id'] ?? 0;

        // 1. KIỂM TRA TÊN LOẠI (Bắt buộc)
        if (empty($data['ten_loai'])) {
            $errors['ten_loai'] = 'Tên loại sản phẩm không được để trống.';
        } else {
            // 2. KIỂM TRA TÍNH DUY NHẤT CỦA TÊN LOẠI
            // Sử dụng hàm checkTenLoaiExists trong Model
            if ($this->loaiModel->checkTenLoaiExists($data['ten_loai'], $current_id)) {
                $errors['ten_loai'] = 'Tên loại sản phẩm này đã tồn tại.';
            }
        }

        // Mô tả (mo_ta) không bắt buộc, nên không cần kiểm tra empty

        return $errors;
    }

    // ----------------------------------------------------------------------
    // PHƯƠNG THỨC THAO TÁC (CRUD)
    // ----------------------------------------------------------------------

    /**
     * Phương thức xử lý thêm loại sản phẩm mới.
     */
    public function add($post_data)
    {
        $errors = $this->validateloaiData($post_data, true);

        if (!empty($errors)) {
            return [
                'success' => false,
                'message' => 'Vui lòng kiểm tra lại các trường bị lỗi.',
                'errors' => $errors
            ];
        }

        if ($this->loaiModel->create($post_data)) {
            return ['success' => true, 'message' => 'Thêm loại sản phẩm mới thành công!'];
        } else {
            return ['success' => false, 'message' => 'Đã xảy ra lỗi trong quá trình thêm vào CSDL.'];
        }
    }

    /**
     * Phương thức xử lý cập nhật loại sản phẩm.
     */
    public function edit($id, $post_data)
    {
        // Gán ID vào data để hàm validate biết loại trừ ID hiện tại
        $post_data['loai_id'] = $id;

        $errors = $this->validateloaiData($post_data, false);

        if (!empty($errors)) {
            return [
                'success' => false,
                'message' => 'Vui lòng kiểm tra lại các trường bị lỗi.',
                'errors' => $errors
            ];
        }

        if ($this->loaiModel->update($id, $post_data)) {
            return ['success' => true, 'message' => 'Cập nhật loại sản phẩm thành công!'];
        } else {
            return ['success' => false, 'message' => 'Đã xảy ra lỗi trong quá trình cập nhật vào CSDL.'];
        }
    }

    /**
     * Xử lý xóa loại sản phẩm.
     */
    public function deleteHandler($id)
    {
        if ((int)$id <= 0) {
            return ['success' => false, 'message' => 'ID loại sản phẩm không hợp lệ.'];
        }

        // TODO: Cần kiểm tra xem có sản phẩm nào thuộc loại này hay không trước khi xóa.
        // Nếu có, nên ngăn chặn xóa hoặc yêu cầu chuyển sản phẩm sang loại khác.

        if ($this->loaiModel->delete($id)) {
            return ['success' => true, 'message' => 'Xóa loại sản phẩm thành công!'];
        } else {
            return ['success' => false, 'message' => 'Lỗi: Không thể xóa loại sản phẩm hoặc dữ liệu liên quan.'];
        }
    }
}
