<?php
require_once 'core/Controller.php';
require_once 'models/UserModel.php';
require_once 'config/database.php';

class AuthController extends Controller {
    private $userModel;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->userModel = new UserModel($db);
    }

    // Xử lý hiển thị form và đăng nhập
    public function login() {
        // Nếu đã đăng nhập thì tự động về trang chủ
        if (isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=home&action=index");
            exit();
        }

        $data = ['error' => ''];

        // Khi người dùng bấm submit (POST)
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if (empty($username) || empty($password)) {
                $data['error'] = 'Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu!';
            } else {
                $user = $this->userModel->getUserByUsername($username);

                // Kiểm tra tài khoản tồn tại, trạng thái và mật khẩu (bcrypt)
                if ($user) {
                    if ($user['status'] == 0) {
                        $data['error'] = 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ Admin!';
                    } elseif (password_verify($password, $user['password'])) {
                        // Mật khẩu đúng => Lưu thông tin vào session
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['fullname'] = $user['fullname'];
                        $_SESSION['role'] = $user['role'];

                        // Đăng nhập thành công, chuyển hướng về Dashboard
                        header("Location: index.php?controller=home&action=index");
                        exit();
                    } else {
                        $data['error'] = 'Mật khẩu không chính xác!';
                    }
                } else {
                    $data['error'] = 'Tài khoản không tồn tại!';
                }
            }
        }

        // Gọi ra giao diện hiển thị form login
        $this->view('auth/login', $data);
    }

    // Xử lý đăng xuất
    public function logout() {
        // Xóa tất cả các session
        session_unset();
        session_destroy();
        
        // Chuyển hướng về trang login
        header("Location: index.php?controller=auth&action=login");
        exit();
    }
}
?>
