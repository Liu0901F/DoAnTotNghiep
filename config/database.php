<?php
class Database {
    // Thông số kết nối CSDL
    private $host = "localhost";
    private $db_name = "quan_ly_phong_cho_thue";
    private $username = "root";
    private $password = ""; // Mặc định XAMPP không có mật khẩu root
    public $conn;

    // Phương thức tạo kết nối
    public function getConnection() {
        $this->conn = null;

        try {
            // Chuỗi kết nối PDO (DSN)
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4";
            
            // Khởi tạo kết nối PDO
            $this->conn = new PDO($dsn, $this->username, $this->password);
            
            // Cấu hình chế độ báo lỗi exception
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Cấu hình chế độ trả về dữ liệu mặc định là Mảng kết hợp (Associative Array)
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
        } catch(PDOException $exception) {
            // Bắt và hiển thị lỗi kết nối
            die("Lỗi kết nối CSDL: " . $exception->getMessage());
        }

        return $this->conn;
    }
}
?>
