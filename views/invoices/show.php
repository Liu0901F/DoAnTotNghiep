<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Phiếu Thu Hóa Đơn</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .invoice-box { background: #fff; padding: 30px; border-radius: 10px; border: 1px dashed #ccc; margin-top: 20px;}
        .table-invoice th, .table-invoice td { padding: 10px; border-bottom: 1px solid #ddd; }
        .table-invoice th { background: #f9f9f9; }
        @media print {
            .no-print { display: none !important; }
            body { background: #fff; }
            .invoice-box { border: none; box-shadow: none; margin-top: 0; padding: 0;}
        }
    </style>
</head>
<body class="bg-light pb-5">

<div class="container mt-4">
    <div class="d-flex justify-content-between no-print mb-3">
        <a href="index.php?controller=invoice&action=index" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Trở về Danh sách</a>
        <button onclick="window.print()" class="btn btn-dark"><i class="bi bi-printer"></i> In Phiếu Thu</button>
    </div>

    <!-- Thông báo -->
    <?php if (!empty($_SESSION['success_msg'])): ?>
        <div class="alert alert-success no-print"><?= htmlspecialchars($_SESSION['success_msg']) ?></div>
        <?php unset($_SESSION['success_msg']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['error_msg'])): ?>
        <div class="alert alert-danger no-print"><?= htmlspecialchars($_SESSION['error_msg']) ?></div>
        <?php unset($_SESSION['error_msg']); ?>
    <?php endif; ?>

    <div class="row">
        <!-- BẢN IN HÓA ĐƠN -->
        <div class="col-lg-8">
            <div class="invoice-box shadow-sm">
                <div class="text-center mb-4">
                    <h2 class="fw-bold text-uppercase">PHIẾU THU TIỀN PHÒNG</h2>
                    <h5 class="text-muted">Tháng <?= $invoice['month'] ?> / Năm <?= $invoice['year'] ?></h5>
                </div>

                <div class="row mb-4">
                    <div class="col-6">
                        <p class="mb-1">Khách thuê: <strong><?= htmlspecialchars($invoice['tenant_name']) ?></strong></p>
                        <p class="mb-1">Số ĐT: <?= htmlspecialchars($invoice['tenant_phone']) ?></p>
                    </div>
                    <div class="col-6 text-end">
                        <h4 class="text-primary mb-0">Phòng: <b><?= htmlspecialchars($invoice['room_number']) ?></b></h4>
                        <p class="text-muted mt-1">Mã Phiếu: #<?= str_pad($invoice['id'], 6, "0", STR_PAD_LEFT) ?></p>
                    </div>
                </div>

                <table class="table table-invoice w-100 mb-4">
                    <thead>
                        <tr>
                            <th>Nội dung thu</th>
                            <th class="text-end">Thành tiền (VNĐ)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>1. Tiền thuê phòng cơ bản</strong></td>
                            <td class="text-end"><?= number_format($invoice['room_amount'], 0, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td>
                                <strong>2. Tiền Điện</strong><br>
                                <small class="text-muted">
                                    Chỉ số cũ: <?= $invoice['old_electricity'] ?> - Chỉ số mới: <?= $invoice['new_electricity'] ?> 
                                    (Tiêu thụ: <b><?= $invoice['new_electricity'] - $invoice['old_electricity'] ?></b> kWh)
                                </small>
                            </td>
                            <td class="text-end"><?= number_format($invoice['electricity_amount'], 0, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td>
                                <strong>3. Tiền Nước</strong><br>
                                <small class="text-muted">
                                    Chỉ số cũ: <?= $invoice['old_water'] ?> - Chỉ số mới: <?= $invoice['new_water'] ?>
                                    (Tiêu thụ: <b><?= $invoice['new_water'] - $invoice['old_water'] ?></b> khối)
                                </small>
                            </td>
                            <td class="text-end"><?= number_format($invoice['water_amount'], 0, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td><strong>4. Dịch vụ khác (Wifi, Rác...)</strong></td>
                            <td class="text-end"><?= number_format($invoice['other_services_amount'], 0, ',', '.') ?></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="text-end border-0 fs-5"><strong>TỔNG TIỀN PHẢI THANH TOÁN:</strong></td>
                            <td class="text-end border-0 fs-4 text-danger fw-bold"><?= number_format($invoice['total_amount'], 0, ',', '.') ?>đ</td>
                        </tr>
                        <tr>
                            <td class="text-end border-0 text-success">Đã thanh toán:</td>
                            <td class="text-end border-0 text-success fw-bold">- <?= number_format($invoice['paid_amount'], 0, ',', '.') ?>đ</td>
                        </tr>
                        <tr>
                            <td class="text-end fs-5 text-dark"><strong>CÒN NỢ LẠI:</strong></td>
                            <td class="text-end fs-5 text-dark fw-bold bg-warning bg-opacity-25 border rounded">
                                <?= number_format($invoice['total_amount'] - $invoice['paid_amount'], 0, ',', '.') ?>đ
                            </td>
                        </tr>
                    </tfoot>
                </table>

                <div class="row text-center mt-5">
                    <div class="col-6"><strong>Người Nộp Tiền</strong><br><small>(Ký, họ tên)</small></div>
                    <div class="col-6"><strong>Người Thu Tiền</strong><br><small>(Ký, họ tên)</small></div>
                </div>
                <br><br><br>
            </div>
        </div>

        <!-- BẢNG ĐIỀU KHIỂN NỘP TIỀN BÊN PHẢI -->
        <div class="col-lg-4 no-print mt-4 mt-lg-0">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-success text-white fw-bold">
                    <i class="bi bi-wallet2"></i> BÁO CÁO THANH TOÁN
                </div>
                <div class="card-body">
                    <?php 
                        $debt = $invoice['total_amount'] - $invoice['paid_amount'];
                        if($debt <= 0): 
                    ?>
                        <div class="alert alert-success text-center mb-0">
                            <i class="bi bi-check-circle-fill fs-1"></i><br>
                            <strong>HÓA ĐƠN ĐÃ ĐƯỢC THANH TOÁN ĐỦ</strong><br>
                            <small>Vào lúc: <?= date('d/m/Y H:i', strtotime($invoice['payment_date'])) ?></small>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            Khách đang nợ: <strong class="fs-4 text-danger"><?= number_format($debt, 0, ',', '.') ?>đ</strong>
                        </div>
                        
                        <form action="index.php?controller=invoice&action=pay" method="POST">
                            <input type="hidden" name="id" value="<?= $invoice['id'] ?>">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nhập số tiền khách đóng đợt này:</label>
                                <div class="input-group">
                                    <input type="number" name="pay_amount" class="form-control fw-bold text-success fs-5" required min="1000" max="<?= $debt ?>" value="<?= $debt ?>">
                                    <span class="input-group-text">VNĐ</span>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success w-100 fw-bold" onclick="return confirm('Xác nhận thu tiền hóa đơn này?');">
                                GHI NHẬN ĐÃ THU TIỀN
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</div>

</body>
</html>
