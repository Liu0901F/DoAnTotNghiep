<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Lập Hợp Đồng Mới</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .section-title { font-size: 1.1rem; font-weight: 600; color: #0d6efd; border-bottom: 2px solid #0d6efd; padding-bottom: 8px; margin-bottom: 20px; }
    </style>
</head>
<body class="bg-light">

<div class="container mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow border-0">
                <div class="card-header bg-success text-white p-3 d-flex justify-content-between align-items-center">
                    <h5 class="m-0"><i class="bi bi-file-earmark-plus"></i> LẬP HỢP ĐỒNG THUÊ PHÒNG</h5>
                    <a href="index.php?controller=contract&action=index" class="btn btn-light btn-sm fw-bold">Trở về</a>
                </div>
                <div class="card-body p-4">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><i class="bi bi-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <form action="index.php?controller=contract&action=create" method="POST">
                        <!-- PHẦN 1: THÔNG TIN PHÒNG -->
                        <div class="section-title"><i class="bi bi-door-open"></i> 1. Thông Tin Phòng Thuê</div>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Chọn Phòng (Chỉ hiển thị phòng Trống) <span class="text-danger">*</span></label>
                                <select name="room_id" class="form-select" required id="roomSelect" onchange="updatePrice()">
                                    <option value="">-- Chọn phòng --</option>
                                    <?php foreach ($empty_rooms as $r): ?>
                                        <option value="<?= $r['id'] ?>" data-price="<?= $r['price'] ?>">
                                            Phòng <?= htmlspecialchars($r['room_number']) ?> - Giá: <?= number_format($r['price'], 0, ',', '.') ?>đ
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Giá chốt trên Hợp Đồng (VNĐ) <span class="text-danger">*</span></label>
                                <input type="number" name="rental_price" id="rentalPrice" class="form-control" required placeholder="Sẽ tự động điền khi chọn phòng">
                            </div>
                        </div>

                        <!-- PHẦN 2: THÔNG TIN KHÁCH THUÊ -->
                        <div class="section-title"><i class="bi bi-person-lines-fill"></i> 2. Thông Tin Khách Đại Diện Ký Hợp Đồng</div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Khách Thuê <span class="text-danger">*</span></label>
                                <select name="tenant_id" class="form-select" id="tenantSelect" onchange="toggleNewTenant()" required>
                                    <option value="new" class="text-success fw-bold">+ THÊM KHÁCH HÀNG MỚI</option>
                                    <?php foreach ($tenants as $t): ?>
                                        <option value="<?= $t['id'] ?>">
                                            <?= htmlspecialchars($t['fullname']) ?> (CCCD: <?= htmlspecialchars($t['id_card']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- FORM NHẬP KHÁCH MỚI -->
                        <div id="newTenantForm" class="p-3 bg-white border rounded mb-4 shadow-sm">
                            <h6 class="text-muted fw-bold mb-3">Nhập thông tin khách mới:</h6>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label small">Họ và Tên</label>
                                    <input type="text" name="fullname" id="t_fullname" class="form-control" placeholder="Nguyễn Văn A">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">Số CCCD</label>
                                    <input type="text" name="id_card" id="t_idcard" class="form-control" placeholder="001200...">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">Số điện thoại</label>
                                    <input type="text" name="phone" id="t_phone" class="form-control">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <label class="form-label small">Quê quán / Thường trú</label>
                                    <input type="text" name="hometown" id="t_hometown" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">Ngày sinh</label>
                                    <input type="date" name="date_of_birth" id="t_dob" class="form-control">
                                </div>
                            </div>
                        </div>

                        <!-- PHẦN 3: THỜI GIAN & TIỀN CỌC -->
                        <div class="section-title"><i class="bi bi-calendar-check"></i> 3. Thời Gian & Đặt Cọc</div>
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Ngày bắt đầu <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" class="form-control" required value="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Ngày kết thúc <span class="text-danger">*</span></label>
                                <input type="date" name="end_date" class="form-control" required value="<?= date('Y-m-d', strtotime('+1 year')) ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Tiền cọc (VNĐ)</label>
                                <input type="number" name="deposit" class="form-control" value="0">
                            </div>
                        </div>

                        <div class="text-center mt-5">
                            <button type="submit" class="btn btn-success btn-lg px-5 fw-bold shadow">
                                <i class="bi bi-check2-circle"></i> TẠO HỢP ĐỒNG & BÀN GIAO PHÒNG
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Tự động điền giá phòng khi chọn phòng
function updatePrice() {
    var select = document.getElementById("roomSelect");
    var priceInput = document.getElementById("rentalPrice");
    if(select.selectedIndex > 0) {
        var price = select.options[select.selectedIndex].getAttribute("data-price");
        priceInput.value = price;
    } else {
        priceInput.value = "";
    }
}

// Ẩn/hiện form khách hàng mới
function toggleNewTenant() {
    var select = document.getElementById("tenantSelect");
    var form = document.getElementById("newTenantForm");
    var inputs = [
        document.getElementById("t_fullname"),
        document.getElementById("t_idcard")
    ];

    if (select.value === "new") {
        form.style.display = "block";
        inputs.forEach(i => i.setAttribute("required", "true"));
    } else {
        form.style.display = "none";
        inputs.forEach(i => i.removeAttribute("required"));
    }
}

// Chạy lần đầu lúc load
toggleNewTenant();
</script>
</body>
</html>
