<?php
// vị trí classes/DB.class.php
// Đảm bảo file connect.php đã được nhúng (thường là trong file header hoặc file khởi tạo)
// Ví dụ: require_once '../config/connect.php'; 

class DB
{
    private $conn;

    /**
     * Khởi tạo đối tượng DB và thiết lập kết nối CSDL.
     * Sử dụng class Database từ connect.php để lấy kết nối mysqli.
     */
    public function __construct()
    {
        $database = new Database(); // Class Database được định nghĩa trong connect.php
        $this->conn = $database->getConnection();
    }


    public function select($sql)
    {
        $result = $this->conn->query($sql);
        $data = [];

        if ($result && $result->num_rows > 0) {
            // Lấy tất cả các hàng dưới dạng một mảng các đối tượng
            while ($row = $result->fetch_object()) {
                $data[] = $row;
            }
            $result->free();
        }

        return $data;
    }

    /**
     * Thực hiện truy vấn SELECT một hàng duy nhất và trả về dưới dạng đối tượng.
     * @param string $sql Câu truy vấn SQL (nên có LIMIT 1).
     * @return object|null Đối tượng kết quả, hoặc null nếu không tìm thấy.
     */
    public function selectOne($sql)
    {
        $result = $this->conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_object();
            $result->free();
            return $row;
        }

        return null;
    }

    /**
     * Thực hiện truy vấn INSERT, UPDATE, DELETE (Non-SELECT).
     * @param string $sql Câu truy vấn SQL.
     * @return bool True nếu truy vấn thành công, False nếu thất bại.
     */
    public function execute($sql)
    {
        $result = $this->conn->query($sql);

        if (!$result) {
            // Tùy chọn: Ghi log hoặc hiển thị lỗi
            echo "Lỗi SQL: " . $this->conn->error . " | Query: " . htmlspecialchars($sql);
            return false;
        }
        return true;
    }

    /**
     * Trả về ID của bản ghi được chèn gần nhất (chỉ dùng sau lệnh INSERT).
     * @return int ID cuối cùng được chèn.
     */
    public function lastInsertId()
    {
        return $this->conn->insert_id;
    }

    /**
     * Đếm số lượng hàng bị ảnh hưởng bởi truy vấn (chỉ dùng sau lệnh INSERT/UPDATE/DELETE).
     * @return int Số hàng bị ảnh hưởng.
     */
    public function affectedRows()
    {
        return $this->conn->affected_rows;
    }

    /**
     * Lọc và thoát chuỗi để ngăn chặn tấn công SQL Injection cơ bản (dùng cho dữ liệu chuỗi).
     * LƯU Ý: Nên dùng Prepared Statements cho bảo mật tốt hơn, nhưng phương thức này 
     * hữu ích cho các truy vấn đơn giản hoặc khi xây dựng câu lệnh thủ công.
     * @param string $string Chuỗi cần lọc.
     * @return string Chuỗi đã được lọc.
     */
    public function escapeString($string)
    {
        // Kiểm tra kết nối trước khi sử dụng real_escape_string
        if ($this->conn) {
            return $this->conn->real_escape_string($string);
        }
        return $string;
    }

    /**
     * Đóng kết nối CSDL (nếu cần thiết, thường được để PHP tự xử lý khi script kết thúc).
     */
    public function closeConnection()
    {
        if ($this->conn) {
            $this->conn->close();
        }
    }
    /**
     * Trả về đối tượng kết nối mysqli (handle) để thực hiện các thao tác cấp cao (ví dụ: transactions).
     * @return mysqli|null Đối tượng kết nối mysqli.
     */
    public function getConnectionHandle()
    {
        return $this->conn;
    }
}
