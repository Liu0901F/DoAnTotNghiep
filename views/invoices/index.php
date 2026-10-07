<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản Lý Hóa Đơn - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php?controller=home&action=index"><i class="bi bi-buildings"></i> ADMIN PANEL</a>
    <div class="d-flex align-items-center text-white">
        <a href="index.php?controller=home&action=index" class="text-white text-decoration-none me-4">Trang chủ</a>
        <a href="index.php?controller=room&action=index" class="text-white text-decoration-none me-4">Phòng trọ</a>
        <a href="index.php?controller=contract&action=index" class="text-white text-decoration-none me-4">Hợp đồng</a>
        <a href="index.php?controller=invoice&action=index" class="text-white text-decoration-none fw-bold border-bottom pb-1 me-4">Hóa đơn</a>
        <a href="index.php?controller=auth&action=logout" class="btn btn-sm btn-danger"><i class="bi bi-box-arrow-right"></i> Đăng xuất</a>
    </div>
  </div>
</nav>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold m-0"><i class="bi bi-receipt text-primary"></i> Danh Sách Hóa Đơn Tiền Phòng</h3>
        <a href="index.php?controller=invoice&action=create" class="btn btn-success shadow-sm">
            <i class="bi bi-plus-circle"></i> Chốt Điện Nước & Lập HĐ
        </a>
    </div>

    <!-- Thông báo -->
    <?php if (!empty($success_msg)): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($success_msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (!empty($error_msg)): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($error_msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Bộ Lọc -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form action="index.php" method="GET" class="row gx-3 gy-2 align-items-center">
                <input type="hidden" name="controller" value="invoice">
                <input type="hidden" name="action" value="index">
                
                <div class="col-sm-2">
                    <select class="form-select" name="month">
                        <option value="">Chọn Tháng</option>
                        <?php for($m=1; $m<=12; $m++): ?>
                            <option value="<?= $m ?>" <?= $currentMonth == $m ? 'selected' : '' ?>>Tháng <?= $m ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-sm-2">
                    <input type="number" name="year" class="form-control" value="<?= htmlspecialchars($currentYear) ?>" placeholder="Năm">
                </div>
                <div class="col-sm-3">
                    <select class="form-select" name="status">
                        <option value="">Tất cả trạng thái nợ</option>
                        <option value="unpaid" <?= $currentStatus == 'unpaid' ? 'selected' : '' ?>>Chưa thanh toán</option>
                        <option value="partially_paid" <?= $currentStatus == 'partially_paid' ? 'selected' : '' ?>>Còn nợ một phần</option>
                        <option value="paid" <?= $currentStatus == 'paid' ? 'selected' : '' ?>>Đã thanh toán đủ</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Lọc</button>
                    <a href="index.php?controller=invoice&action=index&month=&year=&status=" class="btn btn-light border">Tất cả</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Bảng Dữ Liệu Hóa Đơn -->
    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Kỳ HĐ</th>
                        <th>Phòng</th>
                        <th>Khách Thuê</th>
                        <th>Tổng Tiền</th>
                        <th>Đã Đóng</th>
                        <th>Còn Nợ</th>
                        <th>Trạng thái</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($invoices) > 0): ?>
                        <?php foreach($invoices as $i): 
                            $debt = $i['total_amount'] - $i['paid_amount'];
                        ?>
                            <tr>
                                <td><span class="fw-bold">Tháng <?= $i['month'] ?>/<?= $i['year'] ?></span></td>
                                <td><span class="badge bg-primary fs-6"><?= htmlspecialchars($i['room_number']) ?></span></td>
                                <td><?= htmlspecialchars($i['tenant_name']) ?></td>
                                <td class="fw-bold"><?= number_format($i['total_amount'], 0, ',', '.') ?>đ</td>
                                <td class="text-success fw-bold"><?= number_format($i['paid_amount'], 0, ',', '.') ?>đ</td>
                                <td class="text-danger fw-bold"><?= number_format($debt, 0, ',', '.') ?>đ</td>
                                <td>
                                    <?php 
                                        if($i['status'] == 'paid') echo '<span class="badge bg-success">Đã thu đủ</span>';
                                        elseif($i['status'] == 'partially_paid') echo '<span class="badge bg-warning text-dark">Thu 1 phần</span>';
                                        else echo '<span class="badge bg-danger">Chưa nộp</span>';
                                    ?>
                                </td>
                                <td class="text-center">
                                    <a href="index.php?controller=invoice&action=show&id=<?= $i['id'] ?>" class="btn btn-sm btn-info text-white shadow-sm" title="Xem Phiếu Thu">
                                        <i class="bi bi-receipt-cutoff"></i> Xem / Đóng Tiền
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8" class="text-center py-4 text-muted">Không có hóa đơn nào cho kỳ này.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
