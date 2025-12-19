<?php
// Tên file: classes/Khachhang.class.php
class Khachhang
{
    private $db;
    private $table_name = "khachhang";
    private $view_name = "vw_customer_list";
    public $khachhang_id;
    public $ho_ten;
    public $email;
    public $dien_thoai;
    public $dia_chi;
    public $ten_dangnhap;
    public $trang_thai;
    public function __construct()
    {
        $this->db = new DB();
    }
    public function readAll()
    {
        $query = "SELECT * FROM " . $this->view_name;
        $result = $this->db->select($query);
        return $result;
    }
    public function readOne($id)
    {
        $safe_id = (int)$id;
        $query = "SELECT * FROM " . $this->view_name . " WHERE khachhang_id = " . $safe_id . " LIMIT 0,1";
        $result = $this->db->selectOne($query);
        if ($result) {
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
    public static function getStatusClass($status)
    {
        switch ((string)$status) {
            case '1':
            case 'active':
                return 'completed';
            case '0':
                return 'cancelled';
            default:
                return '';
        }
    }
    public function checkUsernameExists($username)
    {
        $safe_username = $this->db->escapeString($username);
        $query = "SELECT taikhoan_id FROM taikhoan WHERE ten_dangnhap = '$safe_username' LIMIT 1";
        $result = $this->db->selectOne($query);
        return (bool)$result;
    }
    public function checkEmailExists($email, $exclude_id = 0)
    {
        $safe_email = $this->db->escapeString($email);
        $safe_exclude_id = (int)$exclude_id;
        $query = "SELECT khachhang_id FROM khachhang WHERE email = '$safe_email'";
        if ($safe_exclude_id > 0) {
            $query .= " AND khachhang_id != $safe_exclude_id";
        }
        $query .= " LIMIT 1";
        $result = $this->db->selectOne($query);
        return (bool)$result;
    }
    public function create($data)
    {
        $ten_dangnhap = $this->db->escapeString($data['ten_dangnhap']);
        $mat_khau_hashed = md5($data['mat_khau']); 
        $vai_tro = 'khachhang';
        $trang_thai = '1'; 
        $this->db->getConnectionHandle()->begin_transaction();
        try {
            $sql_taikhoan = "INSERT INTO taikhoan (ten_dangnhap, mat_khau, vai_tro, trang_thai)
                             VALUES ('$ten_dangnhap', '$mat_khau_hashed', '$vai_tro', '$trang_thai')";
            if (!$this->db->execute($sql_taikhoan)) {
                throw new Exception("Lỗi tạo tài khoản.");
            }
            $taikhoan_id = $this->db->lastInsertId();
            $ho_ten = $this->db->escapeString($data['ho_ten']);
            $email = $this->db->escapeString($data['email']);
            $dien_thoai = $this->db->escapeString($data['dien_thoai']);
            $dia_chi = $this->db->escapeString($data['dia_chi']);
            $sql_khachhang = "INSERT INTO khachhang (taikhoan_id, ho_ten, email, dien_thoai, dia_chi)
                              VALUES ('$taikhoan_id', '$ho_ten', '$email', '$dien_thoai', '$dia_chi')";
            if (!$this->db->execute($sql_khachhang)) {
                throw new Exception("Lỗi tạo khách hàng.");
            }
            $this->db->getConnectionHandle()->commit();
            return true;
        } catch (Exception $e) {
            $this->db->getConnectionHandle()->rollback();
            return false;
        }
    }
    public function update($id, $data)
    {
        $safe_id = (int)$id;
        $taikhoan_info = $this->db->selectOne("SELECT taikhoan_id FROM khachhang WHERE khachhang_id = $safe_id");
        if (!$taikhoan_info) {
            return false;
        }
        $taikhoan_id = $taikhoan_info->taikhoan_id;
        $this->db->getConnectionHandle()->begin_transaction();
        try {
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
            if (!empty($data['mat_khau'])) {
                $mat_khau_hashed = md5($data['mat_khau']);
                $sql_taikhoan = "UPDATE taikhoan SET mat_khau = '$mat_khau_hashed' WHERE taikhoan_id = $taikhoan_id";
                if (!$this->db->execute($sql_taikhoan)) {
                    throw new Exception("Lỗi cập nhật mật khẩu.");
                }
            }
            $this->db->getConnectionHandle()->commit();
            return true;
        } catch (Exception $e) {
            $this->db->getConnectionHandle()->rollback();
            return false;
        }
    }
    public function delete($id)
    {
        $safe_id = (int)$id;
        $db_handle = $this->db->getConnectionHandle();
        $taikhoan_info = $this->db->selectOne("SELECT taikhoan_id FROM khachhang WHERE khachhang_id = $safe_id");
        if (!$taikhoan_info) {
            return false; 
        }
        $taikhoan_id = $taikhoan_info->taikhoan_id;
        $db_handle->begin_transaction();
        try {
            $sql_chitiet = "DELETE FROM chitiet_donhang WHERE donhang_id IN (SELECT donhang_id FROM donhang WHERE khachhang_id = $safe_id)";
            $this->db->execute($sql_chitiet);
            $sql_donhang = "DELETE FROM donhang WHERE khachhang_id = $safe_id";
            $this->db->execute($sql_donhang);
            $sql_danhgia = "DELETE FROM danhgia WHERE khachhang_id = $safe_id";
            $this->db->execute($sql_danhgia);
            $sql_khachhang = "DELETE FROM khachhang WHERE khachhang_id = $safe_id";
            if (!$this->db->execute($sql_khachhang)) {
                throw new Exception("Lỗi xóa khách hàng.");
            }
            $sql_taikhoan = "DELETE FROM taikhoan WHERE taikhoan_id = $taikhoan_id";
            if (!$this->db->execute($sql_taikhoan)) {
                throw new Exception("Lỗi xóa tài khoản.");
            }
            $db_handle->commit();
            return true;
        } catch (Exception $e) {
            $db_handle->rollback();
            return false;
        }
    }
    public function updateStatus($id, $status)
    {
        $safe_id = (int)$id;
        $safe_status = (int)$status;
        $taikhoan_info = $this->db->selectOne("SELECT taikhoan_id FROM khachhang WHERE khachhang_id = $safe_id");
        if (!$taikhoan_info) {
            return false;
        }
        $taikhoan_id = $taikhoan_info->taikhoan_id;
        $sql = "UPDATE taikhoan SET trang_thai = $safe_status WHERE taikhoan_id = $taikhoan_id";
        return $this->db->execute($sql);
    }
}
