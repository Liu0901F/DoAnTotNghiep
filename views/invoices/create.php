<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chốt Điện Nước & Lập Hóa Đơn</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<div class="container mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white p-3 d-flex justify-content-between align-items-center">
                    <h5 class="m-0"><i class="bi bi-calculator"></i> CHỐT ĐIỆN NƯỚC & LẬP HÓA ĐƠN THÁNG</h5>
                    <a href="index.php?controller=invoice&action=index" class="btn btn-light btn-sm fw-bold">Trở về</a>
                </div>
                <div class="card-body p-4">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><i class="bi bi-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <form action="index.php?controller=invoice&action=create" method="POST" id="invoiceForm">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Chọn Phòng đang thuê <span class="text-danger">*</span></label>
                                <select name="contract_id" id="contractSelect" class="form-select fw-bold" required onchange="fillMeterData()">
                                    <option value="">-- Chọn hợp đồng / Phòng --</option>
                                    <?php foreach ($contracts as $c): ?>
                                        <option value="<?= $c['contract_id'] ?>">
                                            Phòng <?= htmlspecialchars($c['room_number']) ?> (Giá: <?= number_format($c['rental_price'], 0, ',', '.') ?>đ)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Kỳ Hóa Đơn (Tháng) <span class="text-danger">*</span></label>
                                <input type="number" name="month" class="form-control" value="<?= date('m') ?>" min="1" max="12" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Năm <span class="text-danger">*</span></label>
                                <input type="number" name="year" class="form-control" value="<?= date('Y') ?>" required>
                            </div>
                        </div>

                        <!-- KHU VỰC NHẬP CHỈ SỐ -->
                        <div class="row g-4 mb-4">
                            <!-- Điện -->
                            <div class="col-md-6">
                                <div class="card border-warning">
                                    <div class="card-header bg-warning text-dark fw-bold"><i class="bi bi-lightning-charge-fill"></i> Chỉ số ĐIỆN</div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">Số cũ (Tự động lấy)</label>
                                            <input type="number" id="old_elec" class="form-control bg-light" readonly tabindex="-1">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label fw-bold text-danger">Số điện MỚI <span class="text-danger">*</span></label>
                                            <input type="number" name="new_electricity" id="new_elec" class="form-control" required onkeyup="calcLive()">
                                        </div>
                                        <small class="text-muted">Đơn giá: <?= number_format($services['Điện'] ?? 3500) ?> đ/kWh</small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Nước -->
                            <div class="col-md-6">
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white fw-bold"><i class="bi bi-droplet-fill"></i> Chỉ số NƯỚC</div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">Số cũ (Tự động lấy)</label>
                                            <input type="number" id="old_water" class="form-control bg-light" readonly tabindex="-1">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label fw-bold text-danger">Số nước MỚI <span class="text-danger">*</span></label>
                                            <input type="number" name="new_water" id="new_water" class="form-control" required onkeyup="calcLive()">
                                        </div>
                                        <small class="text-muted">Đơn giá: <?= number_format($services['Nước'] ?? 20000) ?> đ/khối</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- LIVE PREVIEW TỔNG TIỀN -->
                        <div class="alert alert-secondary text-end">
                            <h5 class="mb-0">Dự kiến Tổng Hóa Đơn: <span id="liveTotal" class="text-danger fw-bold fs-3">0</span> <small>VNĐ</small></h5>
                            <small class="text-muted">(Tiền phòng + Tiền điện + Tiền nước + Wifi + Rác)</small>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary btn-lg px-5 fw-bold shadow">
                                <i class="bi bi-receipt"></i> TẠO HÓA ĐƠN
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Nhúng data PHP sang mảng JavaScript
const contractsData = <?= json_encode($contracts) ?>;
const priceElec = <?= $services['Điện'] ?? 3500 ?>;
const priceWater = <?= $services['Nước'] ?? 20000 ?>;
const otherServices = <?= ($services['Wifi'] ?? 100000) + ($services['Rác'] ?? 30000) ?>;

function fillMeterData() {
    const select = document.getElementById('contractSelect');
    const selectedId = select.value;
    
    // Tìm Hợp đồng được chọn trong mảng JS
    const contract = contractsData.find(c => c.contract_id == selectedId);
    
    if(contract) {
        document.getElementById('old_elec').value = contract.old_electricity;
        document.getElementById('old_water').value = contract.old_water;
        
        // Reset ô nhập mới về rỗng để bắt người dùng gõ
        document.getElementById('new_elec').value = '';
        document.getElementById('new_water').value = '';
        
        calcLive(); // Tính lại
    }
}

function calcLive() {
    const select = document.getElementById('contractSelect');
    if(!select.value) return;

    const contract = contractsData.find(c => c.contract_id == select.value);
    const oldElec = parseInt(document.getElementById('old_elec').value) || 0;
    const oldWater = parseInt(document.getElementById('old_water').value) || 0;
    
    const newElec = parseInt(document.getElementById('new_elec').value) || 0;
    const newWater = parseInt(document.getElementById('new_water').value) || 0;

    let elecTotal = 0;
    let waterTotal = 0;

    if(newElec >= oldElec) elecTotal = (newElec - oldElec) * priceElec;
    if(newWater >= oldWater) waterTotal = (newWater - oldWater) * priceWater;

    const grandTotal = parseFloat(contract.rental_price) + elecTotal + waterTotal + otherServices;
    
    // Format VNĐ
    document.getElementById('liveTotal').innerText = grandTotal.toLocaleString('vi-VN');
}
</script>
</body>
</html>
