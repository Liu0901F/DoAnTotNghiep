<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Cổng Thông Tin Khách Thuê</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 24px; }
        .card-header { background-color: #fff; border-bottom: 1px solid #eee; font-weight: bold; border-radius: 12px 12px 0 0 !important; }
        .portal-header { background: linear-gradient(135deg, #20c997, #198754); color: white; padding: 40px 0; margin-bottom: 30px; border-radius: 0 0 30px 30px; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand fw-bold" href="#"><i class="bi bi-house-door-fill text-success"></i> MY ROOM PORTAL</a>
    <div class="d-flex align-items-center text-white">
        <span class="me-3"><i class="bi bi-person-circle"></i> Xin chào, <b><?= htmlspecialchars($_SESSION['fullname']) ?></b></span>
        <a href="index.php?controller=auth&action=logout" class="btn btn-sm btn-outline-light"><i class="bi bi-box-arrow-right"></i> Đăng xuất</a>
    </div>
  </div>
</nav>

<div class="portal-header shadow-sm">
    <div class="container text-center">
        <h2 class="fw-bold">CỔNG THÔNG TIN DÀNH CHO KHÁCH THUÊ</h2>
        <p class="mb-0 fs-5">Nơi bạn theo dõi thông tin phòng, hóa đơn và gửi yêu cầu hỗ trợ.</p>
    </div>
</div>

<div class="container mb-5">
    <?php if (!empty($success_msg)): ?>
        <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($success_msg) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>
    <?php if (!empty($error_msg)): ?>
        <div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($error_msg) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>

    <?php if(!$contract): ?>
        <div class="alert alert-warning text-center p-5 shadow-sm">
            <i class="bi bi-info-circle-fill fs-1 text-warning"></i>
            <h4 class="mt-3">Bạn chưa có hợp đồng thuê phòng nào!</h4>
            <p>Vui lòng liên hệ với Chủ Trọ/Admin để được hỗ trợ cấp phòng.</p>
        </div>
    <?php else: ?>
        <div class="row">
            <!-- Cột trái: Thông tin hợp đồng & Yêu cầu sửa chữa -->
            <div class="col-lg-4">
                <!-- Thông tin Hợp đồng -->
                <div class="card border-top border-4 border-success">
                    <div class="card-header text-success fs-5"><i class="bi bi-journal-text"></i> HỢP ĐỒNG CỦA BẠN</div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">Phòng đang thuê:</span>
                            <span class="fs-4 fw-bold text-dark">P. <?= htmlspecialchars($contract['room_number']) ?></span>
                        </div>
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span>Giá phòng cơ bản:</span>
                                <strong><?= number_format($contract['room_price'], 0, ',', '.') ?> đ</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span>Tiền cọc:</span>
                                <strong><?= number_format($contract['deposit'], 0, ',', '.') ?> đ</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span>Ký ngày:</span>
                                <strong><?= date('d/m/Y', strtotime($contract['start_date'])) ?></strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span>Hết hạn:</span>
                                <strong class="text-danger"><?= date('d/m/Y', strtotime($contract['end_date'])) ?></strong>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Báo hỏng sửa chữa -->
                <div class="card border-top border-4 border-warning">
                    <div class="card-header text-warning text-dark fs-5"><i class="bi bi-tools"></i> BÁO HỎNG / SỬA CHỮA</div>
                    <div class="card-body">
                        <form action="index.php?controller=tenant&action=submitMaintenance" method="POST">
                            <input type="hidden" name="room_id" value="<?= $contract['room_id'] ?>">
                            <input type="hidden" name="tenant_id" value="<?= $contract['tenant_id'] ?>">
                            <div class="mb-2">
                                <label class="form-label small fw-bold">Tiêu đề (Vấn đề gặp phải) *</label>
                                <input type="text" name="title" class="form-control" required placeholder="VD: Hỏng vòi nước, Điều hòa kêu...">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Mô tả chi tiết</label>
                                <textarea name="description" class="form-control" rows="3" placeholder="Mô tả cụ thể tình trạng..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-warning w-100 fw-bold"><i class="bi bi-send"></i> GỬI YÊU CẦU</button>
                        </form>

                        <!-- Lịch sử yêu cầu -->
                        <?php if(count($requests) > 0): ?>
                            <hr class="my-4">
                            <h6 class="fw-bold mb-3">Lịch Sử Yêu Cầu Của Bạn:</h6>
                            <ul class="list-group list-group-flush small">
                                <?php foreach($requests as $r): ?>
                                    <li class="list-group-item px-0 py-2 border-bottom">
                                        <div class="d-flex justify-content-between">
                                            <strong><?= htmlspecialchars($r['title']) ?></strong>
                                            <?php 
                                                if($r['status'] == 'resolved') echo '<span class="badge bg-success">Đã xử lý</span>';
                                                elseif($r['status'] == 'in_progress') echo '<span class="badge bg-primary">Đang sửa</span>';
                                                else echo '<span class="badge bg-secondary">Chờ xử lý</span>';
                                            ?>
                                        </div>
                                        <div class="text-muted" style="font-size:0.8rem"><?= date('d/m/Y H:i', strtotime($r['created_at'])) ?></div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Cột phải: Hóa đơn & Người ở chung -->
            <div class="col-lg-8">
                <!-- Hóa đơn -->
                <div class="card border-top border-4 border-danger">
                    <div class="card-header text-danger fs-5"><i class="bi bi-receipt-cutoff"></i> LỊCH SỬ HÓA ĐƠN ĐIỆN NƯỚC</div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Kỳ HĐ</th>
                                        <th>Tổng Tiền</th>
                                        <th>Đã Đóng</th>
                                        <th>Trạng Thái</th>
                                        <th class="text-center">Xem</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(count($invoices) > 0): ?>
                                        <?php foreach($invoices as $i): ?>
                                            <tr>
                                                <td><span class="fw-bold">T<?= $i['month'] ?>/<?= $i['year'] ?></span></td>
                                                <td class="text-danger fw-bold"><?= number_format($i['total_amount'], 0, ',', '.') ?>đ</td>
                                                <td class="text-success"><?= number_format($i['paid_amount'], 0, ',', '.') ?>đ</td>
                                                <td>
                                                    <?php 
                                                        if($i['status'] == 'paid') echo '<span class="badge bg-success">Đã thu đủ</span>';
                                                        elseif($i['status'] == 'partially_paid') echo '<span class="badge bg-warning text-dark">Nợ 1 phần</span>';
                                                        else echo '<span class="badge bg-danger">Chưa đóng</span>';
                                                    ?>
                                                </td>
                                                <td class="text-center">
                                                    <!-- Cho phép khách xem chi tiết PDF/Biên bản bằng hàm show của Invoice, nhưng hiện tại Controller Invoice khóa quyền Admin.
                                                         Khắc phục: Mở quyền xem hoặc tạo action riêng. Ở đây ta giả lập một link tĩnh, hoặc sẽ bỏ qua nút in vì scope không yêu cầu phức tạp. -->
                                                    <button class="btn btn-sm btn-outline-danger disabled" title="Xem PDF"><i class="bi bi-file-pdf"></i></button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="5" class="text-center py-4 text-muted">Chưa có hóa đơn nào phát sinh.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Người ở chung -->
                <div class="card border-top border-4 border-info">
                    <div class="card-header text-info fs-5 text-dark"><i class="bi bi-people-fill"></i> THÀNH VIÊN Ở GHÉP</div>
                    <div class="card-body">
                        <?php if(count($members) > 0): ?>
                            <ul class="list-group list-group-flush">
                                <?php foreach($members as $m): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <div><i class="bi bi-person text-muted me-2"></i> <strong><?= htmlspecialchars($m['fullname']) ?></strong></div>
                                        <span class="text-muted"><i class="bi bi-telephone"></i> <?= htmlspecialchars($m['phone']) ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-muted fst-italic mb-0">Bạn chưa đăng ký người ở ghép nào trong hợp đồng.</p>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
