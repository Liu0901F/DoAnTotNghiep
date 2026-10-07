<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản Lý Khách Thuê</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light pb-5">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-people-fill"></i> QUẢN LÝ KHÁCH THUÊ</a>
    <div class="d-flex align-items-center text-white">
        <a href="index.php?controller=home&action=index" class="btn btn-sm btn-outline-light"><i class="bi bi-arrow-left"></i> Về Dashboard</a>
    </div>
  </div>
</nav>

<div class="container">
    <?php if (!empty($success_msg)): ?>
        <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($success_msg) ?></div>
    <?php endif; ?>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="fw-bold m-0"><i class="bi bi-list-task text-primary"></i> Danh sách tất cả khách thuê</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Họ và Tên</th>
                            <th>CCCD/CMND</th>
                            <th>Số điện thoại</th>
                            <th>Quê quán</th>
                            <th class="text-center">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($tenants as $t): ?>
                            <tr>
                                <td><?= $t['id'] ?></td>
                                <td class="fw-bold"><?= htmlspecialchars($t['fullname']) ?></td>
                                <td><?= htmlspecialchars($t['id_card']) ?></td>
                                <td><?= htmlspecialchars($t['phone']) ?></td>
                                <td><?= htmlspecialchars($t['hometown']) ?></td>
                                <td class="text-center">
                                    <a href="index.php?controller=tenantManage&action=show&id=<?= $t['id'] ?>" class="btn btn-sm btn-info text-white"><i class="bi bi-eye"></i> Hồ Sơ</a>
                                    <a href="index.php?controller=tenantManage&action=edit&id=<?= $t['id'] ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil-square"></i> Sửa</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>
