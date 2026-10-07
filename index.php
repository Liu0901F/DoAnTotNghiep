<?php
// Bắt đầu session ngay từ dòng đầu tiên
session_start();

// Bật hiển thị lỗi trong quá trình phát triển
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Mặc định controller và action
$controller = isset($_GET['controller']) ? $_GET['controller'] : 'home';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// ==========================================
// BỘ LỌC BẢO MẬT: KIỂM TRA ĐĂNG NHẬP (MIDDLEWARE)
// ==========================================
$isLoggedIn = isset($_SESSION['user_id']);

// Nếu chưa đăng nhập VÀ đang cố truy cập các trang khác ngoài 'auth' (Đăng nhập)
if (!$isLoggedIn && $controller != 'auth') {
    // Ép chuyển hướng về trang đăng nhập
    header("Location: index.php?controller=auth&action=login");
    exit();
}

// Nếu đã đăng nhập NHƯNG lại cố truy cập lại trang login
if ($isLoggedIn && $controller == 'auth' && $action == 'login') {
    header("Location: index.php?controller=home&action=index");
    exit();
}
// ==========================================

// GỌI CONTROLLER TƯƠNG ỨNG
$controllerName = ucfirst($controller) . 'Controller';
$controllerFile = "controllers/" . $controllerName . ".php";

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    
    $controllerObj = new $controllerName();
    
    // Kiểm tra xem phương thức action có tồn tại trong class không
    if (method_exists($controllerObj, $action)) {
        $controllerObj->$action(); // Thực thi xử lý logic
    } else {
        die("<h1 style='color:red'>404 Not Found</h1><p>Action <b>'$action'</b> không tồn tại trong Controller '$controllerName'!</p>");
    }
} else {
    die("<h1 style='color:red'>404 Not Found</h1><p>Controller <b>'$controllerName'</b> không tồn tại!</p>");
}
?>
