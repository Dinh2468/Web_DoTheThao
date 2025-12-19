<?php
// controller/NhanvienController.php
$root_path = dirname(__DIR__);
require_once $root_path . '/config/connect.php';
require_once $root_path . '/classes/DB.class.php';
require_once $root_path . '/classes/Nhanvien.class.php';
class NhanvienController
{
    private $nhanvienModel;
    public function __construct()
    {
        $this->nhanvienModel = new Nhanvien();
    }
    public function index()
    {
        $nhanviens = $this->nhanvienModel->readAll();
        return [
            'nhanviens' => $nhanviens
        ];
    }
    public function detail($id)
    {
        $nhanvien = null;
        $message = null;
        if ($this->nhanvienModel->readOne($id)) {
            $nhanvien = (object)[
                'nhanvien_id' => $this->nhanvienModel->nhanvien_id,
                'ho_ten' => $this->nhanvienModel->ho_ten,
                'email' => $this->nhanvienModel->email,
                'sdt' => $this->nhanvienModel->sdt,
                'chuc_vu' => $this->nhanvienModel->chuc_vu,
                'ngay_vao_lam' => $this->nhanvienModel->ngay_vao_lam,
                'ten_dangnhap' => $this->nhanvienModel->ten_dangnhap,
                'trang_thai' => $this->nhanvienModel->trang_thai,
            ];
        } else {
            $message = "Lỗi: Không tìm thấy Nhân viên có ID " . $id;
        }
        return [
            'nhanvien' => $nhanvien,
            'message' => $message
        ];
    }
    private function validateNhanvienData($data, $is_new = true)
    {
        $errors = [];
        $current_id = $data['nhanvien_id'] ?? 0;
        if (empty($data['ho_ten'])) $errors['ho_ten'] = 'Họ tên không được để trống.';
        if (empty($data['email'])) $errors['email'] = 'Email không được để trống.';
        if (empty($data['sdt'])) $errors['sdt'] = 'Số điện thoại không được để trống.';
        if (empty($data['chuc_vu'])) $errors['chuc_vu'] = 'Chức vụ không được để trống.';
        if (empty($data['ngay_vao_lam'])) $errors['ngay_vao_lam'] = 'Ngày vào làm không được để trống.';
        if (empty($data['ten_dangnhap'])) {
            $errors['ten_dangnhap'] = 'Tên đăng nhập không được để trống.';
        } else {
            if ($this->nhanvienModel->checkUsernameExists($data['ten_dangnhap'], $current_id)) {
                $errors['ten_dangnhap'] = 'Tên đăng nhập đã tồn tại trong hệ thống.';
            }
        }
        if ($is_new) {
            if (empty($data['mat_khau'])) {
                $errors['mat_khau'] = 'Mật khẩu không được để trống.';
            } elseif (strlen($data['mat_khau']) < 6) {
                $errors['mat_khau'] = 'Mật khẩu phải từ 6 ký tự trở lên.';
            }
        } else {
            if (!empty($data['mat_khau']) && strlen($data['mat_khau']) < 6) {
                $errors['mat_khau'] = 'Mật khẩu mới phải từ 6 ký tự trở lên.';
            }
        }
    }
    public function add($post_data)
    {
        $errors = $this->validateNhanvienData($post_data, true);
        if (!empty($errors)) {
            return [
                'success' => false,
                'message' => 'Vui lòng kiểm tra lại các trường bị lỗi.',
                'errors' => $errors
            ];
        }
        if ($this->nhanvienModel->create($post_data)) {
            return ['success' => true, 'message' => 'Thêm nhân viên mới thành công!'];
        } else {
            return ['success' => false, 'message' => 'Đã xảy ra lỗi trong quá trình thêm vào CSDL.'];
        }
    }
    private function formatNgayVaoLam($date_string)
    {
        if (empty($date_string)) {
            return '';
        }
        if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $date_string, $matches)) {
            return $matches[3] . '-' . $matches[2] . '-' . $matches[1];
        }
        $timestamp = strtotime($date_string);
        return $timestamp ? date('Y-m-d', $timestamp) : '';
    }
    public function edit($id, $post_data)
    {
        if (isset($post_data['ngay_vao_lam'])) {
            $post_data['ngay_vao_lam'] = $this->formatNgayVaoLam($post_data['ngay_vao_lam']);
        }
        $errors = $this->validateNhanvienData($post_data, false);
        if (!empty($errors)) {
            return [
                'success' => false,
                'message' => 'Vui lòng kiểm tra lại các trường bị lỗi.',
                'errors' => $errors
            ];
        }
        if ($this->nhanvienModel->update($id, $post_data)) {
            return ['success' => true, 'message' => 'Cập nhật nhân viên thành công!'];
        } else {
            return ['success' => false, 'message' => 'Đã xảy ra lỗi trong quá trình cập nhật vào CSDL.'];
        }
    }
    public function deleteHandler($id)
    {
        if ((int)$id <= 0) {
            return ['success' => false, 'message' => 'ID nhân viên không hợp lệ.'];
        }
        if ($this->nhanvienModel->delete($id)) {
            return ['success' => true, 'message' => 'Xóa nhân viên thành công!'];
        } else {
            return ['success' => false, 'message' => 'Lỗi: Không thể xóa nhân viên hoặc dữ liệu liên quan.'];
        }
    }
    public function lockHandler($id)
    {
        if ((int)$id <= 0) {
            return ['success' => false, 'message' => 'ID nhân viên không hợp lệ.'];
        }
        if ($this->nhanvienModel->updateStatus($id, 0)) {
            return ['success' => true, 'message' => 'Khóa tài khoản nhân viên thành công!'];
        } else {
            return ['success' => false, 'message' => 'Lỗi: Không thể khóa tài khoản nhân viên.'];
        }
    }
    public function unlockHandler($id)
    {
        if ((int)$id <= 0) {
            return ['success' => false, 'message' => 'ID nhân viên không hợp lệ.'];
        }
        if ($this->nhanvienModel->updateStatus($id, 1)) {
            return ['success' => true, 'message' => 'Mở khóa tài khoản nhân viên thành công!'];
        } else {
            return ['success' => false, 'message' => 'Lỗi: Không thể mở khóa tài khoản nhân viên.'];
        }
    }
}
