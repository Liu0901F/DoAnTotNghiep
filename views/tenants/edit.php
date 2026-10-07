<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa Thông Tin Khách</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark fw-bold">SỬA THÔNG TIN KHÁCH THUÊ</div>
                <div class="card-body">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <form action="index.php?controller=tenantManage&action=edit&id=<?= $tenant['id'] ?>" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Họ và Tên</label>
                            <input type="text" name="fullname" class="form-control" value="<?= htmlspecialchars($tenant['fullname']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">CCCD / CMND</label>
                            <input type="text" name="id_card" class="form-control" value="<?= htmlspecialchars($tenant['id_card']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Số điện thoại</label>
                            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($tenant['phone']) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Quê quán</label>
                            <input type="text" name="hometown" class="form-control" value="<?= htmlspecialchars($tenant['hometown']) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ngày sinh</label>
                            <input type="date" name="date_of_birth" class="form-control" value="<?= $tenant['date_of_birth'] ?>">
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="index.php?controller=tenantManage&action=index" class="btn btn-secondary">Hủy bỏ</a>
                            <button type="submit" class="btn btn-warning fw-bold">Lưu Thay Đổi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
