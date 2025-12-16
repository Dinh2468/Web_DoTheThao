<?php
// controller/NhacungcapController.php

// Thiết lập đường dẫn gốc
$root_path = dirname(__DIR__);

// 1. Nhúng các file cần thiết
require_once $root_path . '/config/connect.php';
require_once $root_path . '/classes/DB.class.php';
require_once $root_path . '/classes/NhaCungCap.class.php'; // Nhúng NhaCungCap Model

class NhacungcapController
{
    private $nhacungcapModel;

    public function __construct()
    {
        // Khởi tạo model NhaCungCap
        $this->nhacungcapModel = new Nhacungcap();
    }

    // ----------------------------------------------------------------------
    // PHƯƠNG THỨC ĐỌC (READ)
    // ----------------------------------------------------------------------

    /**
     * Phương thức chính để hiển thị danh sách nhà cung cấp.
     */
    public function index()
    {
        $nhacungcaps = $this->nhacungcapModel->readAll();
        return [
            'nhacungcaps' => $nhacungcaps
        ];
    }

    /**
     * Lấy chi tiết thông tin một nhà cung cấp.
     */
    public function detail($id)
    {
        $nhacungcap = null;
        $message = null;

        if ($this->nhacungcapModel->readOne($id)) {
            // Lấy dữ liệu từ thuộc tính của model sau khi readOne
            $nhacungcap = (object)[
                'ncc_id' => $this->nhacungcapModel->ncc_id,
                'ten_ncc' => $this->nhacungcapModel->ten_ncc,
                'dien_thoai' => $this->nhacungcapModel->dien_thoai,
                'dia_chi' => $this->nhacungcapModel->dia_chi,
            ];
        } else {
            $message = "Lỗi: Không tìm thấy Nhà Cung Cấp có ID " . $id;
        }

        return [
            'nhacungcap' => $nhacungcap,
            'message' => $message
        ];
    }

    // ----------------------------------------------------------------------
    // PHƯƠNG THỨC VALIDATE
    // ----------------------------------------------------------------------

    /**
     * Hàm validate dữ liệu (Dùng chung cho cả add và edit).
     */
    private function validateNhaCungCapData($data, $is_new = true)
    {
        $errors = [];
        // SỬA: Dùng ncc_id
        $current_id = $data['ncc_id'] ?? 0;

        if (empty($data['ten_ncc'])) $errors['ten_ncc'] = 'Tên nhà cung cấp không được để trống.';
        if (empty($data['dien_thoai'])) $errors['dien_thoai'] = 'Số điện thoại không được để trống.';
        if (!isset($errors['ten_ncc']) && $this->nhacungcapModel->checkTenNhaCungCapExists($data['ten_ncc'], $current_id)) {
            $errors['ten_ncc'] = 'Tên nhà cung cấp này đã tồn tại.';

            if (!empty($data['dien_thoai']) && !preg_match('/^\d{10,11}$/', $data['dien_thoai'])) {
                $errors['dien_thoai'] = 'Số điện thoại không hợp lệ (chỉ chấp nhận 10-11 số).';
            }

            return $errors;
        }
    }

    // ----------------------------------------------------------------------
    // PHƯƠNG THỨC THAO TÁC (CRUD)
    // ----------------------------------------------------------------------

    /**
     * Phương thức xử lý thêm nhà cung cấp mới.
     */
    public function add($post_data)
    {
        $post_data['ten_ncc'] = $post_data['ten_ncc'] ?? null;
        $post_data['dien_thoai'] = $post_data['dien_thoai'] ?? null;

        $errors = $this->validateNhaCungCapData($post_data, true);

        if (!empty($errors)) {
            return [
                'success' => false,
                'message' => 'Vui lòng kiểm tra lại các trường bị lỗi.',
                'errors' => $errors
            ];
        }

        if ($this->nhacungcapModel->create($post_data)) {
            return ['success' => true, 'message' => 'Thêm nhà cung cấp mới thành công!'];
        } else {
            return ['success' => false, 'message' => 'Đã xảy ra lỗi trong quá trình thêm vào CSDL.'];
        }
    }

    /**
     * Phương thức xử lý cập nhật nhà cung cấp.
     */
    public function edit($id, $post_data)
    {
        $post_data['ncc_id'] = $id;
        $post_data['ten_ncc'] = $post_data['ten_ncc'] ?? null;
        $post_data['dien_thoai'] = $post_data['dien_thoai'] ?? null;

        $errors = $this->validateNhaCungCapData($post_data, false);

        if (!empty($errors)) {
            return [
                'success' => false,
                'message' => 'Vui lòng kiểm tra lại các trường bị lỗi.',
                'errors' => $errors
            ];
        }

        if ($this->nhacungcapModel->update($id, $post_data)) {
            return ['success' => true, 'message' => 'Cập nhật nhà cung cấp thành công!'];
        } else {
            return ['success' => false, 'message' => 'Đã xảy ra lỗi trong quá trình cập nhật vào CSDL.'];
        }
    }

    /**
     * Xử lý xóa nhà cung cấp.
     */
    public function deleteHandler($id)
    {
        if ((int)$id <= 0) {
            return ['success' => false, 'message' => 'ID nhà cung cấp không hợp lệ.'];
        }
        if ($this->nhacungcapModel->delete($id)) {
            return ['success' => true, 'message' => 'Xóa nhà cung cấp thành công!'];
        } else {
            return ['success' => false, 'message' => 'Lỗi: Không thể xóa nhà cung cấp hoặc dữ liệu liên quan.'];
        }
    }
}
