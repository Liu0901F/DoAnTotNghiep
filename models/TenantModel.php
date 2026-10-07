<?php
class TenantModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllTenants() {
        $query = "SELECT * FROM tenants ORDER BY fullname ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTenantById($id) {
        $query = "SELECT * FROM tenants WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function checkIdCardExists($idCard, $excludeId = null) {
        $query = "SELECT id FROM tenants WHERE id_card = :id_card";
        if ($excludeId) {
            $query .= " AND id != :exclude_id";
        }
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_card', $idCard);
        if ($excludeId) {
            $stmt->bindParam(':exclude_id', $excludeId);
        }
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function createTenant($data) {
        $query = "INSERT INTO tenants (fullname, id_card, phone, hometown, date_of_birth) 
                  VALUES (:fullname, :id_card, :phone, :hometown, :date_of_birth)";
        $stmt = $this->conn->prepare($query);
        $result = $stmt->execute([
            ':fullname' => $data['fullname'],
            ':id_card' => $data['id_card'],
            ':phone' => $data['phone'],
            ':hometown' => $data['hometown'],
            ':date_of_birth' => $data['date_of_birth']
        ]);
        
        if ($result) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function updateTenant($id, $data) {
        $data['id'] = $id;
        $query = "UPDATE tenants SET fullname = :fullname, id_card = :id_card, phone = :phone, hometown = :hometown, date_of_birth = :date_of_birth WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($data);
    }

    public function getTenantContracts($tenantId) {
        $query = "SELECT c.*, r.room_number FROM contracts c JOIN rooms r ON c.room_id = r.id WHERE c.tenant_id = :tenant_id ORDER BY c.start_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':tenant_id' => $tenantId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
