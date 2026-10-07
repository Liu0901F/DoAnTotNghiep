<?php
class RoomModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy danh sách phòng kèm tên loại phòng
    public function getAllRooms($statusFilter = '') {
        $query = "SELECT r.*, rt.name as room_type_name 
                  FROM rooms r 
                  JOIN room_types rt ON r.room_type_id = rt.id ";
        
        if ($statusFilter !== '') {
            $query .= " WHERE r.status = :status ";
        }
        $query .= " ORDER BY r.room_number ASC";
        
        $stmt = $this->conn->prepare($query);
        if ($statusFilter !== '') {
            $stmt->bindParam(':status', $statusFilter);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy thông tin 1 phòng cụ thể
    public function getRoomById($id) {
        $query = "SELECT * FROM rooms WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách loại phòng cho dropdown
    public function getRoomTypes() {
        $query = "SELECT * FROM room_types ORDER BY name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Kiểm tra phòng đã tồn tại chưa (khi thêm hoặc sửa)
    public function checkRoomNumberExists($roomNumber, $excludeId = null) {
        $query = "SELECT id FROM rooms WHERE room_number = :room_number";
        if ($excludeId) {
            $query .= " AND id != :exclude_id";
        }
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':room_number', $roomNumber);
        if ($excludeId) {
            $stmt->bindParam(':exclude_id', $excludeId);
        }
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    // Thêm phòng
    public function createRoom($data) {
        $query = "INSERT INTO rooms (room_number, room_type_id, price, status, max_occupants, description) 
                  VALUES (:room_number, :room_type_id, :price, :status, :max_occupants, :description)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':room_number' => $data['room_number'],
            ':room_type_id' => $data['room_type_id'],
            ':price' => $data['price'],
            ':status' => $data['status'],
            ':max_occupants' => $data['max_occupants'],
            ':description' => $data['description']
        ]);
    }

    // Cập nhật thông tin phòng
    public function updateRoom($id, $data) {
        $query = "UPDATE rooms SET 
                    room_number = :room_number,
                    room_type_id = :room_type_id,
                    price = :price,
                    status = :status,
                    max_occupants = :max_occupants,
                    description = :description 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $data['id'] = $id;
        return $stmt->execute($data);
    }

    // Kiểm tra có hợp đồng đang hoạt động không (dùng khi Xóa)
    public function checkActiveContracts($roomId) {
        $query = "SELECT id FROM contracts WHERE room_id = :room_id AND status = 'active' LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':room_id', $roomId);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    // Xóa phòng
    public function deleteRoom($id) {
        $query = "DELETE FROM rooms WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>
