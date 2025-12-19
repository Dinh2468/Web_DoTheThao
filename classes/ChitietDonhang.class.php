<?php
// classes/ChitietDonhang.class.php
class ChitietDonhang
{
    private $db;
    private $table_chitiet = 'chitiet_donhang';
    private $table_sanpham = 'sanpham';
    public $donhang_id;
    public $sanpham_id;
    public $so_luong;
    public $gia_ban;
    public $thanh_tien;
    public $ten_sanpham;
    public function __construct()
    {
        $this->db = new DB();
    }
    public function readByDonhangId($donhang_id)
    {
        $safe_id = (int)$donhang_id;
        $query = "
            SELECT 
                ct.*, sp.ten_sanpham
            FROM 
                " . $this->table_chitiet . " ct
            JOIN
                " . $this->table_sanpham . " sp ON ct.sanpham_id = sp.sanpham_id
            WHERE 
                ct.donhang_id = $safe_id";
        return $this->db->select($query);
    }
    public function readOne($donhang_id, $sanpham_id)
    {
        $safe_dh_id = (int)$donhang_id;
        $safe_sp_id = (int)$sanpham_id;
        $query = "
            SELECT 
                ct.*, sp.ten_sanpham
            FROM 
                " . $this->table_chitiet . " ct
            JOIN
                " . $this->table_sanpham . " sp ON ct.sanpham_id = sp.sanpham_id
            WHERE 
                ct.donhang_id = $safe_dh_id AND ct.sanpham_id = $safe_sp_id
            LIMIT 0,1";
        $row = $this->db->selectOne($query);
        if ($row) {
            $this->donhang_id = $row->donhang_id ?? null;
            $this->sanpham_id = $row->sanpham_id ?? null;
            $this->so_luong = $row->so_luong ?? 0;
            $this->gia_ban = $row->gia_ban ?? 0;
            $this->thanh_tien = $row->thanh_tien ?? 0;
            $this->ten_sanpham = $row->ten_sanpham ?? null;
            return true;
        }
        return false;
    }
    public function create($data)
    {
        $donhang_id = (int)$data['donhang_id'];
        $sanpham_id = (int)$data['sanpham_id'];
        $so_luong = (int)$data['so_luong'];
        $gia_ban = (float)$data['gia_ban'];
        $thanh_tien = (float)$data['thanh_tien'];
        $query = "INSERT INTO " . $this->table_chitiet . " 
                  (donhang_id, sanpham_id, so_luong, gia_ban, thanh_tien) 
                  VALUES ($donhang_id, $sanpham_id, $so_luong, $gia_ban, $thanh_tien)";
        return $this->db->execute($query);
    }
    public function deleteByDonhangId($donhang_id)
    {
        $safe_id = (int)$donhang_id;
        $query = "DELETE FROM " . $this->table_chitiet . " WHERE donhang_id = $safe_id";
        return $this->db->execute($query);
    }
}
