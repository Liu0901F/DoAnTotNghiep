<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa Thông Tin Phòng - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-0">
                <div class="card-header bg-warning text-dark p-3 d-flex justify-content-between align-items-center">
                    <h5 class="m-0 fw-bold"><i class="bi bi-pencil-square"></i> Cập Nhật Phòng: <?= htmlspecialchars($room['room_number']) ?></h5>
                    <a href="index.php?controller=room&action=index" class="btn btn-light btn-sm border fw-bold">Trở về</a>
                </div>
                <div class="card-body p-4">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><i class="bi bi-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <form action="index.php?controller=room&action=edit&id=<?= $room['id'] ?>" method="POST">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Số/Mã Phòng <span class="text-danger">*</span></label>
                                <input type="text" name="room_number" class="form-control" required value="<?= htmlspecialchars($room['room_number']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Loại Phòng <span class="text-danger">*</span></label>
                                <select name="room_type_id" class="form-select" required>
                                    <?php foreach ($room_types as $rt): ?>
                                        <option value="<?= $rt['id'] ?>" <?= ($room['room_type_id'] == $rt['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($rt['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Giá Thuê (VNĐ) <span class="text-danger">*</span></label>
                                <input type="number" name="price" class="form-control" required value="<?= htmlspecialchars($room['price']) ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Số người tối đa</label>
                                <input type="number" name="max_occupants" class="form-control" value="<?= htmlspecialchars($room['max_occupants']) ?>" min="1">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Trạng thái</label>
                                <select name="status" class="form-select">
                                    <option value="empty" <?= ($room['status'] == 'empty') ? 'selected' : '' ?>>Phòng Trống</option>
                                    <option value="rented" <?= ($room['status'] == 'rented') ? 'selected' : '' ?>>Đang Thuê</option>
                                    <option value="maintenance" <?= ($room['status'] == 'maintenance') ? 'selected' : '' ?>>Đang Bảo Trì</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Mô tả / Ghi chú</label>
                            <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($room['description'] ?? '') ?></textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-warning py-2 fw-bold text-dark"><i class="bi bi-save"></i> LƯU THAY ĐỔI</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
