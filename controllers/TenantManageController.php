<?php
require_once 'core/Controller.php';
require_once 'models/TenantModel.php';
require_once 'config/database.php';

class TenantManageController extends Controller {
    private $tenantModel;

    public function __construct() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            die("Truy cập bị từ chối!");
        }
        $db = (new Database())->getConnection();
        $this->tenantModel = new TenantModel($db);
    }

    public function index() {
        $data = [
            'tenants' => $this->tenantModel->getAllTenants(),
            'success_msg' => $_SESSION['success_msg'] ?? '',
            'error_msg' => $_SESSION['error_msg'] ?? ''
        ];
        unset($_SESSION['success_msg'], $_SESSION['error_msg']);
        $this->view('tenants/index', $data);
    }

    public function show() {
        if (!isset($_GET['id'])) {
            header("Location: index.php?controller=tenantManage&action=index");
            exit();
        }
        $id = $_GET['id'];
        $data = [
            'tenant' => $this->tenantModel->getTenantById($id),
            'contracts' => $this->tenantModel->getTenantContracts($id)
        ];
        $this->view('tenants/show', $data);
    }

    public function edit() {
        if (!isset($_GET['id'])) {
            header("Location: index.php?controller=tenantManage&action=index");
            exit();
        }
        $id = $_GET['id'];
        $data = [
            'tenant' => $this->tenantModel->getTenantById($id),
            'error' => ''
        ];
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $postData = [
                'fullname' => trim($_POST['fullname']),
                'id_card' => trim($_POST['id_card']),
                'phone' => trim($_POST['phone']),
                'hometown' => trim($_POST['hometown']),
                'date_of_birth' => $_POST['date_of_birth']
            ];
            
            if ($this->tenantModel->checkIdCardExists($postData['id_card'], $id)) {
                $data['error'] = "Số CCCD đã tồn tại trên hệ thống!";
                $data['tenant'] = array_merge($data['tenant'], $postData); // Giữ dữ liệu form
            } else {
                $this->tenantModel->updateTenant($id, $postData);
                $_SESSION['success_msg'] = "Cập nhật thông tin khách thuê thành công!";
                header("Location: index.php?controller=tenantManage&action=index");
                exit();
            }
        }
        $this->view('tenants/edit', $data);
    }
}
?>
