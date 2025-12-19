<?php
// controller/loaiController.php
$root_path = dirname(__DIR__);
require_once $root_path . '/config/connect.php';
require_once $root_path . '/classes/DB.class.php';
require_once $root_path . '/classes/loaisp.class.php'; // Nhúng loai Model
class loaispController
{
    private $loaiModel;
    public function __construct()
    {
        $this->loaiModel = new Loaisp();
    }
    public function index()
    {
        $loai_sanphams = $this->loaiModel->readAll();
        return [
            'loai_sanphams' => $loai_sanphams
        ];
    }
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
    // PHƯƠNG THỨC VALIDATE
    private function validateloaiData($data, $is_new = true)
    {
        $errors = [];
        $current_id = $data['loai_id'] ?? 0;
        if (empty($data['ten_loai'])) {
            $errors['ten_loai'] = 'Tên loại sản phẩm không được để trống.';
        } else {
            if ($this->loaiModel->checkTenLoaiExists($data['ten_loai'], $current_id)) {
                $errors['ten_loai'] = 'Tên loại sản phẩm này đã tồn tại.';
            }
        }
        return $errors;
    }
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
    public function edit($id, $post_data)
    {
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
    public function deleteHandler($id)
    {
        if ((int)$id <= 0) {
            return ['success' => false, 'message' => 'ID loại sản phẩm không hợp lệ.'];
        }
        if ($this->loaiModel->delete($id)) {
            return ['success' => true, 'message' => 'Xóa loại sản phẩm thành công!'];
        } else {
            return ['success' => false, 'message' => 'Lỗi: Không thể xóa loại sản phẩm hoặc dữ liệu liên quan.'];
        }
    }
}
