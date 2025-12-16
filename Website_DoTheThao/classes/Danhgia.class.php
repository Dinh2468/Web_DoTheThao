<?php
// classes/Danhgia.class.php

class Danhgia
{
    private $db;
    private $table_danhgia = 'danhgia';
    private $table_khachhang = 'khachhang';
    private $table_sanpham = 'sanpham';

    // Thuộc tính của đối tượng Đánh giá
    public $danhgia_id;
    public $sanpham_id;
    public $khachhang_id;
    public $diem_so; // Ví dụ: 1 đến 5 sao
    public $noi_dung;
    public $ngay_danh_gia;
    public $trang_thai; // Ví dụ: 'approved', 'pending'

    // Thuộc tính hiển thị (được JOIN từ bảng khác)
    public $ten_khachhang;
    public $ten_sanpham;

    public function __construct()
    {
        // Khởi tạo đối tượng DB
        $this->db = new DB();
    }

    // ======================================================================
    // PHƯƠNG THỨC ĐỌC (READ)
    // ======================================================================

    /**
     * Lấy tất cả đánh giá, bao gồm tên khách hàng và sản phẩm.
     */
    public function readAll()
    {
        $query = "
            SELECT 
                dg.*, 
                kh.ho_ten AS ten_khachhang,
                sp.ten_sanpham
            FROM 
                " . $this->table_danhgia . " dg
            JOIN
                " . $this->table_khachhang . " kh ON dg.khachhang_id = kh.khachhang_id
            JOIN 
                " . $this->table_sanpham . " sp ON dg.sanpham_id = sp.sanpham_id";

        return $this->db->select($query);
    }

    /**
     * Lấy chi tiết một đánh giá.
     */
    public function readOne($id)
    {
        $safe_id = (int)$id;
        $query = "
            SELECT 
                dg.*, 
                kh.ho_ten AS ten_khachhang,
                sp.ten_sanpham
            FROM 
                " . $this->table_danhgia . " dg
            JOIN
                " . $this->table_khachhang . " kh ON dg.khachhang_id = kh.khachhang_id
            JOIN 
                " . $this->table_sanpham . " sp ON dg.sanpham_id = sp.sanpham_id
            WHERE 
                dg.danhgia_id = $safe_id
            LIMIT 0,1";

        $row = $this->db->selectOne($query);

        if ($row) {
            $this->danhgia_id = $row->danhgia_id ?? null;
            $this->sanpham_id = $row->sanpham_id ?? null;
            $this->khachhang_id = $row->khachhang_id ?? null;
            $this->diem_so = $row->diem_so ?? 0;
            $this->noi_dung = $row->noi_dung ?? null;
            $this->ngay_danh_gia = $row->ngay_danh_gia ?? null;
            $this->trang_thai = $row->trang_thai ?? 'pending';
            $this->ten_khachhang = $row->ten_khachhang ?? null;
            $this->ten_sanpham = $row->ten_sanpham ?? null;
            return true;
        }
        return false;
    }

    /**
     * Lấy tất cả đánh giá theo sản phẩm ID.
     */
    public function readBySanphamId($sanpham_id)
    {
        $safe_sp_id = (int)$sanpham_id;
        $query = "
             SELECT 
                 dg.*, kh.ho_ten AS ten_khachhang
             FROM 
                 " . $this->table_danhgia . " dg
             JOIN
                 " . $this->table_khachhang . " kh ON dg.khachhang_id = kh.khachhang_id
             WHERE 
                 dg.sanpham_id = $safe_sp_id AND dg.trang_thai = 'approved'
             ORDER BY 
                 dg.ngay_danh_gia DESC";

        return $this->db->select($query);
    }


    // ======================================================================
    // PHƯƠNG THỨC THAO TÁC (CREATE, UPDATE, DELETE)
    // ======================================================================

    /**
     * Thêm một đánh giá mới.
     * (Thường được gọi từ phía người dùng/khách hàng)
     */
    public function create($data)
    {
        $sanpham_id = (int)$data['sanpham_id'];
        $khachhang_id = (int)$data['khachhang_id'];
        $diem_so = (int)$data['diem_so'];
        $noi_dung = $this->db->escapeString($data['noi_dung'] ?? '');
        $ngay_danh_gia = date('Y-m-d H:i:s');
        $trang_thai = 'pending'; // Mặc định chờ duyệt

        $query = "INSERT INTO " . $this->table_danhgia . " 
                  (sanpham_id, khachhang_id, diem_so, noi_dung, ngay_danh_gia, trang_thai) 
                  VALUES ($sanpham_id, $khachhang_id, $diem_so, '$noi_dung', '$ngay_danh_gia', '$trang_thai')";

        return $this->db->execute($query);
    }

    /**
     * Cập nhật trạng thái đánh giá (duyệt/từ chối/pending).
     */
    public function updateStatus($danhgia_id, $status)
    {
        $safe_id = (int)$danhgia_id;
        $safe_status = $this->db->escapeString($status);

        $query = "UPDATE " . $this->table_danhgia . " SET trang_thai = '$safe_status' WHERE danhgia_id = $safe_id";

        return $this->db->execute($query);
    }

    /**
     * Xóa một đánh giá.
     */
    public function delete($id)
    {
        $safe_id = (int)$id;
        $query = "DELETE FROM " . $this->table_danhgia . " WHERE danhgia_id = $safe_id";

        return $this->db->execute($query);
    }
    
    // ======================================================================
    // PHƯƠNG THỨC HỖ TRỢ TRẠNG THÁI
    // ======================================================================

    /**
     * Lấy chuỗi mô tả trạng thái đánh giá.
     */
    public static function getStatusText($status_code)
    {
        switch (strtolower($status_code)) {
            case 'approved':
                return 'Đã duyệt';
            case 'pending':
                return 'Chờ duyệt';
            case 'rejected':
                return 'Đã từ chối';
            default:
                return 'Không xác định';
        }
    }

    /**
     * Lấy class CSS cho trạng thái đánh giá.
     */
    public static function getStatusClass($status_code)
    {
        switch (strtolower($status_code)) {
            case 'approved':
                return 'completed';
            case 'pending':
                return 'pending';
            case 'rejected':
                return 'cancelled';
            default:
                return '';
        }
    }
}
