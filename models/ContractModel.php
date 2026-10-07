<?php
class ContractModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy danh sách hợp đồng
    public function getAllContracts($statusFilter = '') {
        $query = "SELECT c.*, r.room_number, t.fullname as tenant_name, t.id_card, t.phone 
                  FROM contracts c 
                  JOIN rooms r ON c.room_id = r.id 
                  JOIN tenants t ON c.tenant_id = t.id ";
        
        if ($statusFilter !== '') {
            $query .= " WHERE c.status = :status ";
        }
        $query .= " ORDER BY c.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        if ($statusFilter !== '') {
            $stmt->bindParam(':status', $statusFilter);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy chi tiết hợp đồng
    public function getContractById($id) {
        $query = "SELECT c.*, r.room_number, r.price as room_base_price, rt.name as room_type_name, 
                         t.fullname as tenant_name, t.id_card as tenant_id_card, t.phone as tenant_phone, t.hometown
                  FROM contracts c 
                  JOIN rooms r ON c.room_id = r.id 
                  JOIN room_types rt ON r.room_type_id = rt.id
                  JOIN tenants t ON c.tenant_id = t.id 
                  WHERE c.id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách thành viên ở ghép
    public function getContractMembers($contractId) {
        $query = "SELECT * FROM contract_members WHERE contract_id = :contract_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':contract_id', $contractId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách các phòng còn TRỐNG
    public function getEmptyRooms() {
        $query = "SELECT id, room_number, price, max_occupants FROM rooms WHERE status = 'empty' ORDER BY room_number ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Tạo hợp đồng mới (Dùng Transaction để đảm bảo tính toàn vẹn)
    public function createContract($data) {
        try {
            $this->conn->beginTransaction();
            
            // 1. Lưu hợp đồng
            $query = "INSERT INTO contracts (room_id, tenant_id, start_date, end_date, rental_price, deposit, status) 
                      VALUES (:room_id, :tenant_id, :start_date, :end_date, :rental_price, :deposit, 'active')";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':room_id' => $data['room_id'],
                ':tenant_id' => $data['tenant_id'],
                ':start_date' => $data['start_date'],
                ':end_date' => $data['end_date'],
                ':rental_price' => $data['rental_price'],
                ':deposit' => $data['deposit']
            ]);
            
            // 2. Chuyển trạng thái phòng thành 'rented' (Đang thuê)
            $updateRoom = "UPDATE rooms SET status = 'rented' WHERE id = :room_id";
            $stmt2 = $this->conn->prepare($updateRoom);
            $stmt2->execute([':room_id' => $data['room_id']]);
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    // Thanh lý hợp đồng (Dùng Transaction)
    public function terminateContract($contractId, $roomId) {
        try {
            $this->conn->beginTransaction();
            
            // 1. Cập nhật hợp đồng thành terminated
            $query = "UPDATE contracts SET status = 'terminated' WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':id' => $contractId]);
            
            // 2. Cập nhật trạng thái phòng thành empty
            $updateRoom = "UPDATE rooms SET status = 'empty' WHERE id = :room_id";
            $stmt2 = $this->conn->prepare($updateRoom);
            $stmt2->execute([':room_id' => $roomId]);
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
}
?>
