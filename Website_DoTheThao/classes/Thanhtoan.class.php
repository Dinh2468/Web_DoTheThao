<?php
// classes/Thanhtoan.class.php

class Thanhtoan
{
    private $db;
    private $table_thanhtoan = 'thanhtoan';
    private $table_donhang = 'donhang';

    // Thuộc tính của đối tượng Thanh toán
    public $thanhtoan_id;
    public $donhang_id;
    public $phuong_thuc; // Ví dụ: 'tienmat', 'chuyenkhoan', 'visa'
    public $so_tien;
    public $ngay_thanh_toan;
    public $trang_thai_thanhtoan; // Ví dụ: 'paid', 'pending', 'failed'
    public $ma_giao_dich; // Mã giao dịch ngân hàng/hệ thống (nếu có)

    // Thuộc tính hiển thị (được JOIN từ bảng khác)
    public $trang_thai_don_hang;

    public function __construct()
    {
        // Khởi tạo đối tượng DB
        $this->db = new DB();
    }

    // ======================================================================
    // PHƯƠNG THỨC ĐỌC (READ)
    // ======================================================================

    /**
     * Lấy tất cả giao dịch thanh toán.
     */
    public function readAll()
    {
        $query = "
            SELECT 
                tt.*, 
                dh.trang_thai_don_hang
            FROM 
                " . $this->table_thanhtoan . " tt
            JOIN
                " . $this->table_donhang . " dh ON tt.donhang_id = dh.donhang_id";

        return $this->db->select($query);
    }

    /**
     * Lấy chi tiết một giao dịch thanh toán.
     */
    public function readOne($id)
    {
        $safe_id = (int)$id;
        $query = "
            SELECT 
                tt.*, 
                dh.trang_thai_don_hang
            FROM 
                " . $this->table_thanhtoan . " tt
            JOIN
                " . $this->table_donhang . " dh ON tt.donhang_id = dh.donhang_id
            WHERE 
                tt.thanhtoan_id = $safe_id
            LIMIT 0,1";

        $row = $this->db->selectOne($query);

        if ($row) {
            $this->thanhtoan_id = $row->thanhtoan_id ?? null;
            $this->donhang_id = $row->donhang_id ?? null;
            $this->phuong_thuc = $row->phuong_thuc ?? null;
            $this->so_tien = $row->so_tien ?? 0;
            $this->ngay_thanh_toan = $row->ngay_thanh_toan ?? null;
            $this->trang_thai_thanhtoan = $row->trang_thai_thanhtoan ?? 'pending';
            $this->ma_giao_dich = $row->ma_giao_dich ?? null;
            $this->trang_thai_don_hang = $row->trang_thai_don_hang ?? null;
            return true;
        }
        return false;
    }

    /**
     * Lấy tất cả thanh toán theo ID Đơn hàng.
     */
    public function readByDonhangId($donhang_id)
    {
        $safe_dh_id = (int)$donhang_id;
        $query = "
             SELECT 
                 tt.*
             FROM 
                 " . $this->table_thanhtoan . " tt
             WHERE 
                 tt.donhang_id = $safe_dh_id
             ORDER BY 
                 tt.ngay_thanh_toan DESC";

        return $this->db->select($query);
    }

    // ======================================================================
    // PHƯƠNG THỨC THAO TÁC (CREATE, UPDATE, DELETE)
    // ======================================================================

    /**
     * Thêm một giao dịch thanh toán mới.
     * (Thường được gọi khi đơn hàng được xác nhận hoặc thanh toán thực hiện)
     */
    public function create($data)
    {
        $donhang_id = (int)$data['donhang_id'];
        $phuong_thuc = $this->db->escapeString($data['phuong_thuc']);
        $so_tien = (float)$data['so_tien'];
        $ngay_thanh_toan = date('Y-m-d H:i:s');
        $trang_thai = $this->db->escapeString($data['trang_thai_thanhtoan'] ?? 'pending');
        $ma_giao_dich = $this->db->escapeString($data['ma_giao_dich'] ?? '');

        $query = "INSERT INTO " . $this->table_thanhtoan . " 
                  (donhang_id, phuong_thuc, so_tien, ngay_thanh_toan, trang_thai_thanhtoan, ma_giao_dich) 
                  VALUES ($donhang_id, '$phuong_thuc', $so_tien, '$ngay_thanh_toan', '$trang_thai', '$ma_giao_dich')";

        return $this->db->execute($query);
    }

    /**
     * Cập nhật trạng thái thanh toán.
     */
    public function updateStatus($thanhtoan_id, $status)
    {
        $safe_id = (int)$thanhtoan_id;
        $safe_status = $this->db->escapeString($status);

        $query = "UPDATE " . $this->table_thanhtoan . " SET trang_thai_thanhtoan = '$safe_status' WHERE thanhtoan_id = $safe_id";

        return $this->db->execute($query);
    }

    /**
     * Xóa một giao dịch thanh toán.
     */
    public function delete($id)
    {
        $safe_id = (int)$id;
        $query = "DELETE FROM " . $this->table_thanhtoan . " WHERE thanhtoan_id = $safe_id";

        return $this->db->execute($query);
    }
    
    // ======================================================================
    // PHƯƠNG THỨC HỖ TRỢ TRẠNG THÁI
    // ======================================================================

    /**
     * Lấy chuỗi mô tả trạng thái thanh toán.
     */
    public static function getStatusText($status_code)
    {
        switch (strtolower($status_code)) {
            case 'paid':
                return 'Đã thanh toán';
            case 'pending':
                return 'Chờ thanh toán';
            case 'failed':
                return 'Thất bại';
            case 'refunded':
                return 'Đã hoàn tiền';
            default:
                return 'Không xác định';
        }
    }

    /**
     * Lấy class CSS cho trạng thái thanh toán.
     */
    public static function getStatusClass($status_code)
    {
        switch (strtolower($status_code)) {
            case 'paid':
                return 'completed';
            case 'pending':
                return 'pending';
            case 'failed':
            case 'refunded':
                return 'cancelled';
            default:
                return '';
        }
    }
}
