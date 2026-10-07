<?php
class MaintenanceModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllRequests() {
        $query = "SELECT m.*, r.room_number, t.fullname as tenant_name, t.phone as tenant_phone 
                  FROM maintenance_requests m
                  JOIN rooms r ON m.room_id = r.id
                  JOIN tenants t ON m.tenant_id = t.id
                  ORDER BY m.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus($id, $status) {
        $query = "UPDATE maintenance_requests SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }
}
?>
