<?php
require_once 'core/Controller.php';
require_once 'models/MaintenanceModel.php';
require_once 'config/database.php';

class MaintenanceController extends Controller {
    private $model;

    public function __construct() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') die("Truy cập bị từ chối!");
        $db = (new Database())->getConnection();
        $this->model = new MaintenanceModel($db);
    }

    public function index() {
        $data = [
            'requests' => $this->model->getAllRequests(),
            'success_msg' => $_SESSION['success_msg'] ?? ''
        ];
        unset($_SESSION['success_msg']);
        $this->view('maintenance/index', $data);
    }

    public function updateStatus() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'];
            $status = $_POST['status'];
            $this->model->updateStatus($id, $status);
            $_SESSION['success_msg'] = "Đã cập nhật trạng thái xử lý sự cố!";
        }
        header("Location: index.php?controller=maintenance&action=index");
        exit();
    }
}
?>
