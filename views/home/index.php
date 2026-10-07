<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Quản Lý Phòng Cho Thuê</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background-color: #f4f6f9; }
        .kpi-card { border: none; border-radius: 10px; box-shadow: 0 0 15px rgba(0,0,0,0.03); transition: transform 0.2s;}
        .kpi-card:hover { transform: translateY(-5px); }
        .icon-box { width: 50px; height: 50px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 24px; color: white;}
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
  <div class="container-fluid px-4">
    <a class="navbar-brand fw-bold" href="index.php?controller=home&action=index"><i class="bi bi-buildings"></i> ADMIN DASHBOARD</a>
    <div class="d-flex align-items-center text-white ms-auto">
        <span class="me-4"><i class="bi bi-person-circle"></i> Xin chào, <b><?= htmlspecialchars($user['fullname']) ?></b></span>
        <a href="index.php?controller=auth&action=logout" class="btn btn-sm btn-danger"><i class="bi bi-box-arrow-right"></i> Đăng xuất</a>
    </div>
  </div>
</nav>

<div class="container-fluid px-4 mb-5">
    
    <!-- HÀNG 1: Menu Điều Hướng Nhanh -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="bg-white p-3 rounded shadow-sm d-flex flex-wrap gap-2">
                <a href="index.php?controller=room&action=index" class="btn btn-primary shadow-sm"><i class="bi bi-door-open"></i> Phòng Trọ</a>
                <a href="index.php?controller=tenantManage&action=index" class="btn btn-info text-white shadow-sm"><i class="bi bi-people-fill"></i> Hồ Sơ Khách</a>
                <a href="index.php?controller=contract&action=index" class="btn btn-success shadow-sm"><i class="bi bi-file-earmark-text"></i> Hợp Đồng</a>
                <a href="index.php?controller=invoice&action=index" class="btn btn-warning text-dark shadow-sm fw-bold"><i class="bi bi-receipt-cutoff"></i> Điện Nước & Hóa Đơn</a>
                <a href="index.php?controller=maintenance&action=index" class="btn btn-danger shadow-sm"><i class="bi bi-tools"></i> Sự Cố / Báo Hỏng</a>
                <a href="index.php?controller=setting&action=index" class="btn btn-secondary shadow-sm ms-auto"><i class="bi bi-gear-fill"></i> Cài Đặt Hệ Thống</a>
            </div>
        </div>
    </div>

    <!-- HÀNG 2: 4 Thẻ KPI Thống Kê -->
    <div class="row mb-4">
        <!-- Tổng Phòng -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card kpi-card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="icon-box bg-primary shadow-sm me-3"><i class="bi bi-house-door"></i></div>
                    <div>
                        <div class="text-muted fw-bold small text-uppercase mb-1">Tổng Số Phòng</div>
                        <div class="h3 mb-0 fw-bold text-dark"><?= $stats['total'] ?></div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tỷ lệ lấp đầy -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card kpi-card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="icon-box bg-success shadow-sm me-3"><i class="bi bi-pie-chart"></i></div>
                    <div>
                        <div class="text-muted fw-bold small text-uppercase mb-1">Tỷ lệ lấp đầy</div>
                        <div class="h3 mb-0 fw-bold text-dark"><?= $stats['percent'] ?>%</div>
                        <small class="text-success"><?= $stats['rented'] ?> phòng đang thuê</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Doanh thu tháng này -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card kpi-card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="icon-box bg-info shadow-sm me-3"><i class="bi bi-cash-stack"></i></div>
                    <div>
                        <div class="text-muted fw-bold small text-uppercase mb-1">Doanh thu (T<?= date('m/Y') ?>)</div>
                        <div class="h4 mb-0 fw-bold text-dark"><?= number_format($revenue, 0, ',', '.') ?> đ</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tổng Nợ -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card kpi-card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="icon-box bg-danger shadow-sm me-3"><i class="bi bi-exclamation-triangle"></i></div>
                    <div>
                        <div class="text-muted fw-bold small text-uppercase mb-1">Khách Còn Nợ</div>
                        <div class="h4 mb-0 fw-bold text-danger"><?= number_format($debt, 0, ',', '.') ?> đ</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- HÀNG 3: Biểu Đồ Thống Kê (Chart.js) -->
    <div class="row mb-4">
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white fw-bold"><i class="bi bi-pie-chart-fill text-primary"></i> Trạng Thái Phòng</div>
                <div class="card-body d-flex justify-content-center align-items-center">
                    <canvas id="statusChart" style="max-height: 250px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white fw-bold"><i class="bi bi-bar-chart-line-fill text-success"></i> Doanh Thu 6 Tháng Gần Nhất</div>
                <div class="card-body">
                    <canvas id="revenueChart" style="max-height: 250px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- HÀNG 4: Danh Sách Nhắc Nhở -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold text-danger"><i class="bi bi-bell-fill"></i> Hợp Đồng Sắp Hết Hạn (Trong 30 ngày)</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Mã HĐ</th>
                                    <th>Phòng</th>
                                    <th>Khách Thuê</th>
                                    <th>SĐT</th>
                                    <th>Ngày Ký</th>
                                    <th>Ngày Hết Hạn</th>
                                    <th>Trạng Thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count($expiring) > 0): ?>
                                    <?php foreach($expiring as $c): 
                                        $daysLeft = (strtotime($c['end_date']) - time()) / (60 * 60 * 24);
                                    ?>
                                        <tr>
                                            <td>#<?= $c['id'] ?></td>
                                            <td><span class="badge bg-primary">P.<?= htmlspecialchars($c['room_number']) ?></span></td>
                                            <td><?= htmlspecialchars($c['tenant_name']) ?></td>
                                            <td><?= htmlspecialchars($c['phone']) ?></td>
                                            <td><?= date('d/m/Y', strtotime($c['start_date'])) ?></td>
                                            <td class="text-danger fw-bold"><?= date('d/m/Y', strtotime($c['end_date'])) ?></td>
                                            <td>
                                                <?php if($daysLeft < 0): ?>
                                                    <span class="badge bg-danger">Đã quá hạn</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning text-dark">Còn <?= round($daysLeft) ?> ngày</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="7" class="text-center py-3 text-muted">Không có hợp đồng nào sắp hết hạn.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- INIT CHART.JS -->
<script>
    // Data từ PHP
    const chartRoomData = <?= $chartRoom ?>;
    const chartRevData = <?= $chartRev ?>;

    // 1. Biểu đồ Doughnut (Trạng thái phòng)
    const ctx1 = document.getElementById('statusChart').getContext('2d');
    new Chart(ctx1, {
        type: 'doughnut',
        data: {
            labels: ['Trống', 'Đang Thuê', 'Bảo Trì'],
            datasets: [{
                data: [chartRoomData.empty, chartRoomData.rented, chartRoomData.maintenance],
                backgroundColor: ['#28a745', '#007bff', '#ffc107'],
                borderWidth: 0
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    // 2. Biểu đồ Bar (Doanh thu)
    const ctx2 = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: chartRevData.labels,
            datasets: [{
                label: 'Doanh Thu Thực Thu (VNĐ)',
                data: chartRevData.data,
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: { 
            responsive: true, 
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
