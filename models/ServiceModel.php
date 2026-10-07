<?php
class ServiceModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Dùng cho logic tính tiền
    public function getAllServices() {
        $query = "SELECT * FROM services";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $services = [];
        foreach($result as $row) {
            $services[$row['name']] = $row['unit_price'];
        }
        return $services;
    }

    // Lấy danh sách thô (để quản lý)
    public function getServicesList() {
        $query = "SELECT * FROM services ORDER BY id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Cập nhật giá dịch vụ
    public function updateService($id, $price) {
        $query = "UPDATE services SET unit_price = :price WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':price' => $price, ':id' => $id]);
    }
}
?>
