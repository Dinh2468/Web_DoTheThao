<?php
//vị trí config/connect.php
$configDB = array();
$configDB["host"]     = "localhost";
$configDB["database"] = "db_dothethao";
$configDB["username"] = "root";
$configDB["password"] = "";
define("HOST", $configDB["host"]);
define("DB_NAME", $configDB["database"]);
define("DB_USER", $configDB["username"]);
define("DB_PASS", $configDB["password"]);
define('ROOT_PATH', dirname(dirname(__FILE__)));
// define('BASE_URL', 'http://localhost/Website_DoTheThao/');
define("BASE_URL", "http://" . $_SERVER['SERVER_NAME'] . "/WEBSITE_DOTHETHAO/");
class Database
{
    private $conn;
    public function getConnection()
    {
        $this->conn = new mysqli(HOST, DB_USER, DB_PASS, DB_NAME);
        if ($this->conn->connect_error) {
            die("Lỗi kết nối CSDL: " . $this->conn->connect_error);
        }
        $this->conn->set_charset("utf8");
        return $this->conn;
    }
}
