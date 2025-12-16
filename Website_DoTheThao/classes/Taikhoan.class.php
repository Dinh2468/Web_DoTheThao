<?php
// classes/Taikhoan.php

class Taikhoan
{
    private $conn;
    private $table_name = "Taikhoan";

    public $taikhoan_id;
    public $ten_dangnhap;
    public $mat_khau;
    public $vai_tro = 'khachhang';
    public $trang_thai = 1;

    public function __construct($db)
    {
        $this->conn = $db;
    }


    // TẠO BẢN GHI MỚI (CREATE) - Đã có trước
    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . " 
                  (ten_dangnhap, mat_khau, vai_tro, trang_thai) 
                  VALUES (?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);

        $hashed_password = md5($this->mat_khau);

        $this->ten_dangnhap = htmlspecialchars(strip_tags($this->ten_dangnhap));

        $stmt->bind_param(
            "sssi",
            $this->ten_dangnhap,
            $hashed_password,
            $this->vai_tro,
            $this->trang_thai
        );

        if ($stmt->execute()) {
            $this->taikhoan_id = $this->conn->insert_id;
            return true;
        }
        return false;
    }

    // ĐỌC MỘT BẢN GHI (Read One)
    public function readOne()
    {
        $query = "SELECT taikhoan_id, ten_dangnhap, mat_khau, vai_tro, trang_thai 
                  FROM " . $this->table_name . " 
                  WHERE taikhoan_id = ? 
                  LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->taikhoan_id);
        $stmt->execute();

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row) {
            $this->ten_dangnhap = $row['ten_dangnhap'];
            $this->mat_khau     = $row['mat_khau'];
            $this->vai_tro      = $row['vai_tro'];
            $this->trang_thai   = $row['trang_thai'];
            return true;
        }
        return false;
    }

    // CẬP NHẬT DỮ LIỆU (Update)
    public function update()
    {
        // Khởi tạo các phần cần cập nhật và kiểu dữ liệu
        $set_parts = [];
        $params = [];
        $types = '';

        // 1. Cập nhật Tên Đăng Nhập (luôn cập nhật)
        $set_parts[] = "ten_dangnhap = ?";
        $params[] = $this->ten_dangnhap;
        $types .= 's';

        // 2. Cập nhật Mật Khẩu (chỉ khi có giá trị)
        if (!empty($this->mat_khau)) {
            $set_parts[] = "mat_khau = ?";
            $hashed_password = md5($this->mat_khau);
            $params[] = $hashed_password;
            $types .= 's';
        }

        // 3. Cập nhật Vai Trò
        $set_parts[] = "vai_tro = ?";
        $params[] = $this->vai_tro;
        $types .= 's';

        // 4. Cập nhật Trạng Thái
        $set_parts[] = "trang_thai = ?";
        $params[] = $this->trang_thai;
        $types .= 'i'; // Integer

        // Xây dựng câu lệnh UPDATE
        $query = "UPDATE " . $this->table_name . "
                  SET " . implode(", ", $set_parts) . "
                  WHERE taikhoan_id = ?";

        // Chuẩn bị stmt
        $stmt = $this->conn->prepare($query);

        // 5. Thêm ID vào cuối mảng tham số (cho mệnh đề WHERE)
        $params[] = $this->taikhoan_id;
        $types .= 'i'; // ID là Integer

        // 6. Liên kết tham số
        // Mảng cuối cùng để liên kết: [types_string, param1, param2, ..., taikhoan_id]
        $bind_params = array_merge([$types], $params);

        // Phải dùng refValues để truyền tham chiếu cho bind_param
        call_user_func_array([$stmt, 'bind_param'], $this->refValues($bind_params));

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Hàm hỗ trợ cho bind_param với số lượng tham số động
    private function refValues($arr)
    {
        if (strnatcmp(phpversion(), '5.3') >= 0) {
            $refs = [];
            foreach ($arr as $key => $value)
                $refs[$key] = &$arr[$key];
            return $refs;
        }
        return $arr;
    }

    // XÓA DỮ LIỆU (Delete)
    public function delete()
    {
        // Lưu ý: Phải đảm bảo không có bản ghi nào trong Khachhang/Nhanvien 
        // còn tham chiếu đến taikhoan_id này (ON DELETE CASCADE sẽ tự xử lý)

        $query = "DELETE FROM " . $this->table_name . " WHERE taikhoan_id = ?";
        $stmt = $this->conn->prepare($query);

        $this->taikhoan_id = htmlspecialchars(strip_tags($this->taikhoan_id));
        $stmt->bind_param("i", $this->taikhoan_id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
    public function login()
    {
        // 1. Tìm tài khoản bằng ten_dangnhap (Email)
        $query = "SELECT taikhoan_id, ten_dangnhap, mat_khau, vai_tro, trang_thai 
                  FROM " . $this->table_name . " 
                  WHERE ten_dangnhap = ? 
                  LIMIT 0,1";

        $stmt = $this->conn->prepare($query);

        // Chỉ cần bind tên đăng nhập (email)
        $this->ten_dangnhap = htmlspecialchars(strip_tags($this->ten_dangnhap));
        $stmt->bind_param("s", $this->ten_dangnhap);
        $stmt->execute();

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row) {
            // 2. Nếu tìm thấy, lấy thông tin và so sánh mật khẩu

            // LƯU Ý QUAN TRỌNG: Mật khẩu trong CSDL đang là MD5
            $db_hashed_password = $row['mat_khau'];

            // So sánh mật khẩu người dùng nhập vào (đã được mã hóa MD5)
            // Trong trường hợp này, chúng ta phải mã hóa mật khẩu nhập vào bằng MD5 để so sánh
            if (md5($this->mat_khau) === $db_hashed_password) {



                // Mật khẩu hợp lệ, gán các thuộc tính khác
                $this->taikhoan_id = $row['taikhoan_id'];
                $this->vai_tro     = $row['vai_tro'];
                $this->trang_thai  = $row['trang_thai'];

                return true; // Đăng nhập thành công
            }
        }

        return false; // Sai tên đăng nhập hoặc mật khẩu
    }
}
