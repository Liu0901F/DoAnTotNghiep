<?php
class Controller {
    // Phương thức gọi view và truyền dữ liệu
    public function view($view, $data = []) {
        // Biến các key của mảng $data thành các biến
        extract($data);
        
        $viewFile = "views/" . $view . ".php";
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die("View '$view' không tồn tại!");
        }
    }
}
?>
