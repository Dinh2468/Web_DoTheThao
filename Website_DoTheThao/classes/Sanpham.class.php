<?php
// classes/Sanpham.class.php

class Sanpham
{
    private $db;
    private $table_sanpham = 'sanpham';
    private $table_loaisp = 'loaisanpham';
    private $table_ncc = 'nhacungcap';
    public $thuong_hieu;
    public $hinh_anh;

    public $ten_loai;
    public $ten_ncc;

    public $sanpham_id;
    public $loai_id;
    public $ten_sanpham;
    public $gia;
    public $so_luong_ton;
    public $mo_ta;
    public $ncc_id;




    public function __construct()
    {
        // Khởi tạo đối tượng DB để có thể gọi các phương thức select/execute
        $this->db = new DB();
    }

    // ======================================================================
    // PHƯƠNG THỨC ĐỌC (READ)
    // ======================================================================

    /**
     * Lấy tất cả sản phẩm, bao gồm tên loại sản phẩm.
     */
    public function readAll()
    {
        $query = "
            SELECT 
                sp.*, 
                lsp.ten_loai,
                ncc.ten_ncc 
            FROM 
                " . $this->table_sanpham . " sp
            JOIN
                " . $this->table_loaisp . " lsp ON sp.loai_id = lsp.loai_id
            LEFT JOIN
                " . $this->table_ncc . " ncc ON sp.ncc_id = ncc.ncc_id
            ORDER BY sp.sanpham_id DESC"; // Thêm ORDER BY

        return $this->db->select($query);
    }

    /**
     * Lấy chi tiết một sản phẩm dựa trên ID.
     */
    public function readOne($id)
    {
        $safe_id = (int)$id;
        $query = "
            SELECT 
                sp.*, 
                lsp.ten_loai,
                ncc.ten_ncc 
            FROM 
                " . $this->table_sanpham . " sp
            JOIN
                " . $this->table_loaisp . " lsp ON sp.loai_id = lsp.loai_id   
            LEFT JOIN
                " . $this->table_ncc . " ncc ON sp.ncc_id = ncc.ncc_id 
            WHERE 
                sp.sanpham_id = $safe_id
            LIMIT 0,1";

        $row = $this->db->selectOne($query);

        if ($row) {
            $this->sanpham_id = $row->sanpham_id ?? null;
            $this->loai_id = $row->loai_id ?? null;
            $this->ncc_id = $row->ncc_id ?? null;
            $this->thuong_hieu = $row->thuong_hieu ?? null;
            $this->ten_sanpham = $row->ten_sanpham ?? null;
            $this->gia = $row->gia ?? null;
            $this->so_luong_ton = $row->so_luong_ton ?? null;
            $this->mo_ta = $row->mo_ta ?? null;
            $this->hinh_anh = $row->hinh_anh ?? null;

            // Thuộc tính JOIN
            $this->ten_loai = $row->ten_loai ?? null;
            $this->ten_ncc = $row->ten_ncc ?? 'N/A';
            return true;
        }
        return false;
    }

    // ======================================================================
    // PHƯƠNG THỨC THAO TÁC (CREATE, UPDATE, DELETE)
    // ======================================================================

    /**
     * Thêm sản phẩm mới.
     * @param array $data Dữ liệu cần thêm.
     */
    public function create($data)
    {
        // Sử dụng transaction nếu bạn có nhiều thao tác (ví dụ: thêm ảnh)
        $ten_sanpham = $this->db->escapeString($data['ten_sanpham']);
        $loai_id = (int)$data['loai_id'];
        $gia = (float)$data['gia'];
        $so_luong_ton = (int)$data['so_luong_ton'];
        $mo_ta = $this->db->escapeString($data['mo_ta'] ?? '');
        $ngay_tao = date('Y-m-d H:i:s'); // Gán ngày tạo tự động

        $query = "INSERT INTO " . $this->table_sanpham . " 
                  (loai_id, ten_sanpham, gia, so_luong_ton, mo_ta, ngay_tao) 
                  VALUES ($loai_id, '$ten_sanpham', $gia, $so_luong_ton, '$mo_ta', '$ngay_tao')";

        return $this->db->execute($query);
    }

    /**
     * Cập nhật thông tin sản phẩm.
     * @param int $id sanpham_id cần cập nhật.
     * @param array $data Dữ liệu mới.
     */
    public function update($id, $data)
    {
        $safe_id = (int)$id;

        $ten_sanpham = $this->db->escapeString($data['ten_sanpham']);
        $thuong_hieu = $this->db->escapeString($data['thuong_hieu'] ?? '');
        $mo_ta = $this->db->escapeString($data['mo_ta'] ?? '');
        $hinh_anh = $this->db->escapeString($data['hinh_anh']);

        $loai_id = (int)$data['loai_id'];
        $gia = (float)$data['gia'];
        $so_luong_ton = (int)$data['so_luong_ton'];
        $ncc_id = empty($data['ncc_id']) ? 'NULL' : (int)$data['ncc_id'];

        $query = "UPDATE " . $this->table_sanpham . " SET 
                  loai_id = $loai_id, 
                  ncc_id = " . $ncc_id . ", 
                  thuong_hieu = '$thuong_hieu',
                  ten_sanpham = '$ten_sanpham', 
                  gia = $gia, 
                  so_luong_ton = $so_luong_ton, 
                  mo_ta = '$mo_ta',
                  hinh_anh = '$hinh_anh'
                  WHERE sanpham_id = $safe_id";

        // XÓA BỎ LỆNH die() Ở ĐÂY

        return $this->db->execute($query);
    }


    // classes/Sanpham.class.php (Sửa phương thức delete)

    public function delete($id)
    {
        $db_handle = $this->db->getConnectionHandle(); // Lấy đối tượng kết nối mysqli

        // Nếu getConnectionHandle() bị lỗi, ta dùng $this->db->conn nếu bạn đã định nghĩa nó là public
        // Giả sử $db_handle là đối tượng mysqli.

        $safe_id = (int)$id;

        // Bắt đầu Transaction
        $db_handle->begin_transaction();

        try {

            $query_danhgia = "DELETE FROM danhgia WHERE sanpham_id = $safe_id";
            if (!$db_handle->query($query_danhgia)) {
                throw new Exception("Lỗi xóa đánh giá.");
            }
            $query_chitiet_donhang = "DELETE FROM chitiet_donhang WHERE sanpham_id = $safe_id";
            if (!$db_handle->query($query_chitiet_donhang)) {
                throw new Exception("Lỗi xóa chi tiết đơn hàng.");
            }

            // 2. XÓA SẢN PHẨM (Bản ghi cha)
            $query_sanpham = "DELETE FROM " . $this->table_sanpham . " WHERE sanpham_id = $safe_id";
            if (!$db_handle->query($query_sanpham)) {
                throw new Exception("Lỗi xóa sản phẩm.");
            }

            // 3. THÀNH CÔNG: Commit Transaction
            $db_handle->commit();
            return true;
        } catch (Exception $e) {
            // LỖI: Rollback Transaction
            $db_handle->rollback();
            // Tùy chọn: Log lỗi $e->getMessage()
            return false;
        }
    }
    
    // ======================================================================
    // PHƯƠNG THỨC HỖ TRỢ (VALIDATION HELPERS)
    // ======================================================================

    /**
     * Kiểm tra tên sản phẩm đã tồn tại hay chưa.
     */
    public function checkTenSanphamExists($ten_sanpham, $exclude_id = 0)
    {
        $safe_ten_sanpham = $this->db->escapeString($ten_sanpham);
        $query = "SELECT COUNT(*) as count FROM " . $this->table_sanpham . " 
                  WHERE ten_sanpham = '$safe_ten_sanpham'";

        if ($exclude_id > 0) {
            $query .= " AND sanpham_id != " . (int)$exclude_id;
        }

        $result = $this->db->selectOne($query);
        return $result && $result->count > 0;
    }
    public function search($keywords)
    {
        // Loại bỏ ký tự đặc biệt khỏi từ khóa để tránh lỗi SQL Injection
        $keywords = htmlspecialchars(strip_tags($keywords));
        // Tạo chuỗi tìm kiếm (ví dụ: "%nike%")
        $search_term = "%{$keywords}%";

        // Truy vấn tìm kiếm theo tên sản phẩm, mô tả hoặc bất kỳ trường nào bạn muốn
        $query = "SELECT s.sanpham_id, s.ten_sanpham, s.gia, s.hinh_anh, s.so_luong_ton
                  FROM " . $this->table_sanpham . " s
                  WHERE s.ten_sanpham LIKE ? OR s.mo_ta LIKE ?
                  ORDER BY s.ten_sanpham ASC";

        $stmt = $this->db->getConnectionHandle()->prepare($query);

        // Liên kết tham số cho hai điều kiện LIKE
        $stmt->bind_param("ss", $search_term, $search_term);
        $stmt->execute();

        $result = $stmt->get_result();
        return $result; // Trả về đối tượng kết quả
    }
    public function readByLoaiId($loai_id)
    {
        // Cần đảm bảo loai_id là số nguyên để truy vấn an toàn
        $loai_id = (int) $loai_id;

        $query = "SELECT s.sanpham_id, s.ten_sanpham, s.gia, s.hinh_anh, s.so_luong_ton
                  FROM " . $this->table_sanpham . " s
                  WHERE s.loai_id = ?
                  ORDER BY s.ten_sanpham ASC";

        $stmt = $this->db->getConnectionHandle()->prepare($query);

        $stmt->bind_param("i", $loai_id); // 'i' cho integer
        $stmt->execute();

        $result = $stmt->get_result();
        return $result; // Trả về đối tượng kết quả
    }
}
