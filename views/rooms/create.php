<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Phòng Mới - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white p-3 d-flex justify-content-between align-items-center">
                    <h5 class="m-0"><i class="bi bi-plus-circle"></i> Thêm Phòng Trọ Mới</h5>
                    <a href="index.php?controller=room&action=index" class="btn btn-light btn-sm fw-bold">Trở về</a>
                </div>
                <div class="card-body p-4">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><i class="bi bi-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <form action="index.php?controller=room&action=create" method="POST">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Số/Mã Phòng <span class="text-danger">*</span></label>
                                <input type="text" name="room_number" class="form-control" placeholder="VD: P103" required 
                                    value="<?= isset($old_data['room_number']) ? htmlspecialchars($old_data['room_number']) : '' ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Loại Phòng <span class="text-danger">*</span></label>
                                <select name="room_type_id" class="form-select" required>
                                    <option value="">-- Chọn loại phòng --</option>
                                    <?php foreach ($room_types as $rt): ?>
                                        <option value="<?= $rt['id'] ?>" <?= (isset($old_data['room_type_id']) && $old_data['room_type_id'] == $rt['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($rt['name']) ?> (<?= number_format($rt['base_price'], 0, ',', '.') ?>đ)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Giá Thuê (VNĐ) <span class="text-danger">*</span></label>
                                <input type="number" name="price" class="form-control" required
                                    value="<?= isset($old_data['price']) ? htmlspecialchars($old_data['price']) : '' ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Số người tối đa</label>
                                <input type="number" name="max_occupants" class="form-control" value="<?= isset($old_data['max_occupants']) ? htmlspecialchars($old_data['max_occupants']) : '2' ?>" min="1">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Trạng thái</label>
                                <select name="status" class="form-select">
                                    <option value="empty" <?= (isset($old_data['status']) && $old_data['status'] == 'empty') ? 'selected' : '' ?>>Phòng Trống</option>
                                    <option value="maintenance" <?= (isset($old_data['status']) && $old_data['status'] == 'maintenance') ? 'selected' : '' ?>>Đang Bảo Trì</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Mô tả / Ghi chú</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Đặc điểm, nội thất..."><?= isset($old_data['description']) ? htmlspecialchars($old_data['description']) : '' ?></textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary py-2 fw-bold"><i class="bi bi-save"></i> LƯU THÔNG TIN PHÒNG</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
