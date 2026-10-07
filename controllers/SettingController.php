<?php
require_once 'core/Controller.php';
require_once 'models/ServiceModel.php';
require_once 'models/RoomTypeModel.php';
require_once 'config/database.php';

class SettingController extends Controller {
    private $serviceModel;
    private $roomTypeModel;

    public function __construct() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            die("Truy cập bị từ chối!");
        }
        $db = (new Database())->getConnection();
        $this->serviceModel = new ServiceModel($db);
        $this->roomTypeModel = new RoomTypeModel($db);
    }

    public function index() {
        $data = [
            'services' => $this->serviceModel->getServicesList(),
            'room_types' => $this->roomTypeModel->getAll(),
            'success_msg' => $_SESSION['success_msg'] ?? '',
            'error_msg' => $_SESSION['error_msg'] ?? ''
        ];
        unset($_SESSION['success_msg'], $_SESSION['error_msg']);
        $this->view('settings/index', $data);
    }

    public function updateService() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['services'])) {
            $hasError = false;
            foreach ($_POST['services'] as $id => $price) {
                if (!$this->serviceModel->updateService($id, $price)) {
                    $hasError = true;
                }
            }
            if ($hasError) {
                $_SESSION['error_msg'] = "Có lỗi xảy ra khi cập nhật giá dịch vụ.";
            } else {
                $_SESSION['success_msg'] = "Cập nhật bảng giá dịch vụ thành công!";
            }
        }
        header("Location: index.php?controller=setting&action=index");
        exit();
    }

    public function saveRoomType() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'name' => trim($_POST['name']),
                'base_price' => $_POST['base_price'],
                'description' => trim($_POST['description'])
            ];
            
            if (isset($_POST['id']) && !empty($_POST['id'])) {
                // Sửa
                $this->roomTypeModel->update($_POST['id'], $data);
                $_SESSION['success_msg'] = "Cập nhật loại phòng thành công!";
            } else {
                // Thêm mới
                $this->roomTypeModel->create($data);
                $_SESSION['success_msg'] = "Thêm loại phòng mới thành công!";
            }
        }
        header("Location: index.php?controller=setting&action=index");
        exit();
    }
}
?>
