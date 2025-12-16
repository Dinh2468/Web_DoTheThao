<?php
// classes/Loaisp.class.php

class Loaisp
{
    private $db;
    private $table_name = 'loaisanpham'; // Đặt tên bảng Loai San Pham của bạn

    // Thuộc tính của đối tượng Loai San Pham
    public $loai_id;
    public $ten_loai;
    public $mo_ta;

    public function __construct()
    {
        // Khởi tạo đối tượng DB để có thể gọi các phương thức select/execute
        $this->db = new DB();
    }

    // ======================================================================
    // PHƯƠNG THỨC ĐỌC (READ)
    // ======================================================================

    /**
     * Lấy tất cả các loại sản phẩm.
     */
    public function readAll()
    {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY ten_loai ASC";
        return $this->db->select($query);
    }

    /**
     * Lấy chi tiết một loại sản phẩm dựa trên ID.
     */
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

    // ======================================================================
    // PHƯƠNG THỨC THAO TÁC (CREATE, UPDATE, DELETE)
    // ======================================================================

    /**
     * Thêm loại sản phẩm mới.
     * @param array $data Dữ liệu cần thêm.
     */
    public function create($data)
    {
        $ten_loai = $this->db->escapeString($data['ten_loai']);
        $mo_ta = $this->db->escapeString($data['mo_ta'] ?? ''); // Cho phép mô tả rỗng

        $query = "INSERT INTO " . $this->table_name . " (ten_loai, mo_ta) 
                  VALUES ('$ten_loai', '$mo_ta')";

        return $this->db->execute($query);
    }

    /**
     * Cập nhật thông tin loại sản phẩm.
     * @param int $id loai_id cần cập nhật.
     * @param array $data Dữ liệu mới.
     */
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

    /**
     * Xóa một loại sản phẩm.
     * CẦN XỬ LÝ: Nếu có sản phẩm liên quan đến loại này, cần đảm bảo các sản phẩm đó được xử lý
     * (ví dụ: chuyển sang loại khác, hoặc xóa luôn nếu không có ràng buộc khóa ngoại ON DELETE CASCADE).
     */
    public function delete($id)
    {
        $safe_id = (int)$id;

        // TODO: THÊM LOGIC XÓA SẢN PHẨM HOẶC CHUYỂN SẢN PHẨM KHÁC NẾU CẦN

        $query = "DELETE FROM " . $this->table_name . " WHERE loai_id = $safe_id";

        return $this->db->execute($query);
    }
    
    // ======================================================================
    // PHƯƠNG THỨC HỖ TRỢ (VALIDATION HELPERS)
    // ======================================================================

    /**
     * Kiểm tra tên loại sản phẩm đã tồn tại hay chưa.
     * @param string $ten_loai Tên loại sản phẩm cần kiểm tra.
     * @param int $exclude_id Loại trừ ID này (khi update).
     */
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
