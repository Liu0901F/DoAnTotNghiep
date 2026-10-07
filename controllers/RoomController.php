<?php
require_once 'core/Controller.php';
require_once 'models/RoomModel.php';
require_once 'config/database.php';

class RoomController extends Controller {
    private $roomModel;

    public function __construct() {
        // PHÂN QUYỀN: Chỉ admin mới được dùng module này
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            die("<h2 style='color:red; text-align:center; font-family:sans-serif; margin-top:50px;'>Truy cập bị từ chối! Chỉ tài khoản Admin mới có quyền thực hiện.</h2>");
        }

        $database = new Database();
        $db = $database->getConnection();
        $this->roomModel = new RoomModel($db);
    }

    // Xem danh sách phòng
    public function index() {
        $statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
        $rooms = $this->roomModel->getAllRooms($statusFilter);
        
        $data = [
            'rooms' => $rooms,
            'currentStatus' => $statusFilter,
            'success_msg' => isset($_SESSION['success_msg']) ? $_SESSION['success_msg'] : '',
            'error_msg' => isset($_SESSION['error_msg']) ? $_SESSION['error_msg'] : ''
        ];

        // Xóa flash message sau khi gán
        unset($_SESSION['success_msg']);
        unset($_SESSION['error_msg']);

        $this->view('rooms/index', $data);
    }

    // Thêm mới phòng
    public function create() {
        $data = [
            'room_types' => $this->roomModel->getRoomTypes(),
            'error' => '',
            'old_data' => [] // Giữ lại data khi submit lỗi
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $postData = [
                'room_number' => trim($_POST['room_number']),
                'room_type_id' => $_POST['room_type_id'],
                'price' => $_POST['price'],
                'status' => $_POST['status'],
                'max_occupants' => $_POST['max_occupants'],
                'description' => trim($_POST['description'])
            ];
            $data['old_data'] = $postData;

            if (empty($postData['room_number']) || empty($postData['price'])) {
                $data['error'] = "Vui lòng nhập đủ Mã phòng và Giá thuê!";
            } else if ($this->roomModel->checkRoomNumberExists($postData['room_number'])) {
                $data['error'] = "Số/Mã phòng này đã tồn tại! Vui lòng chọn tên khác.";
            } else {
                if ($this->roomModel->createRoom($postData)) {
                    $_SESSION['success_msg'] = "Đã thêm phòng mới thành công!";
                    header("Location: index.php?controller=room&action=index");
                    exit();
                } else {
                    $data['error'] = "Đã xảy ra lỗi DB khi thêm!";
                }
            }
        }
        $this->view('rooms/create', $data);
    }

    // Chỉnh sửa phòng
    public function edit() {
        if (!isset($_GET['id'])) {
            header("Location: index.php?controller=room&action=index");
            exit();
        }

        $id = $_GET['id'];
        $room = $this->roomModel->getRoomById($id);

        if (!$room) {
            $_SESSION['error_msg'] = "Phòng không tồn tại!";
            header("Location: index.php?controller=room&action=index");
            exit();
        }

        $data = [
            'room' => $room,
            'room_types' => $this->roomModel->getRoomTypes(),
            'error' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $postData = [
                'room_number' => trim($_POST['room_number']),
                'room_type_id' => $_POST['room_type_id'],
                'price' => $_POST['price'],
                'status' => $_POST['status'],
                'max_occupants' => $_POST['max_occupants'],
                'description' => trim($_POST['description'])
            ];

            if ($this->roomModel->checkRoomNumberExists($postData['room_number'], $id)) {
                $data['error'] = "Mã phòng mới bị trùng với phòng khác trên hệ thống!";
                $data['room'] = array_merge($data['room'], $postData); // Hiển thị lại dữ liệu vừa nhập
            } else {
                if ($this->roomModel->updateRoom($id, $postData)) {
                    $_SESSION['success_msg'] = "Cập nhật thông tin phòng thành công!";
                    header("Location: index.php?controller=room&action=index");
                    exit();
                } else {
                    $data['error'] = "Đã xảy ra lỗi khi cập nhật DB!";
                }
            }
        }
        $this->view('rooms/edit', $data);
    }

    // Xóa phòng
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
            $id = $_POST['id'];
            
            // Validate Logic: Đang có người thuê thì không được xóa
            if ($this->roomModel->checkActiveContracts($id)) {
                $_SESSION['error_msg'] = "Không thể xóa! Phòng này đang có hợp đồng thuê hoạt động.";
            } else {
                try {
                    $this->roomModel->deleteRoom($id);
                    $_SESSION['success_msg'] = "Đã xóa phòng thành công!";
                } catch(PDOException $e) {
                    $_SESSION['error_msg'] = "Lỗi toàn vẹn dữ liệu: Không thể xóa vì phòng đã phát sinh dữ liệu liên quan (Hóa đơn, điện nước,...).";
                }
            }
        }
        header("Location: index.php?controller=room&action=index");
        exit();
    }
}
?>
