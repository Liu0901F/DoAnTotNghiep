<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi Tiết Hợp Đồng - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .invoice-box { padding: 30px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0, 0, 0, 0.05); font-size: 16px; line-height: 24px; font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; color: #555; background: #fff; }
        .invoice-box table { width: 100%; line-height: inherit; text-align: left; }
        .invoice-box table td { padding: 5px; vertical-align: top; }
        @media print { .no-print { display: none !important; } .invoice-box { box-shadow: none; border: none; } }
    </style>
</head>
<body class="bg-light">

<div class="container my-5">
    
    <!-- Thanh công cụ -->
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <a href="index.php?controller=contract&action=index" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Quay Lại</a>
        
        <div>
            <button onclick="window.print()" class="btn btn-secondary me-2"><i class="bi bi-printer"></i> In Hợp Đồng</button>
            <?php if($contract['status'] == 'active'): ?>
                <!-- Form Thanh lý hợp đồng -->
                <form action="index.php?controller=contract&action=terminate" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn THANH LÝ hợp đồng này?\n- Trạng thái HĐ sẽ chuyển sang Đã thanh lý.\n- Phòng <?= htmlspecialchars($contract['room_number']) ?> sẽ được trả về trạng thái TRỐNG.');">
                    <input type="hidden" name="id" value="<?= $contract['id'] ?>">
                    <input type="hidden" name="room_id" value="<?= $contract['room_id'] ?>">
                    <button type="submit" class="btn btn-danger"><i class="bi bi-slash-circle"></i> Thanh Lý Hợp Đồng</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <!-- Thông báo -->
    <?php if (!empty($_SESSION['success_msg'])): ?>
        <div class="alert alert-success no-print"><?= htmlspecialchars($_SESSION['success_msg']) ?></div>
        <?php unset($_SESSION['success_msg']); ?>
    <?php endif; ?>

    <!-- Trang Biên Bản -->
    <div class="invoice-box">
        <div class="text-center mb-4 border-bottom pb-3">
            <h2 class="fw-bold text-uppercase mb-1">Cộng Hòa Xã Hội Chủ Nghĩa Việt Nam</h2>
            <h4 class="fw-bold">Độc lập - Tự do - Hạnh phúc</h4>
            <h3 class="mt-4 fw-bold text-primary">HỢP ĐỒNG THUÊ PHÒNG TRỌ</h3>
            <p>Mã HĐ: <b>#<?= str_pad($contract['id'], 5, "0", STR_PAD_LEFT) ?></b> | Trạng thái: 
                <?php 
                    if($contract['status'] == 'active') echo '<span class="text-success fw-bold">HIỆU LỰC</span>';
                    elseif($contract['status'] == 'terminated') echo '<span class="text-danger fw-bold">ĐÃ THANH LÝ</span>';
                    else echo '<span class="text-warning fw-bold">HẾT HẠN</span>';
                ?>
            </p>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <h5 class="fw-bold text-dark border-bottom pb-2">BÊN CHO THUÊ (BÊN A)</h5>
                <p class="mb-1">Đại diện: <strong>Chủ Trọ (Admin)</strong></p>
                <p class="mb-1">Số điện thoại: 090 123 4567</p>
            </div>
            <div class="col-md-6">
                <h5 class="fw-bold text-dark border-bottom pb-2">BÊN THUÊ (BÊN B)</h5>
                <p class="mb-1">Ông/Bà: <strong><?= htmlspecialchars($contract['tenant_name']) ?></strong></p>
                <p class="mb-1">CCCD: <?= htmlspecialchars($contract['tenant_id_card']) ?></p>
                <p class="mb-1">SĐT: <?= htmlspecialchars($contract['tenant_phone']) ?></p>
                <p class="mb-1">Quê quán: <?= htmlspecialchars($contract['hometown']) ?></p>
            </div>
        </div>

        <div class="mb-4">
            <h5 class="fw-bold text-dark border-bottom pb-2">CHI TIẾT THUÊ PHÒNG</h5>
            <table class="table table-bordered">
                <tr>
                    <td class="bg-light" width="30%"><strong>Số Phòng:</strong></td>
                    <td><b class="text-primary fs-5"><?= htmlspecialchars($contract['room_number']) ?></b> (<?= htmlspecialchars($contract['room_type_name']) ?>)</td>
                </tr>
                <tr>
                    <td class="bg-light"><strong>Thời gian thuê:</strong></td>
                    <td>Từ ngày <b><?= date('d/m/Y', strtotime($contract['start_date'])) ?></b> đến ngày <b><?= date('d/m/Y', strtotime($contract['end_date'])) ?></b></td>
                </tr>
                <tr>
                    <td class="bg-light"><strong>Giá thuê chốt (VNĐ/Tháng):</strong></td>
                    <td><b class="text-danger"><?= number_format($contract['rental_price'], 0, ',', '.') ?>đ</b></td>
                </tr>
                <tr>
                    <td class="bg-light"><strong>Tiền cọc (VNĐ):</strong></td>
                    <td><?= number_format($contract['deposit'], 0, ',', '.') ?>đ</td>
                </tr>
            </table>
        </div>

        <!-- THÀNH VIÊN Ở GHÉP -->
        <div class="mb-5">
            <h5 class="fw-bold text-dark border-bottom pb-2">DANH SÁCH NGƯỜI Ở CÙNG PHÒNG</h5>
            <?php if(count($members) > 0): ?>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Họ Tên</th>
                            <th>CCCD</th>
                            <th>Số ĐT</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($members as $index => $m): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= htmlspecialchars($m['fullname']) ?></td>
                                <td><?= htmlspecialchars($m['id_card']) ?></td>
                                <td><?= htmlspecialchars($m['phone']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-muted fst-italic">Không có thành viên ở ghép nào được đăng ký.</p>
            <?php endif; ?>
        </div>

        <div class="row text-center mt-5 pt-5 pb-5">
            <div class="col-6">
                <strong>ĐẠI DIỆN BÊN B</strong><br>
                <small class="text-muted">(Ký và ghi rõ họ tên)</small>
            </div>
            <div class="col-6">
                <strong>ĐẠI DIỆN BÊN A</strong><br>
                <small class="text-muted">(Ký và ghi rõ họ tên)</small>
            </div>
        </div>

    </div>
</div>

</body>
</html>
