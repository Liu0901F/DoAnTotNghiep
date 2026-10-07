<?php
class InvoiceModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy danh sách hợp đồng kèm chỉ số điện nước CŨ (dựa trên tháng ghi gần nhất)
    public function getActiveContractsWithReadings() {
        $query = "SELECT c.id as contract_id, c.rental_price, r.id as room_id, r.room_number,
                         COALESCE((SELECT new_electricity FROM meter_readings WHERE room_id = r.id ORDER BY year DESC, month DESC LIMIT 1), 0) as old_electricity,
                         COALESCE((SELECT new_water FROM meter_readings WHERE room_id = r.id ORDER BY year DESC, month DESC LIMIT 1), 0) as old_water
                  FROM contracts c
                  JOIN rooms r ON c.room_id = r.id
                  WHERE c.status = 'active'
                  ORDER BY r.room_number ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Dùng Transaction lưu đồng thời Chỉ số điện nước và Hóa đơn
    public function createInvoiceAndMeterReading($meterData, $invoiceData) {
        try {
            $this->conn->beginTransaction();

            $query1 = "INSERT INTO meter_readings (room_id, month, year, old_electricity, new_electricity, old_water, new_water, recorded_at) 
                       VALUES (:room_id, :month, :year, :old_electricity, :new_electricity, :old_water, :new_water, :recorded_at)";
            $stmt1 = $this->conn->prepare($query1);
            $stmt1->execute($meterData);

            $query2 = "INSERT INTO invoices (contract_id, month, year, room_amount, electricity_amount, water_amount, other_services_amount, total_amount, status) 
                       VALUES (:contract_id, :month, :year, :room_amount, :electricity_amount, :water_amount, :other_services_amount, :total_amount, 'unpaid')";
            $stmt2 = $this->conn->prepare($query2);
            $stmt2->execute($invoiceData);

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    // Lấy danh sách hóa đơn (có bộ lọc)
    public function getAllInvoices($month = '', $year = '', $status = '') {
        $query = "SELECT i.*, r.room_number, t.fullname as tenant_name 
                  FROM invoices i 
                  JOIN contracts c ON i.contract_id = c.id
                  JOIN rooms r ON c.room_id = r.id
                  JOIN tenants t ON c.tenant_id = t.id
                  WHERE 1=1 ";
        
        if ($month) $query .= " AND i.month = :month ";
        if ($year) $query .= " AND i.year = :year ";
        if ($status) $query .= " AND i.status = :status ";
        
        $query .= " ORDER BY i.year DESC, i.month DESC, r.room_number ASC";

        $stmt = $this->conn->prepare($query);
        if ($month) $stmt->bindParam(':month', $month);
        if ($year) $stmt->bindParam(':year', $year);
        if ($status) $stmt->bindParam(':status', $status);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy chi tiết 1 hóa đơn kèm số điện nước
    public function getInvoiceById($id) {
        $query = "SELECT i.*, r.id as room_id, r.room_number, t.fullname as tenant_name, t.phone as tenant_phone,
                         m.old_electricity, m.new_electricity, m.old_water, m.new_water
                  FROM invoices i 
                  JOIN contracts c ON i.contract_id = c.id
                  JOIN rooms r ON c.room_id = r.id
                  JOIN tenants t ON c.tenant_id = t.id
                  LEFT JOIN meter_readings m ON (m.room_id = r.id AND m.month = i.month AND m.year = i.year)
                  WHERE i.id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Cập nhật thanh toán (đóng tiền)
    public function updatePayment($id, $newPaidAmount, $status) {
        $query = "UPDATE invoices SET paid_amount = :paid_amount, status = :status, payment_date = NOW() WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':paid_amount' => $newPaidAmount,
            ':status' => $status,
            ':id' => $id
        ]);
    }
}
?>
