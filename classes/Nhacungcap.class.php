<?php
// classes/Nhacungcap.class.php
class Nhacungcap
{
    private $db;
    private $table_name = 'nhacungcap';
    public $ncc_id;
    public $ten_ncc;
    public $dien_thoai;
    public $dia_chi;
    public function __construct()
    {
        $this->db = new DB();
    }
    public function readAll()
    {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY ten_ncc ASC";
        return $this->db->select($query);
    }
    public function readOne($id)
    {
        $safe_id = (int)$id;
        $query = "SELECT * FROM " . $this->table_name . " WHERE ncc_id = $safe_id LIMIT 0,1";
        $row = $this->db->selectOne($query);
        if ($row) {
            $this->ncc_id = $row->ncc_id ?? null;
            $this->ten_ncc = $row->ten_ncc ?? null;
            $this->dien_thoai = $row->dien_thoai ?? null;
            $this->dia_chi = $row->dia_chi ?? null;
            return true;
        }
        return false;
    }
    public function create($data)
    {
        $ten_ncc = $this->db->escapeString($data['ten_ncc']);
        $dien_thoai = $this->db->escapeString($data['dien_thoai']);
        $dia_chi = $this->db->escapeString($data['dia_chi'] ?? '');
        $query = "INSERT INTO " . $this->table_name . " 
                  (ten_ncc, dien_thoai, dia_chi) 
                  VALUES ('$ten_ncc', '$dien_thoai', '$dia_chi')";
        return $this->db->execute($query);
    }
    public function update($id, $data)
    {
        $safe_id = (int)$id;
        $ten_ncc = $this->db->escapeString($data['ten_ncc']);
        $dien_thoai = $this->db->escapeString($data['dien_thoai']);
        $dia_chi = $this->db->escapeString($data['dia_chi'] ?? '');
        $query = "UPDATE " . $this->table_name . " SET 
                  ten_ncc = '$ten_ncc', 
                  dien_thoai = '$dien_thoai', 
                  dia_chi = '$dia_chi' 
                  WHERE ncc_id = $safe_id";
        return $this->db->execute($query);
    }
    public function delete($id)
    {
        $safe_id = (int)$id;
        $query = "DELETE FROM " . $this->table_name . " WHERE ncc_id = $safe_id";
        return $this->db->execute($query);
    }
    public function checkTenNhaCungCapExists($ten_ncc, $exclude_id = 0)
    {
        $safe_ten = $this->db->escapeString($ten_ncc);
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " 
                  WHERE ten_ncc = '$safe_ten'";
        if ($exclude_id > 0) {
            $query .= " AND ncc_id != " . (int)$exclude_id;
        }
        $result = $this->db->selectOne($query);
        return $result && $result->count > 0;
    }
}
