<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản Lý Phòng - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php?controller=home&action=index"><i class="bi bi-buildings"></i> ADMIN PANEL</a>
    <div class="d-flex align-items-center text-white">
        <a href="index.php?controller=home&action=index" class="text-white text-decoration-none me-4">Trang chủ</a>
        <a href="index.php?controller=room&action=index" class="text-white text-decoration-none fw-bold me-4 border-bottom pb-1">Phòng trọ</a>
        <a href="index.php?controller=auth&action=logout" class="btn btn-sm btn-danger"><i class="bi bi-box-arrow-right"></i> Đăng xuất</a>
    </div>
  </div>
</nav>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold m-0"><i class="bi bi-door-open-fill text-primary"></i> Quản Lý Danh Sách Phòng</h3>
        <a href="index.php?controller=room&action=create" class="btn btn-success shadow-sm">
            <i class="bi bi-plus-circle"></i> Thêm Phòng Mới
        </a>
    </div>

    <!-- Hiển thị thông báo (Flash Message) -->
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

    <!-- Filter Form -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form action="index.php" method="GET" class="row gx-3 gy-2 align-items-center">
                <input type="hidden" name="controller" value="room">
                <input type="hidden" name="action" value="index">
                <div class="col-sm-4">
                    <label class="visually-hidden">Trạng thái</label>
                    <select class="form-select" name="status">
                        <option value="">Tất cả trạng thái</option>
                        <option value="empty" <?= $currentStatus == 'empty' ? 'selected' : '' ?>>Trống</option>
                        <option value="rented" <?= $currentStatus == 'rented' ? 'selected' : '' ?>>Đang thuê</option>
                        <option value="maintenance" <?= $currentStatus == 'maintenance' ? 'selected' : '' ?>>Đang sửa chữa</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-funnel"></i> Lọc dữ liệu</button>
                    <a href="index.php?controller=room&action=index" class="btn btn-light border"><i class="bi bi-arrow-clockwise"></i> Bỏ lọc</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Danh Sách -->
    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Số phòng</th>
                        <th>Loại phòng</th>
                        <th>Giá thuê (VNĐ)</th>
                        <th>Tối đa</th>
                        <th>Trạng thái</th>
                        <th class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($rooms) > 0): ?>
                        <?php foreach($rooms as $index => $r): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><span class="fw-bold text-primary"><?= htmlspecialchars($r['room_number']) ?></span></td>
                                <td><?= htmlspecialchars($r['room_type_name']) ?></td>
                                <td class="fw-bold text-danger"><?= number_format($r['price'], 0, ',', '.') ?>đ</td>
                                <td><i class="bi bi-people"></i> <?= $r['max_occupants'] ?></td>
                                <td>
                                    <?php 
                                        if($r['status'] == 'empty') echo '<span class="badge bg-success">Phòng Trống</span>';
                                        elseif($r['status'] == 'rented') echo '<span class="badge bg-info text-dark">Đang Thuê</span>';
                                        else echo '<span class="badge bg-warning text-dark">Bảo Trì</span>';
                                    ?>
                                </td>
                                <td class="text-center">
                                    <a href="index.php?controller=room&action=edit&id=<?= $r['id'] ?>" class="btn btn-sm btn-outline-primary" title="Sửa">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="index.php?controller=room&action=delete" method="POST" class="d-inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa phòng <?= htmlspecialchars($r['room_number']) ?> này không?');">
                                        <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center py-4 text-muted">Không tìm thấy phòng nào phù hợp!</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<br><br>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
