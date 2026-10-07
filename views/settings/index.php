<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Cài Đặt Hệ Thống</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light pb-5">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-gear-fill"></i> CÀI ĐẶT HỆ THỐNG</a>
    <div class="d-flex align-items-center text-white">
        <a href="index.php?controller=home&action=index" class="btn btn-sm btn-outline-light"><i class="bi bi-arrow-left"></i> Về Dashboard</a>
    </div>
  </div>
</nav>

<div class="container">
    <?php if (!empty($success_msg)): ?>
        <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($success_msg) ?></div>
    <?php endif; ?>
    <?php if (!empty($error_msg)): ?>
        <div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($error_msg) ?></div>
    <?php endif; ?>

    <div class="row">
        <!-- Bảng giá dịch vụ -->
        <div class="col-lg-5 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-warning text-dark fw-bold">
                    <i class="bi bi-cash-stack"></i> Bảng Giá Dịch Vụ
                </div>
                <div class="card-body">
                    <form action="index.php?controller=setting&action=updateService" method="POST">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Dịch vụ</th>
                                    <th>Đơn giá (VNĐ)</th>
                                    <th>Đơn vị tính</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($services as $s): ?>
                                    <tr>
                                        <td class="fw-bold"><?= htmlspecialchars($s['name']) ?></td>
                                        <td>
                                            <input type="number" name="services[<?= $s['id'] ?>]" class="form-control" value="<?= htmlspecialchars($s['unit_price']) ?>" required>
                                        </td>
                                        <td><?= htmlspecialchars($s['unit_name']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <button type="submit" class="btn btn-warning w-100 fw-bold"><i class="bi bi-save"></i> LƯU BẢNG GIÁ MỚI</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Quản lý loại phòng -->
        <div class="col-lg-7">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-info text-white fw-bold">
                    <i class="bi bi-tags-fill"></i> Quản Lý Danh Mục Loại Phòng
                </div>
                <div class="card-body">
                    <!-- Form thêm/sửa -->
                    <form action="index.php?controller=setting&action=saveRoomType" method="POST" class="bg-light p-3 rounded mb-4 border">
                        <h6 class="fw-bold text-muted mb-3">THÊM / SỬA LOẠI PHÒNG</h6>
                        <input type="hidden" name="id" id="type_id" value="">
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Tên Loại Phòng</label>
                                <input type="text" name="name" id="type_name" class="form-control" required placeholder="VD: Studio">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Giá thuê cơ bản (VNĐ)</label>
                                <input type="number" name="base_price" id="type_price" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Mô tả</label>
                            <input type="text" name="description" id="type_desc" class="form-control">
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Lưu thông tin</button>
                            <button type="button" class="btn btn-secondary" onclick="resetForm()">Hủy sửa</button>
                        </div>
                    </form>

                    <!-- Danh sách -->
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Tên Loại</th>
                                    <th>Giá Cơ Bản</th>
                                    <th>Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($room_types as $rt): ?>
                                    <tr>
                                        <td><?= $rt['id'] ?></td>
                                        <td class="fw-bold text-primary"><?= htmlspecialchars($rt['name']) ?></td>
                                        <td class="text-danger fw-bold"><?= number_format($rt['base_price'], 0, ',', '.') ?>đ</td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-info" 
                                                onclick="editType(<?= $rt['id'] ?>, '<?= htmlspecialchars($rt['name']) ?>', <?= $rt['base_price'] ?>, '<?= htmlspecialchars($rt['description']) ?>')">
                                                Sửa
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function editType(id, name, price, desc) {
    document.getElementById('type_id').value = id;
    document.getElementById('type_name').value = name;
    document.getElementById('type_price').value = price;
    document.getElementById('type_desc').value = desc;
}
function resetForm() {
    document.getElementById('type_id').value = '';
    document.getElementById('type_name').value = '';
    document.getElementById('type_price').value = '';
    document.getElementById('type_desc').value = '';
}
</script>
</body>
</html>
