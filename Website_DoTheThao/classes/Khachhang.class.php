<?php
// Tên file: classes/Khachhang.class.php

// Yêu cầu class DB để thực hiện truy vấn CSDL
// Giả định file DB.class.php nằm trong cùng thư mục 'classes' hoặc đã được nhúng ở nơi khác
// Tuy nhiên, để đảm bảo tính độc lập, tôi sẽ nhúng lại DB.class.php
// *LƯU Ý:* Bạn cần điều chỉnh đường dẫn nhúng cho phù hợp với cấu trúc thư mục thực tế của bạn
// Ví dụ: require_once 'DB.class.php';

class Khachhang
{
    private $db;
    private $table_name = "khachhang";
    private $view_name = "vw_customer_list"; // Dùng View có sẵn trong DB để lấy thông tin đầy đủ

    // Thuộc tính để lưu trữ thông tin của một khách hàng cụ thể
    public $khachhang_id;
    public $ho_ten;
    public $email;
    public $dien_thoai;
    public $dia_chi;
    public $ten_dangnhap;
    public $trang_thai;


    /**
     * Khởi tạo đối tượng Khachhang và thiết lập kết nối CSDL.
     */
    public function __construct()
    {
        // Khởi tạo đối tượng DB để có thể truy vấn CSDL
        // Giả định class DB đã được định nghĩa và có thể sử dụng
        if (!class_exists('DB')) {
            // Thường thì bạn sẽ nhúng DB.class.php ở đây nếu nó chưa được nhúng
            // Ví dụ: require_once 'DB.class.php';
        }
        $this->db = new DB();
    }

    /**
     * Lấy tất cả khách hàng từ View vw_customer_list.
     * @return array Mảng các đối tượng Khachhang (từ View).
     */
    public function readAll()
    {
        // Sử dụng view vw_customer_list để lấy thông tin khách hàng và tài khoản liên quan
        $query = "SELECT * FROM " . $this->view_name;

        $result = $this->db->select($query);

        return $result;
    }

    /**
     * Lấy thông tin chi tiết của một khách hàng dựa trên ID.
     * @param int $id khachhang_id
     * @return bool True nếu tìm thấy và gán thuộc tính, False nếu không tìm thấy.
     */
    public function readOne($id)
    {
        // Chắc chắn ID là số nguyên và lọc
        $safe_id = (int)$id;

        // Truy vấn lấy dữ liệu từ View
        $query = "SELECT * FROM " . $this->view_name . " WHERE khachhang_id = " . $safe_id . " LIMIT 0,1";

        $result = $this->db->selectOne($query);

        if ($result) {
            // Gán các thuộc tính của đối tượng Khachhang
            $this->khachhang_id = $result->khachhang_id;
            $this->ho_ten = $result->ho_ten;
            $this->email = $result->email;
            $this->dien_thoai = $result->dien_thoai;
            $this->dia_chi = $result->dia_chi;
            $this->ten_dangnhap = $result->ten_dangnhap;
            $this->trang_thai = $result->trang_thai;

            return true;
        }

        return false;
    }
    
    // --- Các phương thức helper liên quan đến nghiệp vụ ---

    /**
     * Chuyển đổi mã trạng thái tài khoản (0, 1, active) thành chuỗi trạng thái thân thiện.
     * Dựa trên cột trang_thai trong bảng taikhoan: '1' (active), '0' (inactive/khoá), 'active'.
     * Trong DB, vai trò khách hàng: '1' hoặc 'active'. '0' là đã khóa.
     * @param string|int $status Mã trạng thái từ CSDL.
     * @return string Tên trạng thái.
     */
    public static function getStatusText($status)
    {
        switch ((string)$status) {
            case '1':
            case 'active':
                return 'Đang hoạt động';
            case '0':
                return 'Đã khóa';
            default:
                return 'Không xác định';
        }
    }

    /**
     * Trả về class CSS cho huy hiệu trạng thái tài khoản.
     * @param string|int $status Mã trạng thái từ CSDL.
     * @return string Class CSS.
     */
    public static function getStatusClass($status)
    {
        switch ((string)$status) {
            case '1':
            case 'active':
                return 'completed'; // Dùng lại màu xanh lá từ admin_style.css
            case '0':
                return 'cancelled'; // Dùng lại màu đỏ từ admin_style.css
            default:
                return '';
        }
    }

    public function checkUsernameExists($username)
    {
        $safe_username = $this->db->escapeString($username);
        // Kiểm tra trong bảng taikhoan
        $query = "SELECT taikhoan_id FROM taikhoan WHERE ten_dangnhap = '$safe_username' LIMIT 1";
        $result = $this->db->selectOne($query);
        return (bool)$result;
    }

    /**
     * Kiểm tra xem email đã tồn tại chưa.
     * @param string $email Email cần kiểm tra.
     * @param int $exclude_id Khachhang ID để loại trừ (dùng khi chỉnh sửa).
     * @return bool True nếu tồn tại, False nếu chưa.
     */
    public function checkEmailExists($email, $exclude_id = 0)
    {
        $safe_email = $this->db->escapeString($email);
        $safe_exclude_id = (int)$exclude_id;

        $query = "SELECT khachhang_id FROM khachhang WHERE email = '$safe_email'";

        if ($safe_exclude_id > 0) {
            // Thêm điều kiện loại trừ chính bản thân khách hàng đang chỉnh sửa
            $query .= " AND khachhang_id != $safe_exclude_id";
        }

        $query .= " LIMIT 1";

        $result = $this->db->selectOne($query);
        return (bool)$result;
    }
    /**
     * Thêm khách hàng mới và tài khoản liên quan.
     * Sử dụng transaction để đảm bảo cả hai lệnh INSERT đều thành công.
     * @param array $data Dữ liệu khách hàng và đăng nhập.
     * @return bool True nếu thành công, False nếu thất bại.
     */
    public function create($data)
    {
        // 1. Dữ liệu Tài khoản (Login Account)
        $ten_dangnhap = $this->db->escapeString($data['ten_dangnhap']);
        $mat_khau_hashed = md5($data['mat_khau']); // Mã hóa MD5 cho tương thích với hệ thống hiện tại
        $vai_tro = 'khachhang';
        $trang_thai = '1'; // Mặc định là hoạt động

        // Bắt đầu transaction
        $this->db->getConnectionHandle()->begin_transaction();

        try {
            // INSERT vào bảng taikhoan
            $sql_taikhoan = "INSERT INTO taikhoan (ten_dangnhap, mat_khau, vai_tro, trang_thai)
                             VALUES ('$ten_dangnhap', '$mat_khau_hashed', '$vai_tro', '$trang_thai')";

            if (!$this->db->execute($sql_taikhoan)) {
                throw new Exception("Lỗi tạo tài khoản.");
            }

            $taikhoan_id = $this->db->lastInsertId();

            // 2. Dữ liệu Khách hàng
            $ho_ten = $this->db->escapeString($data['ho_ten']);
            $email = $this->db->escapeString($data['email']);
            $dien_thoai = $this->db->escapeString($data['dien_thoai']);
            $dia_chi = $this->db->escapeString($data['dia_chi']);

            // INSERT vào bảng khachhang
            $sql_khachhang = "INSERT INTO khachhang (taikhoan_id, ho_ten, email, dien_thoai, dia_chi)
                              VALUES ('$taikhoan_id', '$ho_ten', '$email', '$dien_thoai', '$dia_chi')";

            if (!$this->db->execute($sql_khachhang)) {
                throw new Exception("Lỗi tạo khách hàng.");
            }

            // Commit transaction nếu cả hai đều thành công
            $this->db->getConnectionHandle()->commit();
            return true;
        } catch (Exception $e) {
            // Rollback nếu có lỗi
            $this->db->getConnectionHandle()->rollback();
            return false;
        }
    }

    /**
     * Cập nhật thông tin khách hàng và mật khẩu (nếu có).
     * @param int $id khachhang_id.
     * @param array $data Dữ liệu cập nhật.
     * @return bool True nếu thành công, False nếu thất bại.
     */
    public function update($id, $data)
    {
        $safe_id = (int)$id;

        // 1. Lấy taikhoan_id từ khachhang_id
        $taikhoan_info = $this->db->selectOne("SELECT taikhoan_id FROM khachhang WHERE khachhang_id = $safe_id");

        if (!$taikhoan_info) {
            return false;
        }

        $taikhoan_id = $taikhoan_info->taikhoan_id;

        // Bắt đầu transaction
        $this->db->getConnectionHandle()->begin_transaction();

        try {
            // 2. Cập nhật bảng khachhang
            $ho_ten = $this->db->escapeString($data['ho_ten']);
            $email = $this->db->escapeString($data['email']);
            $dien_thoai = $this->db->escapeString($data['dien_thoai']);
            $dia_chi = $this->db->escapeString($data['dia_chi']);

            $sql_khachhang = "UPDATE khachhang SET
                              ho_ten = '$ho_ten',
                              email = '$email',
                              dien_thoai = '$dien_thoai',
                              dia_chi = '$dia_chi'
                              WHERE khachhang_id = $safe_id";

            if (!$this->db->execute($sql_khachhang)) {
                throw new Exception("Lỗi cập nhật khách hàng.");
            }

            // 3. Cập nhật mật khẩu nếu có (bảng taikhoan)
            if (!empty($data['mat_khau'])) {
                $mat_khau_hashed = md5($data['mat_khau']);
                $sql_taikhoan = "UPDATE taikhoan SET mat_khau = '$mat_khau_hashed' WHERE taikhoan_id = $taikhoan_id";

                if (!$this->db->execute($sql_taikhoan)) {
                    throw new Exception("Lỗi cập nhật mật khẩu.");
                }
            }

            // Commit transaction
            $this->db->getConnectionHandle()->commit();
            return true;
        } catch (Exception $e) {
            // Rollback
            $this->db->getConnectionHandle()->rollback();
            return false;
        }
    }
    public function delete($id)
    {
        $safe_id = (int)$id;
        $db_handle = $this->db->getConnectionHandle();

        // 1. Lấy thông tin cần thiết
        $taikhoan_info = $this->db->selectOne("SELECT taikhoan_id FROM khachhang WHERE khachhang_id = $safe_id");
        if (!$taikhoan_info) {
            return false; // Không tìm thấy khách hàng
        }
        $taikhoan_id = $taikhoan_info->taikhoan_id;

        $db_handle->begin_transaction();

        try {
            // Do donhang và danhgia có liên kết tới khachhang, 
            // và chitiet_donhang liên kết tới donhang.
            // Nếu không có ON DELETE CASCADE, ta phải xóa theo thứ tự:

            // 1. Xóa chi tiết đơn hàng (con của donhang)
            $sql_chitiet = "DELETE FROM chitiet_donhang WHERE donhang_id IN (SELECT donhang_id FROM donhang WHERE khachhang_id = $safe_id)";
            $this->db->execute($sql_chitiet);

            // 2. Xóa đơn hàng (con của khachhang)
            $sql_donhang = "DELETE FROM donhang WHERE khachhang_id = $safe_id";
            $this->db->execute($sql_donhang);

            // 3. Xóa đánh giá (con của khachhang)
            $sql_danhgia = "DELETE FROM danhgia WHERE khachhang_id = $safe_id";
            $this->db->execute($sql_danhgia);

            // 4. Xóa khách hàng (cha)
            $sql_khachhang = "DELETE FROM khachhang WHERE khachhang_id = $safe_id";
            if (!$this->db->execute($sql_khachhang)) {
                throw new Exception("Lỗi xóa khách hàng.");
            }

            // 5. Xóa tài khoản (cha)
            $sql_taikhoan = "DELETE FROM taikhoan WHERE taikhoan_id = $taikhoan_id";
            if (!$this->db->execute($sql_taikhoan)) {
                throw new Exception("Lỗi xóa tài khoản.");
            }

            $db_handle->commit();
            return true;
        } catch (Exception $e) {
            $db_handle->rollback();
            // Tùy chọn: ghi log $e->getMessage()
            return false;
        }
    }
    public function updateStatus($id, $status)
    {
        $safe_id = (int)$id;
        $safe_status = (int)$status;

        // 1. Lấy taikhoan_id từ khachhang_id
        $taikhoan_info = $this->db->selectOne("SELECT taikhoan_id FROM khachhang WHERE khachhang_id = $safe_id");

        if (!$taikhoan_info) {
            return false;
        }

        $taikhoan_id = $taikhoan_info->taikhoan_id;

        // 2. Cập nhật trạng thái trong bảng taikhoan
        $sql = "UPDATE taikhoan SET trang_thai = $safe_status WHERE taikhoan_id = $taikhoan_id";

        return $this->db->execute($sql);
    }
}
