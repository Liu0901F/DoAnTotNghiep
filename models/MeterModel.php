<?php
class MeterModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Kiểm tra xem tháng/năm này của phòng đã được ghi điện nước chưa
    public function checkReadingExists($roomId, $month, $year) {
        $query = "SELECT id FROM meter_readings WHERE room_id = :room_id AND month = :month AND year = :year";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':room_id' => $roomId, ':month' => $month, ':year' => $year]);
        return $stmt->rowCount() > 0;
    }
}
?>
