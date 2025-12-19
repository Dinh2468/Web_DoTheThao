<?php
// controller/KhachhangController.php
$root_path = dirname(__DIR__);
require_once $root_path . '/config/connect.php';
require_once $root_path . '/classes/DB.class.php';
require_once $root_path . '/classes/Khachhang.class.php';
class KhachhangController
{
    private $khachhangModel;
    public function __construct()
    {
        $this->khachhangModel = new Khachhang();
    }
    public function index()
    {
        $khachhangs = $this->khachhangModel->readAll();
        $data = [
            'khachhangs' => $khachhangs
        ];
        return $data;
    }
    public function add($post_data)
    {
        $errors = $this->validateKhachhangData($post_data, true); // true: cần mật khẩu
        if (!empty($errors)) {
            return ['errors' => $errors, 'success' => false, 'message' => 'Vui lòng kiểm tra lại các lỗi.'];
        }
        if ($this->khachhangModel->create($post_data)) {
            return ['success' => true, 'message' => 'Thêm khách hàng mới thành công!'];
        } else {
            return ['success' => false, 'message' => 'Đã xảy ra lỗi trong quá trình thêm vào CSDL.'];
        }
    }
    public function edit($id, $post_data)
    {
        $errors = $this->validateKhachhangData($post_data, false); // false: không bắt buộc mật khẩu
        if (!empty($errors)) {
            return ['errors' => $errors, 'success' => false, 'message' => 'Vui lòng kiểm tra lại các lỗi.'];
        }
        if ($this->khachhangModel->update($id, $post_data)) {
            return ['success' => true, 'message' => '✅ Cập nhật khách hàng thành công!'];
        } else {
            return ['success' => false, 'message' => 'Đã xảy ra lỗi trong quá trình cập nhật CSDL.'];
        }
    }
    private function validateKhachhangData($data, $is_new = true)
    {
        $errors = [];
        if (empty($data['ho_ten'])) $errors['ho_ten'] = 'Họ tên không được để trống.';
        if (empty($data['email'])) $errors['email'] = 'Email không được để trống.';
        if (empty($data['dien_thoai'])) $errors['dien_thoai'] = 'Điện thoại không được để trống.';
        if (empty($data['dia_chi'])) $errors['dia_chi'] = 'Địa chỉ không được để trống.';
        if (empty($data['ten_dangnhap'])) $errors['ten_dangnhap'] = 'Tên đăng nhập không được để trống.';
        $current_id = $data['khachhang_id'] ?? 0;
        if ($is_new && $this->khachhangModel->checkUsernameExists($data['ten_dangnhap'])) {
            $errors['ten_dangnhap'] = 'Tên đăng nhập đã tồn tại trong hệ thống.';
        }
        if ($this->khachhangModel->checkEmailExists($data['email'], $current_id)) {
            $errors['email'] = 'Email đã được sử dụng cho một khách hàng khác.';
        }
        if ($is_new || !empty($data['mat_khau'])) {
            if (empty($data['mat_khau'])) $errors['mat_khau'] = 'Mật khẩu không được để trống.';
            else if (strlen($data['mat_khau']) < 6) $errors['mat_khau'] = 'Mật khẩu phải từ 6 ký tự trở lên.';
        }
        return $errors;
    }
    public function detail($id)
    {
        $khachhang = null;
        $message = null;
        if ($this->khachhangModel->readOne($id)) {
            $khachhang = (object)[
                'khachhang_id' => $this->khachhangModel->khachhang_id,
                'ho_ten' => $this->khachhangModel->ho_ten,
                'email' => $this->khachhangModel->email,
                'dien_thoai' => $this->khachhangModel->dien_thoai,
                'dia_chi' => $this->khachhangModel->dia_chi,
                'ten_dangnhap' => $this->khachhangModel->ten_dangnhap,
                'trang_thai' => $this->khachhangModel->trang_thai,
            ];
        } else {
            $message = "Lỗi: Không tìm thấy Khách hàng có ID " . $id;
        }
        return [
            'khachhang' => $khachhang,
            'message' => $message
        ];
    }
    public function deleteHandler($id)
    {
        if ((int)$id <= 0) {
            return ['success' => false, 'message' => 'ID khách hàng không hợp lệ.'];
        }
        if ($this->khachhangModel->delete($id)) {
            return ['success' => true, 'message' => 'Xóa khách hàng thành công!'];
        } else {
            return ['success' => false, 'message' => 'Lỗi: Không thể xóa khách hàng hoặc các dữ liệu liên quan.'];
        }
    }
    public function lockHandler($id)
    {
        if ((int)$id <= 0) {
            return ['success' => false, 'message' => 'ID khách hàng không hợp lệ.'];
        }
        if ($this->khachhangModel->updateStatus($id, 0)) {
            return ['success' => true, 'message' => 'Khóa tài khoản thành công!'];
        } else {
            return ['success' => false, 'message' => 'Lỗi: Không thể khóa tài khoản.'];
        }
    }
    public function unlockHandler($id)
    {
        if ((int)$id <= 0) {
            return ['success' => false, 'message' => 'ID khách hàng không hợp lệ.'];
        }
        if ($this->khachhangModel->updateStatus($id, 1)) {
            return ['success' => true, 'message' => 'Mở khóa tài khoản thành công!'];
        } else {
            return ['success' => false, 'message' => 'Lỗi: Không thể mở khóa tài khoản.'];
        }
    }
}
