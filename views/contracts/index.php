<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản Lý Hợp Đồng - Admin</title>
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
        <a href="index.php?controller=contract&action=index" class="text-white text-decoration-none fw-bold border-bottom pb-1 me-4">Hợp đồng</a>
        <a href="index.php?controller=auth&action=logout" class="btn btn-sm btn-danger"><i class="bi bi-box-arrow-right"></i> Đăng xuất</a>
    </div>
  </div>
</nav>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold m-0"><i class="bi bi-file-earmark-text-fill text-primary"></i> Danh Sách Hợp Đồng</h3>
        <a href="index.php?controller=contract&action=create" class="btn btn-success shadow-sm">
            <i class="bi bi-plus-circle"></i> Lập Hợp Đồng Mới
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
                <input type="hidden" name="controller" value="contract">
                <input type="hidden" name="action" value="index">
                <div class="col-sm-4">
                    <select class="form-select" name="status">
                        <option value="">Tất cả trạng thái</option>
                        <option value="active" <?= $currentStatus == 'active' ? 'selected' : '' ?>>Đang có hiệu lực</option>
                        <option value="terminated" <?= $currentStatus == 'terminated' ? 'selected' : '' ?>>Đã thanh lý</option>
                        <option value="expired" <?= $currentStatus == 'expired' ? 'selected' : '' ?>>Hết hạn</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-funnel"></i> Lọc dữ liệu</button>
                    <a href="index.php?controller=contract&action=index" class="btn btn-light border"><i class="bi bi-arrow-clockwise"></i> Bỏ lọc</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Bảng Dữ Liệu -->
    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Mã HĐ</th>
                        <th>Phòng</th>
                        <th>Người đại diện (Khách thuê)</th>
                        <th>Thời gian thuê</th>
                        <th>Giá chốt</th>
                        <th>Trạng thái</th>
                        <th class="text-center">Chi tiết</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($contracts) > 0): ?>
                        <?php foreach($contracts as $c): ?>
                            <tr>
                                <td>#<?= $c['id'] ?></td>
                                <td><span class="badge bg-primary fs-6"><?= htmlspecialchars($c['room_number']) ?></span></td>
                                <td>
                                    <strong><?= htmlspecialchars($c['tenant_name']) ?></strong><br>
                                    <small class="text-muted"><i class="bi bi-telephone"></i> <?= htmlspecialchars($c['phone']) ?></small>
                                </td>
                                <td>
                                    <?= date('d/m/Y', strtotime($c['start_date'])) ?> 
                                    <i class="bi bi-arrow-right text-muted"></i> 
                                    <?= date('d/m/Y', strtotime($c['end_date'])) ?>
                                </td>
                                <td class="fw-bold text-danger"><?= number_format($c['rental_price'], 0, ',', '.') ?>đ</td>
                                <td>
                                    <?php 
                                        if($c['status'] == 'active') echo '<span class="badge bg-success">Hiệu lực</span>';
                                        elseif($c['status'] == 'terminated') echo '<span class="badge bg-secondary">Đã thanh lý</span>';
                                        else echo '<span class="badge bg-warning text-dark">Hết hạn</span>';
                                    ?>
                                </td>
                                <td class="text-center">
                                    <a href="index.php?controller=contract&action=show&id=<?= $c['id'] ?>" class="btn btn-sm btn-info text-white shadow-sm" title="Xem chi tiết">
                                        <i class="bi bi-eye"></i> Xem
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center py-4 text-muted">Không có dữ liệu hợp đồng!</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
