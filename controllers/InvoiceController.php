<?php
require_once 'core/Controller.php';
require_once 'models/InvoiceModel.php';
require_once 'models/MeterModel.php';
require_once 'models/ServiceModel.php';
require_once 'config/database.php';

class InvoiceController extends Controller {
    private $invoiceModel;
    private $meterModel;
    private $serviceModel;

    public function __construct() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            die("<h2 style='color:red; text-align:center;'>Truy cập bị từ chối!</h2>");
        }
        $db = (new Database())->getConnection();
        $this->invoiceModel = new InvoiceModel($db);
        $this->meterModel = new MeterModel($db);
        $this->serviceModel = new ServiceModel($db);
    }

    public function index() {
        $month = $_GET['month'] ?? date('m');
        $year = $_GET['year'] ?? date('Y');
        $status = $_GET['status'] ?? '';

        $invoices = $this->invoiceModel->getAllInvoices($month, $year, $status);

        $data = [
            'invoices' => $invoices,
            'currentMonth' => $month,
            'currentYear' => $year,
            'currentStatus' => $status,
            'success_msg' => $_SESSION['success_msg'] ?? '',
            'error_msg' => $_SESSION['error_msg'] ?? ''
        ];
        unset($_SESSION['success_msg'], $_SESSION['error_msg']);

        $this->view('invoices/index', $data);
    }

    public function create() {
        $contracts = $this->invoiceModel->getActiveContractsWithReadings();
        $services = $this->serviceModel->getAllServices();

        $data = [
            'contracts' => $contracts,
            'services' => $services,
            'error' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $contractId = $_POST['contract_id'];
            $month = (int)$_POST['month'];
            $year = (int)$_POST['year'];
            
            // Lấy thông tin phòng dựa theo contract_id được gộp trong $_POST (Dùng chuỗi JS để bind, hoặc tìm tay)
            // Giải pháp an toàn hơn: Tìm contract_id trong danh sách $contracts
            $selectedContract = null;
            foreach ($contracts as $c) {
                if ($c['contract_id'] == $contractId) {
                    $selectedContract = $c;
                    break;
                }
            }

            if (!$selectedContract) {
                $data['error'] = "Hợp đồng không hợp lệ!";
            } else {
                $roomId = $selectedContract['room_id'];
                $oldElec = $selectedContract['old_electricity'];
                $oldWater = $selectedContract['old_water'];
                $newElec = (int)$_POST['new_electricity'];
                $newWater = (int)$_POST['new_water'];

                // Validate chỉ số mới
                if ($newElec < $oldElec || $newWater < $oldWater) {
                    $data['error'] = "Chỉ số mới không được nhỏ hơn chỉ số cũ!";
                } elseif ($this->meterModel->checkReadingExists($roomId, $month, $year)) {
                    $data['error'] = "Phòng {$selectedContract['room_number']} đã chốt điện nước trong Tháng $month/$year rồi!";
                } else {
                    // Tính toán tiền
                    $priceElec = ($newElec - $oldElec) * ($services['Điện'] ?? 3500);
                    $priceWater = ($newWater - $oldWater) * ($services['Nước'] ?? 20000);
                    $priceWifi = $services['Wifi'] ?? 100000;
                    $priceGarbage = $services['Rác'] ?? 30000;
                    
                    $roomAmount = $selectedContract['rental_price'];
                    $otherServices = $priceWifi + $priceGarbage;
                    $totalAmount = $roomAmount + $priceElec + $priceWater + $otherServices;

                    $meterData = [
                        ':room_id' => $roomId,
                        ':month' => $month,
                        ':year' => $year,
                        ':old_electricity' => $oldElec,
                        ':new_electricity' => $newElec,
                        ':old_water' => $oldWater,
                        ':new_water' => $newWater,
                        ':recorded_at' => date('Y-m-d')
                    ];

                    $invoiceData = [
                        ':contract_id' => $contractId,
                        ':month' => $month,
                        ':year' => $year,
                        ':room_amount' => $roomAmount,
                        ':electricity_amount' => $priceElec,
                        ':water_amount' => $priceWater,
                        ':other_services_amount' => $otherServices,
                        ':total_amount' => $totalAmount
                    ];

                    if ($this->invoiceModel->createInvoiceAndMeterReading($meterData, $invoiceData)) {
                        $_SESSION['success_msg'] = "Lập hóa đơn tháng $month/$year thành công!";
                        header("Location: index.php?controller=invoice&action=index");
                        exit();
                    } else {
                        $data['error'] = "Đã xảy ra lỗi khi tạo hóa đơn.";
                    }
                }
            }
        }

        $this->view('invoices/create', $data);
    }

    public function show() {
        if (!isset($_GET['id'])) {
            header("Location: index.php?controller=invoice&action=index");
            exit();
        }

        $id = $_GET['id'];
        $invoice = $this->invoiceModel->getInvoiceById($id);

        if (!$invoice) {
            $_SESSION['error_msg'] = "Hóa đơn không tồn tại!";
            header("Location: index.php?controller=invoice&action=index");
            exit();
        }

        $this->view('invoices/show', ['invoice' => $invoice]);
    }

    // Xử lý nộp tiền hóa đơn
    public function pay() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
            $id = $_POST['id'];
            $payAmount = (float)$_POST['pay_amount'];
            
            $invoice = $this->invoiceModel->getInvoiceById($id);
            if ($invoice) {
                $newTotalPaid = $invoice['paid_amount'] + $payAmount;
                
                // Xác định trạng thái
                if ($newTotalPaid >= $invoice['total_amount']) {
                    $status = 'paid';
                    $newTotalPaid = $invoice['total_amount']; // Không cho đóng lố
                } else {
                    $status = 'partially_paid';
                }

                if ($this->invoiceModel->updatePayment($id, $newTotalPaid, $status)) {
                    $_SESSION['success_msg'] = "Cập nhật thanh toán thành công!";
                } else {
                    $_SESSION['error_msg'] = "Lỗi khi cập nhật thanh toán!";
                }
            }
        }
        header("Location: index.php?controller=invoice&action=show&id=" . (isset($_POST['id']) ? $_POST['id'] : ''));
        exit();
    }
}
?>
