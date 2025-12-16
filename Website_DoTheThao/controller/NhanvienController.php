<?php
// controller/NhanvienController.php

// Thiết lập đường dẫn gốc
$root_path = dirname(__DIR__);

// 1. Nhúng các file cần thiết
// Đảm bảo BASE_URL, class Database có trong connect.php
require_once $root_path . '/config/connect.php';
require_once $root_path . '/classes/DB.class.php';
require_once $root_path . '/classes/Nhanvien.class.php'; // Nhúng Nhanvien Model

class NhanvienController
{
    private $nhanvienModel;

    public function __construct()
    {
        // Khởi tạo model Nhanvien
        $this->nhanvienModel = new Nhanvien();
    }

    // ----------------------------------------------------------------------
    // PHƯƠNG THỨC ĐỌC (READ)
    // ----------------------------------------------------------------------

    /**
     * Phương thức chính để hiển thị danh sách nhân viên.
     * @return array Danh sách nhân viên và dữ liệu liên quan để hiển thị.
     */
    public function index()
    {
        $nhanviens = $this->nhanvienModel->readAll();
        return [
            'nhanviens' => $nhanviens
        ];
    }

    /**
     * Lấy chi tiết thông tin một nhân viên.
     */
    public function detail($id)
    {
        $nhanvien = null;
        $message = null;

        if ($this->nhanvienModel->readOne($id)) {
            // Lấy dữ liệu từ thuộc tính của model sau khi readOne
            $nhanvien = (object)[
                'nhanvien_id' => $this->nhanvienModel->nhanvien_id,
                'ho_ten' => $this->nhanvienModel->ho_ten,
                'email' => $this->nhanvienModel->email,

                // === BỔ SUNG CÁC THUỘC TÍNH MỚI/ĐÃ SỬA ===
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

    // ----------------------------------------------------------------------
    // PHƯƠNG THỨC VALIDATE
    // ----------------------------------------------------------------------

    /**
     * Hàm validate dữ liệu (Dùng chung cho cả add và edit).
     */
    private function validateNhanvienData($data, $is_new = true)
    {
        $errors = [];

        // Lấy ID nhân viên nếu đang edit
        $current_id = $data['nhanvien_id'] ?? 0;

        // 1. KIỂM TRA CÁC TRƯỜNG BẮT BUỘC (Required Fields)
        if (empty($data['ho_ten'])) $errors['ho_ten'] = 'Họ tên không được để trống.';
        if (empty($data['email'])) $errors['email'] = 'Email không được để trống.';
        if (empty($data['sdt'])) $errors['sdt'] = 'Số điện thoại không được để trống.';
        if (empty($data['chuc_vu'])) $errors['chuc_vu'] = 'Chức vụ không được để trống.';
        if (empty($data['ngay_vao_lam'])) $errors['ngay_vao_lam'] = 'Ngày vào làm không được để trống.';

        // 2. KIỂM TRA TÊN ĐĂNG NHẬP & MẬT KHẨU
        if (empty($data['ten_dangnhap'])) {
            $errors['ten_dangnhap'] = 'Tên đăng nhập không được để trống.';
        } else {
            // Kiểm tra tính duy nhất của Tên đăng nhập (chỉ khi thêm mới hoặc nếu tên bị thay đổi)
            if ($this->nhanvienModel->checkUsernameExists($data['ten_dangnhap'], $current_id)) {
                $errors['ten_dangnhap'] = 'Tên đăng nhập đã tồn tại trong hệ thống.';
            }
        }

        if ($is_new) {
            // BẮT BUỘC MẬT KHẨU khi Thêm mới
            if (empty($data['mat_khau'])) {
                $errors['mat_khau'] = 'Mật khẩu không được để trống.';
            } elseif (strlen($data['mat_khau']) < 6) {
                $errors['mat_khau'] = 'Mật khẩu phải từ 6 ký tự trở lên.';
            }
        } else {
            // KHÔNG BẮT BUỘC MẬT KHẨU khi Chỉnh sửa (chỉ kiểm tra nếu có nhập)
            if (!empty($data['mat_khau']) && strlen($data['mat_khau']) < 6) {
                $errors['mat_khau'] = 'Mật khẩu mới phải từ 6 ký tự trở lên.';
            }
        }
    }
    // ----------------------------------------------------------------------
    // PHƯƠNG THỨC THAO TÁC (CRUD)
    // ----------------------------------------------------------------------

    /**
     * Phương thức xử lý thêm nhân viên mới.
     */
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
        // Kiểm tra xem chuỗi có ở định dạng DD/MM/YYYY không
        // Nếu có, chuyển đổi sang YYYY-MM-DD
        if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $date_string, $matches)) {
            return $matches[3] . '-' . $matches[2] . '-' . $matches[1];
        }
        // Nếu đã là YYYY-MM-DD hoặc định dạng khác (từ input type="date" thuần), cố gắng xử lý bằng strtotime
        $timestamp = strtotime($date_string);
        return $timestamp ? date('Y-m-d', $timestamp) : '';
    }

    /**
     * Phương thức xử lý cập nhật nhân viên.
     */
    public function edit($id, $post_data)
    {
        // === 1. CHUẨN HÓA DỮ LIỆU NGÀY TỪ POST TRƯỚC KHI VALIDATE ===
        if (isset($post_data['ngay_vao_lam'])) {
            $post_data['ngay_vao_lam'] = $this->formatNgayVaoLam($post_data['ngay_vao_lam']);
        }

        // === 2. GỌI VALIDATE CHỈ MỘT LẦN ===
        $errors = $this->validateNhanvienData($post_data, false);

        if (!empty($errors)) {
            return [
                'success' => false,
                'message' => 'Vui lòng kiểm tra lại các trường bị lỗi.',
                'errors' => $errors
            ];
        }

        // === 3. THỰC HIỆN CẬP NHẬT ===
        if ($this->nhanvienModel->update($id, $post_data)) {
            return ['success' => true, 'message' => 'Cập nhật nhân viên thành công!'];
        } else {
            return ['success' => false, 'message' => 'Đã xảy ra lỗi trong quá trình cập nhật vào CSDL.'];
        }
    }

    /**
     * Xử lý xóa nhân viên.
     */
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

    /**
     * Xử lý khóa tài khoản (chuyển trang_thai = 0).
     */
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

    /**
     * Xử lý mở khóa tài khoản (chuyển trang_thai = 1).
     */
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
