<?php
// classes/Donhang.class.php
class Donhang
{
    private $db;
    private $table_donhang = 'donhang';
    private $table_chitiet = 'chitiet_donhang';
    private $table_khachhang = 'khachhang';
    private $table_nhanvien = 'nhanvien';
    public $donhang_id;
    public $khachhang_id;
    public $nhanvien_id;
    public $ngay_dat;
    public $tong_tien;
    public $trang_thai_don_hang;
    public $phuong_thuc_thanh_toan;
    public $ghi_chu;
    public $ten_khachhang;
    public $ten_nhanvien_xu_ly;
    public function __construct()
    {
        $this->db = new DB();
    }
    public function readAll()
    {
        $query = "
            SELECT 
                dh.*, 
                kh.ho_ten AS ten_khachhang,
                nv.ho_ten AS ten_nhanvien_xu_ly
            FROM 
                " . $this->table_donhang . " dh
            JOIN
                " . $this->table_khachhang . " kh ON dh.khachhang_id = kh.khachhang_id
            LEFT JOIN 
                " . $this->table_nhanvien . " nv ON dh.nhanvien_id = nv.nhanvien_id";
        return $this->db->select($query);
    }
    public function readOne($id)
    {
        $safe_id = (int)$id;
        $query = "
            SELECT 
                dh.*, 
                kh.ho_ten AS ten_khachhang,
                nv.ho_ten AS ten_nhanvien_xu_ly
            FROM 
                " . $this->table_donhang . " dh
            JOIN
                " . $this->table_khachhang . " kh ON dh.khachhang_id = kh.khachhang_id
            LEFT JOIN 
                " . $this->table_nhanvien . " nv ON dh.nhanvien_id = nv.nhanvien_id
            WHERE 
                dh.donhang_id = $safe_id
            LIMIT 0,1";
        $row = $this->db->selectOne($query);
        if ($row) {
            $this->donhang_id = $row->donhang_id ?? null;
            $this->khachhang_id = $row->khachhang_id ?? null;
            $this->nhanvien_id = $row->nhanvien_id ?? null;
            $this->ngay_dat = $row->ngay_dat ?? null;
            $this->tong_tien = $row->tong_tien ?? 0;
            $this->trang_thai_don_hang = $row->trang_thai_don_hang ?? null;
            $this->phuong_thuc_thanh_toan = $row->phuong_thuc_thanh_toan ?? null;
            $this->ghi_chu = $row->ghi_chu ?? null;
            $this->ten_khachhang = $row->ten_khachhang ?? null;
            $this->ten_nhanvien_xu_ly = $row->ten_nhanvien_xu_ly ?? null;
            return true;
        }
        return false;
    }
    public function createDonhang($data, $items)
    {
        $db_handle = $this->db->getConnectionHandle();
        $db_handle->begin_transaction();
        try {
            $khachhang_id = (int)$data['khachhang_id'];
            $nhanvien_id = $data['nhanvien_id'] ? (int)$data['nhanvien_id'] : 'NULL';
            $tong_tien = (float)$data['tong_tien'];
            $ngay_dat = date('Y-m-d H:i:s');
            $trang_thai = $this->db->escapeString($data['trang_thai_don_hang']);
            $pttt = $this->db->escapeString($data['phuong_thuc_thanh_toan']);
            $ghi_chu = $this->db->escapeString($data['ghi_chu'] ?? '');
            $query_dh = "INSERT INTO " . $this->table_donhang . " 
                         (khachhang_id, nhanvien_id, ngay_dat, tong_tien, trang_thai_don_hang, phuong_thuc_thanh_toan, ghi_chu) 
                         VALUES ($khachhang_id, $nhanvien_id, '$ngay_dat', $tong_tien, '$trang_thai', '$pttt', '$ghi_chu')";
            if (!$this->db->execute($query_dh)) {
                throw new Exception("Lỗi khi thêm đơn hàng.");
            }
            $donhang_id = $this->db->lastInsertId();
            foreach ($items as $item) {
                $sanpham_id = (int)$item['sanpham_id'];
                $so_luong = (int)$item['so_luong'];
                $gia = (float)$item['gia_ban'];
                $thanh_tien = $so_luong * $gia;
                $query_ct = "INSERT INTO " . $this->table_chitiet . " 
                             (donhang_id, sanpham_id, so_luong, gia_ban, thanh_tien) 
                             VALUES ($donhang_id, $sanpham_id, $so_luong, $gia, $thanh_tien)";
                if (!$this->db->execute($query_ct)) {
                    throw new Exception("Lỗi khi thêm chi tiết đơn hàng.");
                }
            }
            $db_handle->commit();
            return true;
        } catch (Exception $e) {
            $db_handle->rollback();
            return false;
        }
    }
    public function updateStatus($donhang_id, $status)
    {
        $safe_id = (int)$donhang_id;
        $safe_status = $this->db->escapeString($status);
        $query = "UPDATE " . $this->table_donhang . " SET trang_thai_don_hang = '$safe_status' WHERE donhang_id = $safe_id";
        return $this->db->execute($query);
    }
    public function delete($id)
    {
        $db_handle = $this->db->getConnectionHandle();
        $db_handle->begin_transaction();
        $safe_id = (int)$id;
        try {
            $query_ct = "DELETE FROM " . $this->table_chitiet . " WHERE donhang_id = $safe_id";
            if (!$this->db->execute($query_ct)) {
                throw new Exception("Lỗi xóa chi tiết đơn hàng.");
            }
            $query_dh = "DELETE FROM " . $this->table_donhang . " WHERE donhang_id = $safe_id";
            if (!$this->db->execute($query_dh)) {
                throw new Exception("Lỗi xóa đơn hàng.");
            }
            $db_handle->commit();
            return true;
        } catch (Exception $e) {
            $db_handle->rollback();
            return false;
        }
    }
    public static function getStatusText($status_code)
    {
        switch (strtolower($status_code)) {
            case 'pending':
                return 'Đang chờ xử lý';
            case 'processing':
                return 'Đang xử lý';
            case 'shipped':
                return 'Đã giao hàng';
            case 'completed':
                return 'Hoàn thành';
            case 'cancelled':
                return 'Đã hủy';
            default:
                return 'Không xác định';
        }
    }
    public static function getStatusClass($status_code)
    {
        switch (strtolower($status_code)) {
            case 'pending':
            case 'processing':
                return 'pending';
            case 'shipped':
            case 'completed':
                return 'completed';
            case 'cancelled':
                return 'cancelled';
            default:
                return '';
        }
    }
}
