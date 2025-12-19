<?php
// classes/Loaisp.class.php
class Loaisp
{
    private $db;
    private $table_name = 'loaisanpham';
    public $loai_id;
    public $ten_loai;
    public $mo_ta;
    public function __construct()
    {
        $this->db = new DB();
    }
    public function readAll()
    {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY ten_loai ASC";
        return $this->db->select($query);
    }
    public function readOne($id)
    {
        $safe_id = (int)$id;
        $query = "SELECT * FROM " . $this->table_name . " WHERE loai_id = $safe_id LIMIT 0,1";
        $row = $this->db->selectOne($query);
        if ($row) {
            $this->loai_id = $row->loai_id ?? null;
            $this->ten_loai = $row->ten_loai ?? null;
            $this->mo_ta = $row->mo_ta ?? null;
            return true;
        }
        return false;
    }
    public function create($data)
    {
        $ten_loai = $this->db->escapeString($data['ten_loai']);
        $mo_ta = $this->db->escapeString($data['mo_ta'] ?? '');
        $query = "INSERT INTO " . $this->table_name . " (ten_loai, mo_ta) 
                  VALUES ('$ten_loai', '$mo_ta')";
        return $this->db->execute($query);
    }
    public function update($id, $data)
    {
        $safe_id = (int)$id;
        $ten_loai = $this->db->escapeString($data['ten_loai']);
        $mo_ta = $this->db->escapeString($data['mo_ta'] ?? '');
        $query = "UPDATE " . $this->table_name . " SET 
                  ten_loai = '$ten_loai', 
                  mo_ta = '$mo_ta' 
                  WHERE loai_id = $safe_id";
        return $this->db->execute($query);
    }
    public function delete($id)
    {
        $safe_id = (int)$id;
        $query = "DELETE FROM " . $this->table_name . " WHERE loai_id = $safe_id";
        return $this->db->execute($query);
    }
    public function checkTenLoaiExists($ten_loai, $exclude_id = 0)
    {
        $safe_ten_loai = $this->db->escapeString($ten_loai);
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " 
                  WHERE ten_loai = '$safe_ten_loai'";
        if ($exclude_id > 0) {
            $query .= " AND loai_id != " . (int)$exclude_id;
        }
        $result = $this->db->selectOne($query);
        return $result && $result->count > 0;
    }
}
