<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản Lý Xử Lý Sự Cố</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light pb-5">

<nav class="navbar navbar-expand-lg navbar-dark bg-danger shadow-sm mb-4">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-tools"></i> QUẢN LÝ SỰ CỐ / BẢO TRÍ</a>
    <div class="d-flex align-items-center text-white">
        <a href="index.php?controller=home&action=index" class="btn btn-sm btn-outline-light"><i class="bi bi-arrow-left"></i> Về Dashboard</a>
    </div>
  </div>
</nav>

<div class="container">
    <?php if (!empty($success_msg)): ?>
        <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($success_msg) ?></div>
    <?php endif; ?>

    <div class="card shadow-sm border-0 border-top border-danger border-4">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold m-0"><i class="bi bi-cone-striped text-danger"></i> Danh Sách Yêu Cầu Hỗ Trợ Từ Khách Thuê</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Ngày Gửi</th>
                            <th>Phòng</th>
                            <th>Người Báo</th>
                            <th>Vấn Đề</th>
                            <th>Trạng Thái</th>
                            <th class="text-end">Cập Nhật Trạng Thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($requests) > 0): ?>
                            <?php foreach($requests as $r): ?>
                                <tr>
                                    <td><?= date('d/m/Y H:i', strtotime($r['created_at'])) ?></td>
                                    <td><span class="badge bg-dark fs-6">P.<?= htmlspecialchars($r['room_number']) ?></span></td>
                                    <td>
                                        <div class="fw-bold"><?= htmlspecialchars($r['tenant_name']) ?></div>
                                        <small class="text-muted"><?= htmlspecialchars($r['tenant_phone']) ?></small>
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($r['title']) ?></strong>
                                        <div class="text-muted small"><?= htmlspecialchars($r['description']) ?></div>
                                    </td>
                                    <td>
                                        <?php 
                                            if($r['status'] == 'resolved') echo '<span class="badge bg-success">Đã Xong</span>';
                                            elseif($r['status'] == 'in_progress') echo '<span class="badge bg-primary">Đang Sửa</span>';
                                            else echo '<span class="badge bg-warning text-dark">Chờ Xử Lý</span>';
                                        ?>
                                    </td>
                                    <td class="text-end">
                                        <form action="index.php?controller=maintenance&action=updateStatus" method="POST" class="d-flex justify-content-end gap-2">
                                            <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                            <select name="status" class="form-select form-select-sm w-auto">
                                                <option value="pending" <?= $r['status']=='pending'?'selected':'' ?>>Chờ Xử Lý</option>
                                                <option value="in_progress" <?= $r['status']=='in_progress'?'selected':'' ?>>Đang Sửa</option>
                                                <option value="resolved" <?= $r['status']=='resolved'?'selected':'' ?>>Hoàn Thành</option>
                                            </select>
                                            <button type="submit" class="btn btn-sm btn-success"><i class="bi bi-check2"></i> Lưu</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center py-4 text-muted">Tuyệt vời, không có sự cố nào đang chờ xử lý!</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>
