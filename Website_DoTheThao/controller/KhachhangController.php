<?php
// controller/KhachhangController.php

// Đường dẫn gốc của ứng dụng (lên 1 cấp từ thư mục 'controller')
$root_path = dirname(__DIR__);

// 1. Nhúng các file cần thiết
// Đảm bảo BASE_URL, class Database có trong connect.php
// Đảm bảo class DB và Khachhang có sẵn để sử dụng
require_once $root_path . '/config/connect.php';
require_once $root_path . '/classes/DB.class.php';
require_once $root_path . '/classes/Khachhang.class.php';

class KhachhangController
{
    private $khachhangModel;

    public function __construct()
    {
        // Khởi tạo model Khachhang
        $this->khachhangModel = new Khachhang();
    }

    /**
     * Phương thức chính để hiển thị danh sách khách hàng.
     * @return array Danh sách khách hàng và dữ liệu liên quan để hiển thị.
     */
    public function index()
    {
        // Gọi phương thức readAll từ model để lấy dữ liệu
        $khachhangs = $this->khachhangModel->readAll();

        // Chuẩn bị dữ liệu để trả về cho View
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

        // Cần thêm kiểm tra tên đăng nhập/email đã tồn tại trước khi tạo

        if ($this->khachhangModel->create($post_data)) {
            return ['success' => true, 'message' => 'Thêm khách hàng mới thành công!'];
        } else {
            return ['success' => false, 'message' => 'Đã xảy ra lỗi trong quá trình thêm vào CSDL.'];
        }
    }
    /**
     * Xử lý cập nhật khách hàng.
     * @param int $id khachhang_id.
     * @param array $post_data Dữ liệu từ form POST.
     * @return array Kết quả và thông báo.
     */
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

    /**
     * Hàm validate dữ liệu (Dùng chung cho cả add và edit).
     */
    private function validateKhachhangData($data, $is_new = true)
    {
        $errors = [];

        if (empty($data['ho_ten'])) $errors['ho_ten'] = 'Họ tên không được để trống.';
        if (empty($data['email'])) $errors['email'] = 'Email không được để trống.';
        if (empty($data['dien_thoai'])) $errors['dien_thoai'] = 'Điện thoại không được để trống.';
        if (empty($data['dia_chi'])) $errors['dia_chi'] = 'Địa chỉ không được để trống.';
        if (empty($data['ten_dangnhap'])) $errors['ten_dangnhap'] = 'Tên đăng nhập không được để trống.';

        // Lấy ID khách hàng nếu đang edit
        $current_id = $data['khachhang_id'] ?? 0; // Chúng ta cần truyền khachhang_id vào $data khi edit

        // === KIỂM TRA TÍNH DUY NHẤT (UNIQUE) ===

        // 1. Kiểm tra Tên đăng nhập (chỉ khi thêm mới)
        if ($is_new && $this->khachhangModel->checkUsernameExists($data['ten_dangnhap'])) {
            $errors['ten_dangnhap'] = 'Tên đăng nhập đã tồn tại trong hệ thống.';
        }

        // 2. Kiểm tra Email (cho cả thêm mới và chỉnh sửa)
        if ($this->khachhangModel->checkEmailExists($data['email'], $current_id)) {
            $errors['email'] = 'Email đã được sử dụng cho một khách hàng khác.';
        }

        // Yêu cầu mật khẩu khi thêm mới, hoặc khi người dùng điền vào trường mật khẩu khi chỉnh sửa
        if ($is_new || !empty($data['mat_khau'])) {
            if (empty($data['mat_khau'])) $errors['mat_khau'] = 'Mật khẩu không được để trống.';
            else if (strlen($data['mat_khau']) < 6) $errors['mat_khau'] = 'Mật khẩu phải từ 6 ký tự trở lên.';
        }

        return $errors;
    }
    /**
     * Phương thức hiển thị chi tiết khách hàng (chưa triển khai).
     */
    public function detail($id)
    {
        $khachhang = null;
        $message = null;

        if ($this->khachhangModel->readOne($id)) {
            // Nếu tìm thấy, gán các thuộc tính của model vào biến $khachhang
            // (Vì readOne gán dữ liệu vào thuộc tính, ta copy chúng để dùng trong view)
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


    /**
     * Phương thức xử lý xóa khách hàng (chưa triển khai).
     */
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

        // Trạng thái khóa là 0
        if ($this->khachhangModel->updateStatus($id, 0)) {
            return ['success' => true, 'message' => 'Khóa tài khoản thành công!'];
        } else {
            return ['success' => false, 'message' => 'Lỗi: Không thể khóa tài khoản.'];
        }
    }

    /**
     * Xử lý mở khóa tài khoản (chuyển trang_thai = 1).
     * (Có thể cần một file unlock.php riêng nếu bạn triển khai nút mở khóa)
     * @param int $id khachhang_id.
     * @return array Kết quả và thông báo.
     */
    public function unlockHandler($id)
    {
        if ((int)$id <= 0) {
            return ['success' => false, 'message' => 'ID khách hàng không hợp lệ.'];
        }

        // Trạng thái hoạt động là 1
        if ($this->khachhangModel->updateStatus($id, 1)) {
            return ['success' => true, 'message' => 'Mở khóa tài khoản thành công!'];
        } else {
            return ['success' => false, 'message' => 'Lỗi: Không thể mở khóa tài khoản.'];
        }
    }
}
