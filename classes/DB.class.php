<?php
// vị trí classes/DB.class.php
class DB
{
    private $conn;
    public function __construct()
    {
        $database = new Database(); 
        $this->conn = $database->getConnection();
    }
    public function select($sql)
    {
        $result = $this->conn->query($sql);
        $data = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_object()) {
                $data[] = $row;
            }
            $result->free();
        }
        return $data;
    }
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
    public function execute($sql)
    {
        $result = $this->conn->query($sql);
        if (!$result) {
            echo "Lỗi SQL: " . $this->conn->error . " | Query: " . htmlspecialchars($sql);
            return false;
        }
        return true;
    }
    public function lastInsertId()
    {
        return $this->conn->insert_id;
    }
    public function affectedRows()
    {
        return $this->conn->affected_rows;
    }
    public function escapeString($string)
    {
        if ($this->conn) {
            return $this->conn->real_escape_string($string);
        }
        return $string;
    }
    public function closeConnection()
    {
        if ($this->conn) {
            $this->conn->close();
        }
    }
    public function getConnectionHandle()
    {
        return $this->conn;
    }
}
