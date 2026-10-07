<?php
require_once 'core/Controller.php';
require_once 'models/ContractModel.php';
require_once 'models/TenantModel.php';
require_once 'config/database.php';

class ContractController extends Controller {
    private $contractModel;
    private $tenantModel;

    public function __construct() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            die("<h2 style='color:red; text-align:center;'>Truy cập bị từ chối!</h2>");
        }

        $database = new Database();
        $db = $database->getConnection();
        $this->contractModel = new ContractModel($db);
        $this->tenantModel = new TenantModel($db);
    }

    // Danh sách hợp đồng
    public function index() {
        $statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
        $contracts = $this->contractModel->getAllContracts($statusFilter);
        
        $data = [
            'contracts' => $contracts,
            'currentStatus' => $statusFilter,
            'success_msg' => isset($_SESSION['success_msg']) ? $_SESSION['success_msg'] : '',
            'error_msg' => isset($_SESSION['error_msg']) ? $_SESSION['error_msg'] : ''
        ];
        unset($_SESSION['success_msg']);
        unset($_SESSION['error_msg']);

        $this->view('contracts/index', $data);
    }

    // Form tạo hợp đồng
    public function create() {
        $data = [
            'empty_rooms' => $this->contractModel->getEmptyRooms(),
            'tenants' => $this->tenantModel->getAllTenants(),
            'error' => '',
            'old_data' => []
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $tenantId = $_POST['tenant_id'];
            $data['old_data'] = $_POST;
            
            // Xử lý tạo khách hàng mới nếu user chọn 'new'
            if ($tenantId == 'new') {
                $tenantData = [
                    'fullname' => trim($_POST['fullname']),
                    'id_card' => trim($_POST['id_card']),
                    'phone' => trim($_POST['phone']),
                    'hometown' => trim($_POST['hometown']),
                    'date_of_birth' => trim($_POST['date_of_birth'])
                ];

                if (empty($tenantData['fullname']) || empty($tenantData['id_card'])) {
                    $data['error'] = "Vui lòng nhập Họ tên và Số CCCD khách thuê mới!";
                    return $this->view('contracts/create', $data);
                }

                if ($this->tenantModel->checkIdCardExists($tenantData['id_card'])) {
                    $data['error'] = "Số CCCD này đã tồn tại trong hệ thống! Vui lòng chọn từ danh sách khách có sẵn.";
                    return $this->view('contracts/create', $data);
                }

                $tenantId = $this->tenantModel->createTenant($tenantData);
                if (!$tenantId) {
                    $data['error'] = "Lỗi khi lưu thông tin khách thuê mới.";
                    return $this->view('contracts/create', $data);
                }
            }

            // Dữ liệu hợp đồng
            $contractData = [
                'room_id' => $_POST['room_id'],
                'tenant_id' => $tenantId,
                'start_date' => $_POST['start_date'],
                'end_date' => $_POST['end_date'],
                'rental_price' => $_POST['rental_price'],
                'deposit' => $_POST['deposit']
            ];

            if (empty($contractData['room_id']) || empty($contractData['start_date']) || empty($contractData['end_date'])) {
                $data['error'] = "Vui lòng nhập đủ thông tin phòng, ngày bắt đầu và kết thúc.";
            } else {
                if ($this->contractModel->createContract($contractData)) {
                    $_SESSION['success_msg'] = "Tạo hợp đồng thành công! Phòng đã chuyển sang trạng thái Đang Thuê.";
                    header("Location: index.php?controller=contract&action=index");
                    exit();
                } else {
                    $data['error'] = "Lỗi tạo hợp đồng! (Kiểm tra lại kết nối Database)";
                }
            }
        }

        $this->view('contracts/create', $data);
    }

    // Chi tiết hợp đồng
    public function show() {
        if (!isset($_GET['id'])) {
            header("Location: index.php?controller=contract&action=index");
            exit();
        }

        $id = $_GET['id'];
        $contract = $this->contractModel->getContractById($id);

        if (!$contract) {
            $_SESSION['error_msg'] = "Không tìm thấy hợp đồng!";
            header("Location: index.php?controller=contract&action=index");
            exit();
        }

        $members = $this->contractModel->getContractMembers($id);
        
        $data = [
            'contract' => $contract,
            'members' => $members
        ];

        $this->view('contracts/show', $data);
    }

    // Thanh lý hợp đồng
    public function terminate() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id']) && isset($_POST['room_id'])) {
            $contractId = $_POST['id'];
            $roomId = $_POST['room_id'];
            
            if ($this->contractModel->terminateContract($contractId, $roomId)) {
                $_SESSION['success_msg'] = "Thanh lý hợp đồng thành công! Phòng đã được chuyển về trạng thái Trống.";
            } else {
                $_SESSION['error_msg'] = "Lỗi khi thanh lý hợp đồng!";
            }
        }
        header("Location: index.php?controller=contract&action=show&id=" . (isset($_POST['id']) ? $_POST['id'] : ''));
        exit();
    }
}
?>
