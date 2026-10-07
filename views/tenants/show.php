<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hồ Sơ Khách Thuê</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light pb-5">

<div class="container mt-4">
    <a href="index.php?controller=tenantManage&action=index" class="btn btn-secondary mb-3"><i class="bi bi-arrow-left"></i> Trở về</a>

    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 border-top border-4 border-info">
                <div class="card-body text-center">
                    <div class="display-1 text-muted mb-3"><i class="bi bi-person-badge"></i></div>
                    <h4 class="fw-bold"><?= htmlspecialchars($tenant['fullname']) ?></h4>
                    <p class="text-muted mb-4">Mã Khách: KH<?= str_pad($tenant['id'], 4, '0', STR_PAD_LEFT) ?></p>
                    <ul class="list-group list-group-flush text-start">
                        <li class="list-group-item"><strong>CCCD:</strong> <?= htmlspecialchars($tenant['id_card']) ?></li>
                        <li class="list-group-item"><strong>SĐT:</strong> <?= htmlspecialchars($tenant['phone']) ?></li>
                        <li class="list-group-item"><strong>Ngày Sinh:</strong> <?= date('d/m/Y', strtotime($tenant['date_of_birth'])) ?></li>
                        <li class="list-group-item"><strong>Quê quán:</strong> <?= htmlspecialchars($tenant['hometown']) ?></li>
                    </ul>
                    <a href="index.php?controller=tenantManage&action=edit&id=<?= $tenant['id'] ?>" class="btn btn-outline-warning w-100 mt-3">Sửa Thông Tin</a>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold"><i class="bi bi-clock-history text-primary"></i> Lịch Sử Thuê Phòng</div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Phòng</th>
                                <th>Ngày Bắt Đầu</th>
                                <th>Ngày Hết Hạn</th>
                                <th>Trạng Thái</th>
                                <th>Tiền Cọc</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($contracts) > 0): ?>
                                <?php foreach($contracts as $c): ?>
                                    <tr>
                                        <td><span class="badge bg-primary fs-6">P.<?= htmlspecialchars($c['room_number']) ?></span></td>
                                        <td><?= date('d/m/Y', strtotime($c['start_date'])) ?></td>
                                        <td><?= date('d/m/Y', strtotime($c['end_date'])) ?></td>
                                        <td>
                                            <?php if($c['status'] == 'active') echo '<span class="badge bg-success">Đang hiệu lực</span>';
                                                  elseif($c['status'] == 'expired') echo '<span class="badge bg-secondary">Đã hết hạn</span>';
                                                  else echo '<span class="badge bg-danger">Đã thanh lý</span>'; ?>
                                        </td>
                                        <td class="text-danger fw-bold"><?= number_format($c['deposit'], 0, ',', '.') ?>đ</td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-center text-muted py-4">Chưa có lịch sử hợp đồng.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
