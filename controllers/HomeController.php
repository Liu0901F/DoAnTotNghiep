<?php
require_once 'core/Controller.php';
require_once 'models/DashboardModel.php';
require_once 'config/database.php';

class HomeController extends Controller {
    public function index() {
        // Phân rẽ luồng nếu là khách thuê (Tenant)
        if (isset($_SESSION['role']) && $_SESSION['role'] === 'tenant') {
            header("Location: index.php?controller=tenant&action=portal");
            exit();
        }

        // Logic dành cho Admin
        $db = (new Database())->getConnection();
        $dashboardModel = new DashboardModel($db);

        $roomStats = $dashboardModel->getRoomStats();
        $revenue = $dashboardModel->getRevenueThisMonth();
        $debt = $dashboardModel->getTotalDebt();
        $chartRoom = $dashboardModel->getChartRoomStatus();
        $chartRev = $dashboardModel->getChartRevenue6Months();
        $expiring = $dashboardModel->getExpiringContracts(30);

        $data = [
            'user' => [
                'fullname' => $_SESSION['fullname'],
                'role' => $_SESSION['role']
            ],
            'stats' => $roomStats,
            'revenue' => $revenue,
            'debt' => $debt,
            'chartRoom' => json_encode($chartRoom),
            'chartRev' => json_encode($chartRev),
            'expiring' => $expiring
        ];
        
        $this->view('home/index', $data);
    }
}
?>
