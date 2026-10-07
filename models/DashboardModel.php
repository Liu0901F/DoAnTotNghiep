<?php
class DashboardModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy tổng số phòng và số phòng đang thuê
    public function getRoomStats() {
        $query = "SELECT 
                    COUNT(*) as total, 
                    SUM(CASE WHEN status = 'rented' THEN 1 ELSE 0 END) as rented 
                  FROM rooms";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $total = (int)$res['total'];
        $rented = (int)$res['rented'];
        $percent = $total > 0 ? round(($rented / $total) * 100) : 0;
        
        return ['total' => $total, 'rented' => $rented, 'percent' => $percent];
    }

    // Doanh thu thực thu tháng này
    public function getRevenueThisMonth() {
        $month = date('m');
        $year = date('Y');
        $query = "SELECT SUM(paid_amount) as total_revenue FROM invoices WHERE month = :month AND year = :year";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':month' => $month, ':year' => $year]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return (float)$res['total_revenue'];
    }

    // Tổng tiền khách còn nợ toàn hệ thống
    public function getTotalDebt() {
        $query = "SELECT SUM(total_amount - paid_amount) as total_debt FROM invoices WHERE status != 'paid'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return (float)$res['total_debt'];
    }

    // Dữ liệu cho Doughnut Chart (Trạng thái phòng)
    public function getChartRoomStatus() {
        $query = "SELECT status, COUNT(*) as count FROM rooms GROUP BY status";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $data = ['empty' => 0, 'rented' => 0, 'maintenance' => 0];
        foreach($rows as $r) {
            $data[$r['status']] = (int)$r['count'];
        }
        return $data;
    }

    // Dữ liệu cho Bar Chart (Doanh thu 6 tháng gần nhất)
    public function getChartRevenue6Months() {
        // Lấy 6 tháng gần nhất từ bảng invoices (dựa theo month/year)
        $query = "SELECT month, year, SUM(paid_amount) as revenue 
                  FROM invoices 
                  GROUP BY year, month 
                  ORDER BY year DESC, month DESC 
                  LIMIT 6";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $rows = array_reverse($stmt->fetchAll(PDO::FETCH_ASSOC)); // Đảo ngược để theo thứ tự thời gian tăng dần
        
        $labels = [];
        $data = [];
        foreach($rows as $r) {
            $labels[] = "T" . $r['month'] . "/" . substr($r['year'], 2);
            $data[] = (float)$r['revenue'];
        }
        
        return ['labels' => $labels, 'data' => $data];
    }

    // Lấy danh sách hợp đồng sắp hết hạn (trong 30 ngày)
    public function getExpiringContracts($days = 30) {
        $query = "SELECT c.*, r.room_number, t.fullname as tenant_name, t.phone 
                  FROM contracts c
                  JOIN rooms r ON c.room_id = r.id
                  JOIN tenants t ON c.tenant_id = t.id
                  WHERE c.status = 'active' AND c.end_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL :days DAY)
                  ORDER BY c.end_date ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':days', $days, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
