<?php
class TenantPortalModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy thông tin hợp đồng đang active bằng user_id của account đang login
    public function getActiveContractByUserId($userId) {
        $query = "SELECT c.*, r.room_number, r.price as room_price, t.id as tenant_id, t.fullname
                  FROM contracts c
                  JOIN tenants t ON c.tenant_id = t.id
                  JOIN rooms r ON c.room_id = r.id
                  WHERE t.user_id = :user_id AND c.status = 'active'
                  LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách thành viên ở chung
    public function getContractMembers($contractId) {
        $query = "SELECT * FROM contract_members WHERE contract_id = :contract_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':contract_id' => $contractId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy lịch sử hóa đơn của hợp đồng này
    public function getInvoicesByContract($contractId) {
        $query = "SELECT * FROM invoices WHERE contract_id = :contract_id ORDER BY year DESC, month DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':contract_id' => $contractId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Gửi yêu cầu sửa chữa
    public function submitMaintenanceRequest($roomId, $tenantId, $title, $description) {
        $query = "INSERT INTO maintenance_requests (room_id, tenant_id, title, description) 
                  VALUES (:room_id, :tenant_id, :title, :description)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':room_id' => $roomId,
            ':tenant_id' => $tenantId,
            ':title' => $title,
            ':description' => $description
        ]);
    }

    // Lấy danh sách yêu cầu sửa chữa của khách này
    public function getMaintenanceRequestsByTenant($tenantId) {
        $query = "SELECT * FROM maintenance_requests WHERE tenant_id = :tenant_id ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':tenant_id' => $tenantId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
