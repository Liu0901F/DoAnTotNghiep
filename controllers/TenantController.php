<?php
require_once 'core/Controller.php';
require_once 'models/TenantPortalModel.php';
require_once 'config/database.php';

class TenantController extends Controller {
    private $portalModel;

    public function __construct() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tenant') {
            die("<h2 style='color:red; text-align:center;'>Truy cập bị từ chối! Trang này chỉ dành cho Khách Thuê.</h2>");
        }
        $db = (new Database())->getConnection();
        $this->portalModel = new TenantPortalModel($db);
    }

    public function portal() {
        $userId = $_SESSION['user_id'];
        
        // 1. Lấy Hợp đồng đang hiệu lực
        $contract = $this->portalModel->getActiveContractByUserId($userId);
        
        $data = [
            'contract' => $contract,
            'members' => [],
            'invoices' => [],
            'requests' => [],
            'success_msg' => $_SESSION['success_msg'] ?? '',
            'error_msg' => $_SESSION['error_msg'] ?? ''
        ];
        unset($_SESSION['success_msg'], $_SESSION['error_msg']);

        if ($contract) {
            $data['members'] = $this->portalModel->getContractMembers($contract['id']);
            $data['invoices'] = $this->portalModel->getInvoicesByContract($contract['id']);
            $data['requests'] = $this->portalModel->getMaintenanceRequestsByTenant($contract['tenant_id']);
        }

        $this->view('tenant/portal', $data);
    }

    public function submitMaintenance() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $roomId = $_POST['room_id'];
            $tenantId = $_POST['tenant_id'];
            $title = trim($_POST['title']);
            $description = trim($_POST['description']);

            if (empty($title)) {
                $_SESSION['error_msg'] = "Vui lòng nhập Tiêu đề sự cố!";
            } else {
                if ($this->portalModel->submitMaintenanceRequest($roomId, $tenantId, $title, $description)) {
                    $_SESSION['success_msg'] = "Gửi yêu cầu hỗ trợ thành công! Ban quản lý sẽ xử lý sớm nhất.";
                } else {
                    $_SESSION['error_msg'] = "Có lỗi xảy ra, không thể gửi yêu cầu.";
                }
            }
        }
        header("Location: index.php?controller=tenant&action=portal");
        exit();
    }
}
?>
