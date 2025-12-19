<?php
// classes/Nhanvien.class.php
class Nhanvien
{
    private $db;
    private $table_nhanvien = 'nhanvien';
    private $table_taikhoan = 'taikhoan';
    public $nhanvien_id;
    public $taikhoan_id;
    public $ho_ten;
    public $email;
    public $sdt;
    public $chuc_vu;
    public $ngay_vao_lam;
    public $ten_dangnhap;
    public $trang_thai;
    public function __construct()
    {
        $this->db = new DB();
    }
    public function readAll()
    {
        $query = "
            SELECT 
                nv.*, tk.ten_dangnhap, tk.trang_thai
            FROM 
                " . $this->table_nhanvien . " nv
            JOIN 
                " . $this->table_taikhoan . " tk ON nv.taikhoan_id = tk.taikhoan_id";
        return $this->db->select($query);
    }
    public function readOne($id)
    {
        $safe_id = (int)$id;
        $query = "
            SELECT 
                nv.*, tk.ten_dangnhap, tk.trang_thai
            FROM 
                " . $this->table_nhanvien . " nv
            JOIN 
                " . $this->table_taikhoan . " tk ON nv.taikhoan_id = tk.taikhoan_id
            WHERE 
                nv.nhanvien_id = $safe_id 
            LIMIT 0,1";
        $row = $this->db->selectOne($query);
        if ($row) {
            $this->nhanvien_id = $row->nhanvien_id ?? null;
            $this->taikhoan_id = $row->taikhoan_id ?? null;
            $this->ho_ten = $row->ho_ten ?? null;
            $this->email = $row->email ?? null;
            $this->sdt = $row->sdt ?? null;
            $this->chuc_vu = $row->chuc_vu ?? null;
            $this->ngay_vao_lam = $row->ngay_vao_lam ?? null;
            $this->ten_dangnhap = $row->ten_dangnhap ?? null;
            $this->trang_thai = $row->trang_thai ?? null;
            return true;
        }
        return false;
    }
    public function create($data)
    {
        $db_handle = $this->db->getConnectionHandle();
        $db_handle->begin_transaction();
        try {
            $taikhoan_id = $this->db->lastInsertId();
            $ho_ten = $this->db->escapeString($data['ho_ten']);
            $email = $this->db->escapeString($data['email']);
            $sdt = $this->db->escapeString($data['sdt']);
            $chuc_vu = $this->db->escapeString($data['chuc_vu']);
            $ngay_vao_lam = $this->db->escapeString($data['ngay_vao_lam']);
            $query_nv = "INSERT INTO " . $this->table_nhanvien . " 
                         (taikhoan_id, ho_ten, email, sdt, chuc_vu, ngay_vao_lam) 
                         VALUES ($taikhoan_id, '$ho_ten', '$email', '$sdt', '$chuc_vu', '$ngay_vao_lam')";
            if (!$this->db->execute($query_nv)) {
                $db_handle->rollback();
                return false;
            }
            $db_handle->commit();
            return true;
        } catch (Exception $e) {
            $db_handle->rollback();
            return false;
        }
    }
    public function update($id, $data)
    {
        $db_handle = $this->db->getConnectionHandle();
        $db_handle->begin_transaction();
        $safe_id = (int)$id;
        try {
            $taikhoan_info = $this->db->selectOne("SELECT taikhoan_id FROM " . $this->table_nhanvien . " WHERE nhanvien_id = $safe_id");
            if (!$taikhoan_info) {
                $db_handle->rollback();
                return false;
            }
            $taikhoan_id = $taikhoan_info->taikhoan_id;
            $ho_ten = $this->db->escapeString($data['ho_ten']);
            $email = $this->db->escapeString($data['email']);
            $sdt = $this->db->escapeString($data['sdt']);
            $chuc_vu = $this->db->escapeString($data['chuc_vu']);
            $ngay_vao_lam = $this->db->escapeString($data['ngay_vao_lam']);
            $query_nv = "UPDATE " . $this->table_nhanvien . " SET 
                         ho_ten = '$ho_ten', 
                         email = '$email', 
                         sdt = '$sdt', 
                         chuc_vu = '$chuc_vu',
                         ngay_vao_lam = '$ngay_vao_lam'
                         WHERE nhanvien_id = $safe_id";
            if (!$this->db->execute($query_nv)) {
                $db_handle->rollback();
                return false;
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
        $taikhoan_info = $this->db->selectOne("SELECT taikhoan_id FROM " . $this->table_nhanvien . " WHERE nhanvien_id = $safe_id");
        if (!$taikhoan_info) {
            return false;
        }
        $taikhoan_id = $taikhoan_info->taikhoan_id;
        $sql = "UPDATE " . $this->table_taikhoan . " SET trang_thai = $safe_status WHERE taikhoan_id = $taikhoan_id";
        return $this->db->execute($sql); 
    }
    public function delete($id)
    {
        $db_handle = $this->db->getConnectionHandle();
        $db_handle->begin_transaction();
        $safe_id = (int)$id;
        try {
            $taikhoan_info = $this->db->selectOne("SELECT taikhoan_id FROM " . $this->table_nhanvien . " WHERE nhanvien_id = $safe_id");
            if (!$taikhoan_info) {
                $db_handle->rollback();
                return false;
            }
            $taikhoan_id = $taikhoan_info->taikhoan_id;
            $query_nv = "DELETE FROM " . $this->table_nhanvien . " WHERE nhanvien_id = $safe_id";
            if (!$this->db->execute($query_nv)) {
                $db_handle->rollback();
                return false;
            }
            $query_tk = "DELETE FROM " . $this->table_taikhoan . " WHERE taikhoan_id = $taikhoan_id";
            if (!$this->db->execute($query_tk)) {
                $db_handle->rollback();
                return false;
            }
            $db_handle->commit();
            return true;
        } catch (Exception $e) {
            $db_handle->rollback();
            return false;
        }
    }
    public function checkUsernameExists($username)
    {
        $safe_username = $this->db->escapeString($username);
        $query = "SELECT taikhoan_id FROM " . $this->table_taikhoan . " WHERE ten_dangnhap = '$safe_username' LIMIT 1";
        $result = $this->db->selectOne($query);
        return (bool)$result;
    }
    public function checkEmailExists($email, $exclude_id = 0)
    {
        $safe_email = $this->db->escapeString($email);
        $safe_exclude_id = (int)$exclude_id;
        $query = "SELECT nhanvien_id FROM " . $this->table_nhanvien . " WHERE email = '$safe_email'";
        if ($safe_exclude_id > 0) {
            $query .= " AND nhanvien_id != $safe_exclude_id";
        }
        $query .= " LIMIT 1";
        $result = $this->db->selectOne($query);
        return (bool)$result;
    }
    public static function getStatusText($status_code)
    {
        switch ((string)$status_code) {
            case '1':
            case 'active':
                return 'Đang hoạt động';
            case '0':
                return 'Đã khóa';
            default:
                return 'Không xác định';
        };
    }
    public static function getStatusClass($status_code)
    {
        switch ((string)$status_code) {
            case '1':
            case 'active':
                return 'completed'; 
            case '0':
                return 'cancelled';
            default:
                return '';
        }
    }
}
